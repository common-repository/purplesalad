<?php 
class PurpleSaladLayouts {
	static function manage() {
		global $wpdb;
		$do = empty($_GET['do']) ? 'list' : $_GET['do'];
		$type = empty($_GET['type']) ? 'menu' : $_GET['type'];
		
		switch($do) {
			case 'add':
				if(!empty($_POST['ok'])) {
					$wpdb->query($wpdb->prepare("INSERT INTO ".PURPLESALAD_LAYOUTS." SET
						name=%s, layout_type=%s, html=%s, is_def=%d, no_cols=%d", 
						$_POST['name'], $type, $_POST['html'], @$_POST['is_def'], @$_POST['no_cols']));
					$id = $wpdb->insert_id;	
					
					// in case this layout is marked as default we should reset is_def for all others
					if(!empty($_POST['is_def'])) {
						$wpdb->query($wpdb->prepare("UPDATE ".PURPLESALAD_LAYOUTS."
							SET is_def=0 WHERE id!=%d AND layout_type=%s", $id, $type));
					}					
					 	
					purplesalad_redirect("admin.php?page=purplesalad_layouts&type=".$type);
				}
				
				$layout_type = ($type == 'menu') ? __('Menu', 'purplesalad') : __('Menu Item', 'purplesalad');
				include(PURPLESALAD_PATH."/views/layout.html.php");	
			break;	
			
			case 'edit':
				if(!empty($_POST['ok'])) {
					$wpdb->query($wpdb->prepare("UPDATE ".PURPLESALAD_LAYOUTS." SET
						name=%s, layout_type=%s, html=%s, is_def=%d, no_cols=%d WHERE id=%d", 
						$_POST['name'], $type, $_POST['html'], @$_POST['is_def'], @$_POST['no_cols'], $_GET['id']));
						
					// in case this layout is marked as default we should reset is_def for all others
					if(!empty($_POST['is_def'])) {
						$wpdb->query($wpdb->prepare("UPDATE ".PURPLESALAD_LAYOUTS."
							SET is_def=0 WHERE id!=%d AND layout_type=%s", $_GET['id'], $type));
					}	
						
					purplesalad_redirect("admin.php?page=purplesalad_layouts&type=".$type);
				}
				
				$layout = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".PURPLESALAD_LAYOUTS." WHERE id=%d", $_GET['id']));
				
				$layout_type = ($type == 'menu') ? __('Menu', 'purplesalad') : __('Menu Item', 'purplesalad');				
				include(PURPLESALAD_PATH."/views/layout.html.php");	
			break;			
			
			case 'list':
				if(!empty($_GET['del'])) {
					$wpdb->query($wpdb->prepare("DELETE FROM ".PURPLESALAD_LAYOUTS." WHERE id=%d", $_GET['id']));
					purplesalad_redirect("admin.php?page=purplesalad_layouts&type=".$type);
				}			
			
				$layouts = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".PURPLESALAD_LAYOUTS." 
					WHERE layout_type=%s ORDER BY name", $type));
					
				include(PURPLESALAD_PATH."/views/layouts.html.php");	
			break;
		}
	} // end manage layouts
	
	// on first install insert 2-3 ready layouts
	static function init() {
		global $wpdb;
		
		if(get_option('purplesalad_default_layouts_loaded') == '1') return false;
		
		// insert the default menu layout
		$menu_layout = '{{{items}}}{{{item}}}<hr />{{{/items}}}';
		$wpdb->query($wpdb->prepare("INSERT INTO ".PURPLESALAD_LAYOUTS." SET
						name=%s, layout_type='menu', html=%s, is_def=1, no_cols=2", __('Default', 'purplesalad'), $menu_layout));
						
						
		// insert 3 menu item layouts
		$default_layout = '<h2>{{{item-title}}}</h2>
		<div class="purplesalad-item-wrap">
			<div class="purplesalad-item-image">{{{item-image}}}</div>
			<div class="purplesalad-item-data">
				<div class="purplesalad-item-price">{{{item-price}}}</div>
				<div class="purplesalad-item-description">{{{item-description}}}</div>
			</div>	
		</div>';
		
		$simple_layout = '<h2>{{{item-title}}}</h2>
		{{{item-description}}}';			
		
		$table_layout = '<h3>{{{item-title}}}</h3>
		<table class="purplesalad-item-table"><tr><td valign="top" rowspan="2" class="purplesalad-item-image">{{{item-image}}}</td>
		<td class="purplesalad-item-price">{{{item-price}}}</td></tr>
		<tr><td class="purplesalad-item-descritpion">{{{item-description}}}</td></tr></table>';	
		
		// now insert these 3 layouts
		$wpdb->query($wpdb->prepare("INSERT INTO ".PURPLESALAD_LAYOUTS." (name, layout_type, html, is_def)
			VALUES (%s, %s, %s, %d), (%s, %s, %s, %d), (%s, %s, %s, %d)", 
				__('Default', 'purplesalad'), 'menu_item', $default_layout, 1, 
				__('Simple', 'purplesalad'), 'menu_item', $simple_layout, 0,
				__('Table', 'purplesalad'), 'menu_item', $table_layout, 0));
				
		update_option('purplesalad_default_layouts_loaded', '1');		
	} // end init()
}