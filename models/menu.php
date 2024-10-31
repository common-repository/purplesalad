<?php
class PurpleSaladMenu {
	// custom post type Course	
	static function register_menu_type() {		
		$menu_slug = get_option('purplesalad_menu_slug');
	   if(empty($menu_slug)) $menu_slug = 'purple-menu';
	  	   
		$args=array(
			"label" => __("PurpleSalad Menus", 'purplesalad'),
			"labels" => array
				(
					"name"=>__("Menus", 'purplesalad'), 
					"singular_name"=>__("Menu", 'purplesalad'),
					"add_new_item"=>__("Add New Menu", 'purplesalad')
				),
			"public"=> true,
			"show_ui"=>true,
			"has_archive"=>true,
			"rewrite"=> array("slug"=>$menu_slug, "with_front"=>false),
			"description"=>__("This will create a new restaurant menu.",'purplesalad'),
			"supports"=>array("title", 'editor', 'thumbnail', 'excerpt'),
			"taxonomies"=>array("category"),
			"show_in_nav_menus" => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => 'purplesalad_options',
			"register_meta_box_cb" => array(__CLASS__,"meta_boxes")
		);
		register_post_type( 'purplesalad_menu', $args );
		register_taxonomy_for_object_type('category', 'purplesalad_menu');
	}
	
	static function meta_boxes() {
		add_meta_box("purplesalad_meta", __("PurpleSalad Settings", 'purplesalad'), 
							array(__CLASS__, "print_meta_box"), "purplesalad_menu", 'normal', 'high');
	}
	
	static function print_meta_box($post) {
		global $wpdb;
		
		// select menu layouts
		$layouts = $wpdb->get_results("SELECT * FROM ".PURPLESALAD_LAYOUTS." WHERE layout_type='menu' ORDER BY name");
		
		// get current layout id
		if(!empty($post->ID)) $layout_id = get_post_meta($post->ID, 'purplesalad_layout_id', true);
		
		include(PURPLESALAD_PATH."/views/menu-meta-box.html.php");
	}
	
	static function save_menu_meta($post_id) {	
		global $wpdb;
				
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  return;		
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	  	if ('purplesalad_menu' != @$_POST['post_type']) return;
	  	update_post_meta($post_id, 'purplesalad_layout_id', $_POST['purplesalad_layout']);
	}
	
	static function content_filter($content) {
		global $post;
		if(@$post->post_type == 'purplesalad_menu') {			
			$content = self :: apply_layout($post, $content);
		}	
		return $content;
	}	
	
	// the content filter
	static function apply_layout($post, $content) {
		global $wpdb;
		if(@$post->post_type == 'purplesalad_menu') {			
			// select the layout
			$layout_id = get_post_meta($post->ID, 'purplesalad_layout_id', true);
			$layout_html = $wpdb->get_var($wpdb->prepare("SELECT html FROM ".PURPLESALAD_LAYOUTS." WHERE id=%d", $layout_id));
			
			if(strstr($layout_html, '{{{items}}}')) {				
				// select menu items in this menu
				$menu_items = $wpdb->get_results("SELECT tP.* FROM {$wpdb->posts} tP 
					JOIN {$wpdb->postmeta} tM ON tM.meta_key='purplesalad_menu_ids'
					WHERE tM.meta_value LIKE '%|".$post->ID."|%' AND tM.post_id=tP.ID
					AND tP.post_type='purplesalad_item' AND tP.post_status='publish'
					ORDER BY post_date DESC");
					
					
				$parts = explode('{{{items}}}', $layout_html);
				$layout_start = $parts[0];
				$sparts = explode('{{{/items}}}', $parts[1]);
				$items_html = $sparts[0];
				$layout_end = $sparts[1];
				
				// now repeat items for each item to receive $items_content
				$items_content = '';
				foreach($menu_items as $item) {
					// replace {{{item}}} with the layout for that item, then add to $items_content
					$items_content .= PurpleSaladMenuItem :: apply_layout($item, $items_html, true);
				}
				
				// finally concatenate layout parts with $items_content
				$content .= $layout_start . $items_content . $layout_end;
			} // end replacing items
			
			// replace title
			$content = str_replace('{{{menu-title}}}', stripslashes($post->post_title), $content);
		}
		return $content;
	}
}