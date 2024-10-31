<?php
class PurpleSaladMenuItem {
	// custom post type Menu Item	
	static function register_item_type() {		
		$item_slug = get_option('purplesalad_item_slug');
	   if(empty($item_slug)) $item_slug = 'purple-item';
	  	   
		$args=array(
			"label" => __("PurpleSalad Menu Items", 'purplesalad'),
			"labels" => array
				(
					"name"=>__("Menu Items", 'purplesalad'), 
					"singular_name"=>__("Menu Item", 'purplesalad'),
					"add_new_item"=>__("Add New Menu Item", 'purplesalad')
				),
			"public"=> true,
			"show_ui"=>true,
			"has_archive"=>true,
			"rewrite"=> array("slug"=>$item_slug, "with_front"=>false),
			"description"=>__("This will create a new item for your menus.",'purplesalad'),
			"supports"=>array("title", 'editor', 'thumbnail', 'excerpt'),
			"taxonomies"=>array("category"),
			"show_in_nav_menus" => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => 'purplesalad_options',
			"register_meta_box_cb" => array(__CLASS__,"meta_boxes")
		);
		register_post_type( 'purplesalad_item', $args );
		register_taxonomy_for_object_type('category', 'purplesalad_item');
	}
	
	static function meta_boxes() {
		add_meta_box("purplesalad_meta", __("PurpleSalad Settings", 'purplesalad'), 
							array(__CLASS__, "print_meta_box"), "purplesalad_item", 'normal', 'high');
	}
	
	static function print_meta_box($post) {
		global $wpdb;
		
		// select menu layouts
		$layouts = $wpdb->get_results("SELECT * FROM ".PURPLESALAD_LAYOUTS." WHERE layout_type='menu_item' ORDER BY name");
		
		// get current layout id and price
		if(!empty($post->ID)) {
			$layout_id = get_post_meta($post->ID, 'purplesalad_layout_id', true);
			$page_layout_id = get_post_meta($post->ID, 'purplesalad_page_layout_id', true);
			$item_price = get_post_meta($post->ID, 'purplesalad_item_price', true);
			$menu_ids = get_post_meta($post->ID, 'purplesalad_menu_ids', true);
		}
		
		// select all menus so we can assign to them
		$menus = get_posts(array("post_type" => 'purplesalad_menu', 'orderby'=>'post_title', 'posts_per_page'=>'-1'));
		
		include(PURPLESALAD_PATH."/views/menu-item-meta-box.html.php");
	}
	
	static function save_menu_item_meta($post_id) {	
		global $wpdb;
				
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return;		
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	  	if ('purplesalad_item' != @$_POST['post_type']) return;	  
	  	update_post_meta($post_id, 'purplesalad_layout_id', $_POST['purplesalad_layout']);
	  	update_post_meta($post_id, 'purplesalad_page_layout_id', $_POST['purplesalad_page_layout']);
	  	update_post_meta($post_id, 'purplesalad_item_price', $_POST['purplesalad_price']);
	  	update_post_meta($post_id, 'purplesalad_menu_ids', '|'.@implode('|',@$_POST['purplesalad_menu_ids']).'|');
	}
	
	static function content_filter($content) {
		global $post;
		if(@$post->post_type == 'purplesalad_item') {			
			$content = self :: apply_layout($post, $content);
		}	
		return $content;
	}	
	
	// apply the layout to the content
	// $is_layout is used when passing already prepared layout from the menu model
	static function apply_layout($post, $content, $is_layout = false) {
		global $wpdb;
		
		$layout_id = $is_layout ? get_post_meta($post->ID, 'purplesalad_layout_id', true) : get_post_meta($post->ID, 'purplesalad_page_layout_id', true);
		$layout_html = $wpdb->get_var($wpdb->prepare("SELECT html FROM ".PURPLESALAD_LAYOUTS." WHERE id=%d", $layout_id));
		
		if($is_layout) $content = str_replace('{{{item}}}', stripslashes($layout_html), $content);
		else $content = $layout_html;
		
		$price = get_post_meta($post->ID, 'purplesalad_item_price', true);
		if(PURPLESALAD_NO_DECIMALS == '') $price = number_format($price, 2); 
						
		$content = str_replace('{{{item-title}}}', stripslashes($post->post_title), $content);
		$content = str_replace('{{{item-description}}}', wpautop(stripslashes($post->post_content)), $content);
		if(strstr($content, '{{{item-price}}}')) {
			$content = str_replace('{{{item-price}}}', $price, $content);
		}
		
		// featured image?
		if(strstr($content, '{{{item-image}}}')) {
			$image = get_the_post_thumbnail( $post->ID );
			$content = str_replace('{{{item-image}}}', $image, $content);
		}
		
		// item URL?
		if(strstr($content, '{{{item-url}}}')) {
			$permalink = get_permalink($post->ID);
			$content = str_replace('{{{item-url}}}', $permalink, $content);
		}
		
		$content = apply_filters('purplesalad_content', $content);
		return $content;
	} // end apply_layout()
}