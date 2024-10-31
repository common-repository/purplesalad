<div class="wrap">
	<h1><?php _e('Import / Export Menu Items', 'purplesalad');?></h1>
	
	<?php if(!empty($_POST['import'])):?>
		<p class="purplesalad-notice purplesalad-<?php echo $result ? 'success' : 'error';?>">
			<?php if($result) _e('Menu items were imported successfully.', 'purplesalad');
			else _e('There was an error importing the menu items.', 'purplesalad');?>
		</p>
	<?php endif;?>
	
	<h2><?php _e('Import Items:', 'purplesalad');?></h2>
	<form method="post" enctype="multipart/form-data">
		<div class="inside purplesalad-form">
		  <p><label><?php _e('Upload file:', 'purplesalad');?></label> <input type="file" name="csv"> 
		  <br><?php printf(__('The file must be CSV, comma-delimited. <a href="%s" target="_blank">See the format</a>', 'purplesalad'), 'http://purplesalad.net/blog/import-csv-format-for-purplesalad-plugin/');?></p>
		  <p><?php _e('Uploaded items will be linked to the following menus:','purplesalad');?> <br>
		  <?php foreach($menus as $menu):?>
		  	<span style="white-space: nowrap;"><input type="checkbox" name="menus[]" value="<?php echo $menu->ID?>"> <?php echo stripslashes($menu->post_title);?></span>
		  <?php endforeach;?></p>
		  <p><input type="checkbox" name="skip_title_row" value="1"> <?php _e('Skip title row in the CSV file.', 'purplesalad');?></p>
		  	<p><input type="submit" name="import" value="<?php _e('Import', 'purplesalad');?>"></p>
		</div>	  	
	</form>
	
	<hr>
	
	<h2><?php _e('Export Items:', 'purplesalad');?></h2>
	
	<form method="post" action="admin.php?page=purplesalad_import&noheader=1">
		<div class="inside purplesalad-form">
			<p><?php _e('Select menu:', 'purplesalad');?> <select name="menu_id">
			<?php foreach($menus as $menu):?>
				<option value="<?php echo $menu->ID?>"><?php echo stripslashes($menu->post_title);?></option>
			<?php endforeach;?>
			</select>
			<input type="submit" name="export" value="<?php _e('Export', 'purplesalad');?>"></p>
		</div>	
	</form> 
</div>