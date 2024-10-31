<div class="wrap">
	<h1><?php printf(__('Add/Edit %s Layout', 'purplesalad'), $layout_type)?></h1>
	
	<p><a href="admin.php?page=purplesalad_layouts&type=<?php echo $type?>"><?php _e('Back to layouts', 'purplesalad');?></a></p>
	
	<form method="post" onsubmit="return purpleSaladValidate(this);">
		<div class="inside purplesalad-form">
			<p><label><?php _e('Layout name:', 'purplesalad')?></label> <input type="text" name="name" value="<?php echo stripslashes(@$layout->name)?>" size="30"></p>
			<p><label><?php _e('Layout design:', 'purplesalad')?></label> <?php echo wp_editor(stripslashes(@$layout->html), 'html')?><br>
			<h2><?php _e('Available variables:', 'purplesalad');?></h2>
			<?php if($type == 'menu'):?>
				{{{menu-title}}} - <?php _e('The title of the menu', 'purplesalad');?><br>
				{{{items}}} <?php _e('and', 'purplesalad');?> {{{/items}}} <?php printf(__('mark the beginning and the end of the list of items. You can use variable %s between them to indicate the loop of menu items.', 'purplesalad'), '{{{item}}}');?>
				<p><label><?php _e('Number of columns:', 'purplesalad');?></label> <select name="no_cols">
					<?php for($i=1; $i<=3; $i++):
						$selected = ($i == @$layout->no_cols) ? ' selected' : '';?>
						<option value="<?php echo $i?>"<?php echo $selected?>><?php echo $i?></option>
					<?php endfor;?>
				</select></p>
			<?php else:?>
				{{{item-title}}} - <?php _e('The title of the menu item.', 'purplesalad');?><br>
				{{{item-description}}} - <?php _e('This is the menu item description that you enter in the main box. It can contain images and media formatted in any way you wish. For easiness and consistency you may prefer to enter only text and to use the featured image.', 'purplesalad');?><br>
				{{{item-image}}} - <?php _e('If you want to use this you need to add featured image for the menu item.', 'purplesalad');?><br>
				{{{item-price}}} - <?php _e('The price of the item.', 'purpelsalad');?><br>
				{{{item-url}}} - <?php _e('The permalink URL of the item.', 'purpelsalad');?>
			<?php endif;?></p>
			<p><input type="checkbox" name="is_def" value="1" <?php if(!empty($layout->is_def)) echo 'checked'?>> 
				<?php printf(__('This is the default %s layout', 'purplesalad'), $layout_type)?></p>
			<p><input type="submit" name="ok" value="<?php _e('Save Layout', 'purplesalad')?>"</p>	
		</div>
	</form>
</div>

<script type="text/javascript" >
function purpleSaladValidate(frm) {
	if(frm.name.value == '') {
		alert("<?php _e('Please enter layout name', 'purplesalad')?>");
		frm.name.focus();
		return false;
	}
	
	return true;
}
</script>