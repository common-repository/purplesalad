<?php
class PurpleSaladItems {
	static function import_export() {
		global $wpdb;
		
		if(!empty($_POST['import'])) {
			$result = self :: import();
		}
		
		if(!empty($_POST['export'])) {
			self :: export();
		}
		
		// select menus (so we select them when importing and exporting)
		$menus = get_posts(array("post_type" => 'purplesalad_menu', 'orderby'=>'post_title'));
		
		include(PURPLESALAD_PATH . "/views/import-export.html.php");
	}
	
	static function import() {
		global $wpdb;
		if(empty($_FILES['csv']['name'])) wp_die(__('Please upload file', 'purplesalad'));
		
		$page_layout_ids = $menu_layout_ids = array();
		$delimiter = ',';
		
		$row = 0;
		if (($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE) {			
		    while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {	    	  
		    	  $row++;	
		        if(empty($data)) continue;			  			  
		        if(!empty($_POST['skip_title_row']) and $row == 1) continue;	        
		       
		       // data format: title, contents, menu layout, page layout, item price, excerpt
				 // figure out menu layout and page layout IDs:
				 if(!isset($menu_layout_ids[$data[2]])) {
				 	$menu_layout_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".PURPLESALAD_LAYOUTS."
				 		WHERE layout_type='menu_item' AND name LIKE %s", $data[2]));
				 	$menu_layout_ids[$data[2]] = $menu_layout_id;	
				 }  
				 if(!isset($page_layout_ids[$data[3]])) {
				 	$page_layout_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".PURPLESALAD_LAYOUTS."
				 		WHERE layout_type='menu_item' AND name LIKE %s", $data[3]));
				 	$page_layout_ids[$data[3]] = $page_layout_id;	
				 }      
				 
				 // now WP insert post, then update post meta
				 $item = array("post_content" => $data[1], "post_title" => $data[0], 
				 	"post_type"=>'purplesalad_item', "post_excerpt" => $data[5], "post_status"=>'publish');
				 $item_id = wp_insert_post($item);
				 
				 if(!empty($item_id)) {
				 	// update meta
				 	update_post_meta($item_id, 'purplesalad_layout_id', $menu_layout_ids[$data[2]]);
				  	update_post_meta($item_id, 'purplesalad_page_layout_id', $page_layout_ids[$data[3]]);
				  	update_post_meta($item_id, 'purplesalad_item_price', $data[4]);
				  	update_post_meta($item_id, 'purplesalad_menu_ids', '|'.@implode('|',@$_POST['menus']).'|');
				 }
		       
		    } // end while
			 
			 $result = true;
		} // end if $handle
		else $result = false;	
		
		return $result;
	} // end import
	
	// export items to CSV
	static function export() {
		global $wpdb;
		
		$newline = purplesalad_define_newline();
		
		$menu_items = $wpdb->get_results("SELECT tP.* FROM {$wpdb->posts} tP 
					JOIN {$wpdb->postmeta} tM ON tM.meta_key='purplesalad_menu_ids' 
					AND tM.meta_value LIKE '%|".$_POST['menu_id']."|%' AND tM.post_id=tP.ID
					WHERE post_type = 'purplesalad_item' AND post_status != 'trash'
					ORDER BY post_date DESC");
						
		$rows = array();
		$rows[] = "Title,Contents,Menu Layout,Page Layout,Item price,Excerpt";
		
		// select all layotus to match them and avoid unnecessary queries
		$layouts = $wpdb->get_results("SELECT id,name FROM ".PURPLESALAD_LAYOUTS." WHERE layout_type='menu_item' ORDER BY id"); 
		
		foreach($menu_items as $item) {
			$item->post_content = str_replace("\t", "   ", $item->post_content);
			$item->post_content = str_replace('"', "'", $item->post_content);
			$item->post_content = purplesalad_nl2br($item->post_content);					
			$item->post_title = str_replace('"', "'", $item->post_title);
			
			// select layouts and price
			$layout_id = get_post_meta($item->ID, 'purplesalad_layout_id', true);
			$page_layout_id = get_post_meta($item->ID, 'purplesalad_page_layout_id', true);
			$item_price = get_post_meta($item->ID, 'purplesalad_item_price', true);
			$menu_layout = $page_layout = '';
			foreach($layouts as $layout) {
				if($layout_id == $layout->id) $menu_layout = stripslashes($layout->name);
				if($page_layout_id == $layout->id) $page_layout = stripslashes($layout->name);
			}
			
			$rows[] = '"'.stripslashes($item->post_title).'","'.stripslashes($item->post_content).'","'.$menu_layout
				.'","'.$page_layout.'",'.$item_price.',"'.$item->post_excerpt.'"';
		}	// end foreach item	
		
		$csv=implode($newline,$rows);
	
		// credit to http://yoast.com/wordpress/users-to-csv/	
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		
		$filename = 'menu-items.csv';		
	
		header('Content-Type: ' . purplesalad_get_mime_type());
		header('Expires: ' . $now);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Pragma: no-cache');
		echo $csv;
		exit;	
	} // end export
}