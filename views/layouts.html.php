<div class="wrap">
	<h1><?php _e('Manage Layouts', 'purplesalad');?></h1>
	
	<p><?php _e('Show layouts for:', 'purplesalad')?> <select name="type" onchange="window.location='admin.php?page=purplesalad_layouts&type=' + this.value;">
		<option value="menu" <?php if($type == 'menu') echo 'selected'?>><?php _e('Menu', 'purplesalad');?></option>
		<option value="menu_item" <?php if($type == 'menu_item') echo 'selected'?>><?php _e('Menu Item', 'purplesalad');?></option>
	</select></p>
	
	<p><a href="admin.php?page=purplesalad_layouts&do=add&type=<?php echo $type?>"><?php _e('Click here to create a new layout', 'purplesalad')?></a></p>
	
	<?php if(count($layouts)):?>
		<table class="widefat">
			<tr><th><?php _e('Layout name', 'purplesalad')?></th><th><?php _e('Default?', 'purplesalad')?></th><th><?php _e('Action', 'purplesalad')?></th></tr>
			<?php foreach($layouts as $layout):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>"><td><?php echo stripslashes($layout->name)?></td>
				<td><?php echo $layout->is_def ? __('Yes', 'purplesalad') : __('No', 'purplesalad')?></td>
				<td><a href="admin.php?page=purplesalad_layouts&type=<?php echo $type?>&do=edit&id=<?php echo $layout->id?>"><?php _e('Edit', 'purplesalad')?></a>
				|
				<a href="#" onclick="purpleSaladDelLayout(<?php echo $layout->id?>);return false;"><?php _e('Delete', 'purplesalad')?></a></td></tr>
			<?php endforeach;?>
		</table>
	<?php else:?>
		<p><?php _e('There are no layouts of this type.', 'purplesalad')?></p>
	<?php endif;?>
</div>

<script type="text/javascript" >
function purpleSaladDelLayout(id) {
	if(confirm("<?php _e('Are you sure?', 'purplesalad')?>")) {
		window.location = 'admin.php?page=purplesalad_layouts&type=<?php echo $type?>&del=1&id=' + id;
	}
}
</script>