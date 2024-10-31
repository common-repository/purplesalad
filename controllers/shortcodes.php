<?php
class PurpleSaladShortcodes {
	static function menu_item($atts) {
		$item_id = intval($atts['id']);
		if(empty($item_id)) return __('Menu item not found.', 'purplesalad');
		
		$item = get_post($item_id);
		if(empty($item->ID)) return __('Menu item not found.', 'purplesalad');
		
		return PurpleSaladMenuItem :: apply_layout($item, $item->post_content);
	} // end menu_item
	
	static function menu($atts) {
		$menu_id = intval($atts['id']);
		if(empty($menu_id)) return __('Menu  not found.', 'purplesalad');
		
		$menu = get_post($menu_id);
		if(empty($menu->ID)) return __('Menu not found.', 'purplesalad');
		
		return PurpleSaladMenu :: apply_layout($menu, $menu->post_content);
	} // end menu_item
}