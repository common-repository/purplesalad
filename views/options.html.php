<div class="wrap">
	<h1><?php _e('PurpleSalad Options', 'purplesalad')?></h1>
	
	<p><?php _e('PurpleSalad lets you quickly create restaurant menus and publish them using different menu layouts.', 'purplesalad');?></p>
	
	<form method="post">
		<div class="inside purplesalad-form">
			<p><?php _e('These settings reflect how the prices on your menu items are displayed (if you choose to display prices).', 'purplesalad')?></p>
			<p><label><?php _e('Choose your currency:', 'purplesalad')?></label> <select name="currency" onchange="purplesaladChangeCurrency(this.value);">
				<?php foreach($currencies as $key=>$val):
	            if($key==$currency) $selected='selected';
	            else $selected='';?>
	        		<option <?php echo $selected?> value='<?php echo $key?>'><?php echo $val?></option>
	         <?php endforeach; ?>
	         <option value="" <?php if(!in_array($currency, $currency_keys)) echo 'selected'?>><?php _e('Custom', 'purplesalad')?></option>
				</select> <input type="text" id="customCurrency" name="custom_currency" style="display:<?php echo in_array($currency, $currency_keys) ? 'none' : 'inline';?>" value="<?php echo $currency?>">
				
				<input type="checkbox" name="no_decimals" value="1" <?php if(get_option('purplesalad_no_decimals') == 1) echo 'checked'?>> <?php _e('Show no decimals.', 'purplesalad');?></p>
				
				<h3><?php _e('URL identificators for menus and menu items', 'purplesalad');?></h3>
				
				<p><?php _e('These are the parts of the URLs that identify a post as PurpleSalad menu or menu item. These URL slugs are shown at the browser address bar and are parts of all links to menus or menu items. By default they are "purple-menu" and "purple-item". You can change them here.', 'purplesalad');?></p>
				
				<p><label><?php _e('Menu URL Slug:', 'purplesalad');?></label> <input type="text" name="menu_slug" value="<?php echo get_option('purplesalad_menu_slug')?>"></p>
				<p><label><?php _e('Menu Item URL Slug:', 'purplesalad');?></label> <input type="text" name="item_slug" value="<?php echo get_option('purplesalad_item_slug')?>"></p>
				<p><?php printf(__('Note: if you change these you MUST visit your <a href="%s">Permalinks</a> page once to get the new rewrite rules applied.', 'purplesalad'), 'options-permalink.php');?></p>
				
			<p><input type="submit" name="ok" value="<?php _e('Save Options', 'purplesalad')?>"></p>	
		</div>
	</form>
</div>

<script type="text/javascript" >
function purplesaladChangeCurrency(val) {
	if(val) {
		jQuery('#customCurrency').hide();
	}
	else {
		jQuery('#customCurrency').show();
	}
}
</script>