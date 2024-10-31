<p><label><?php _e('Choose menu layout', 'purplesalad');?></label> <select name="purplesalad_layout">
	<?php foreach($layouts as $layout):
	   $selected = '';
	   if(!empty($layout_id) and $layout_id == $layout->id) $selected = ' selected';
	   if(empty($layout_id) and $layout->is_def) $selected = ' selected';?>
		<option value="<?php echo $layout->id?>"<?php echo $selected?>><?php echo stripslashes($layout->name);?></option>
	<?php endforeach;?>
</select> <?php _e('(This layout will be used when the menu item is shown on the menu page.)','purplesalad');?></p>

<p><label><?php _e('Choose page layout', 'purplesalad');?></label> <select name="purplesalad_page_layout">
	<?php foreach($layouts as $layout):
	   $selected = '';
	   if(!empty($page_layout_id) and $page_layout_id == $layout->id) $selected = ' selected';
	   if(empty($page_layout_id) and $layout->is_def) $selected = ' selected';?>
		<option value="<?php echo $layout->id?>"<?php echo $selected?>><?php echo stripslashes($layout->name);?></option>
	<?php endforeach;?>
</select> <?php _e('(This layout will be used on the menu item page itself.)','purplesalad');?></p>

<p><?php _e('Item price:', 'purplesalad')?> <?php echo PURPLESALAD_CURRENCY?> <input type="text" size="6"  name="purplesalad_price" value="<?php echo $item_price?>"></p>

<p><?php _e('Assign to menus:', 'purplesalad');?> <?php foreach($menus as $menu):?>
	<input type="checkbox" name="purplesalad_menu_ids[]" value="<?php echo $menu->ID?>" <?php if(!empty($menu_ids) and @strstr($menu_ids, "|".$menu->ID."|")) echo 'checked'?>> <?php echo stripslashes($menu->post_title);?> &nbsp;
<?php endforeach;?></p>

<p><label><?php _e('Shortcode:', 'purplesalad');?></label> <input type="text" size="30" value='[purplesalad-menu-item id="<?php echo $post->ID?>"]' onclick="this.select()" readonly="true"><br>
<?php _e('Use this shortcode if you want to publish the menu item alone in a sidebar, widget, etc', 'purplesalad');?></p>