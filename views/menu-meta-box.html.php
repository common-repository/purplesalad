<p><label><?php _e('Choose layout', 'purplesalad');?></label> <select name="purplesalad_layout">
	<?php foreach($layouts as $layout):
	   $selected = '';
	   if(!empty($layout_id) and $layout_id == $layout->id) $selected = ' selected';
	   if(empty($layout_id) and $layout->is_def) $selected = ' selected';?>
		<option value="<?php echo $layout->id?>"<?php echo $selected?>><?php echo stripslashes($layout->name);?></option>
	<?php endforeach;?>
</select></p>

<p><label><?php _e('Shortcode:', 'purplesalad');?></label> <input type="text" size="30" value='[purplesalad-menu id="<?php echo $post->ID?>"]' onclick="this.select()" readonly="true"><br>
<?php _e('Use this shortcode if you want to publish the menu in a sidebar, widget, etc', 'purplesalad');?></p>