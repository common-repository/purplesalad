<?php
// main model containing general config and UI functions
class PurpleSalad {
   static function install($update = false) {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	if(!$update) self::init();
   	
   	// enrollments to courses
   	if($wpdb->get_var("SHOW TABLES LIKE '".PURPLESALAD_LAYOUTS."'") != PURPLESALAD_LAYOUTS) {        
			$sql = "CREATE TABLE `" . PURPLESALAD_LAYOUTS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `name` VARCHAR(100) NOT NULL DEFAULT '',
				  `layout_type` VARCHAR(20) NOT NULL DEFAULT '',
				  `html` TEXT, 
				  `is_def` TINYINT UNSIGNED NOT NULL DEFAULT 0,
				  `no_cols` TINYINT UNSIGNED NOT NULL DEFAULT 0 /* columns for the menu layouts */					
				) DEFAULT CHARSET=utf8;";			
			$wpdb->query($sql);
	  }
   	
   	// flush rewrite rules
   	PurpleSaladMenu :: register_menu_type();
   	PurpleSaladMenuItem :: register_item_type();

		// load default layouts   	
   	PurpleSaladLayouts :: init();
	
		update_option('purplesalad_version', 0.15);
   } // end install()
   
   // initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'purplesalad', false, PURPLESALAD_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define('PURPLESALAD_LAYOUTS', $wpdb->prefix.'purplesalad_layouts');
		
		define( 'PURPLESALAD_VERSION', get_option('purplesalad_version'));
		
		$currency = get_option('purplesalad_currency');
		if(empty($currency)) update_option('purplesalad_currency', 'USD');  	
		define( 'PURPLESALAD_CURRENCY', get_option('purplesalad_currency'));
		
		// actions 
		add_action('save_post', array('PurpleSaladMenu', 'save_menu_meta'));
		add_action('save_post', array('PurpleSaladMenuItem', 'save_menu_item_meta'));
		
		// filters
		add_filter('the_content', array('PurpleSaladMenu', 'content_filter'));
		add_filter('the_content', array('PurpleSaladMenuItem', 'content_filter'));
		
		// shortcodes
		add_shortcode('purplesalad-menu-item', array('PurpleSaladShortcodes', 'menu_item'));
		add_shortcode('purplesalad-menu', array('PurpleSaladShortcodes', 'menu'));
		
		define('PURPLESALAD_NO_DECIMALS', get_option('purplesalad_no_decimals'));
		
		// default datepicker CSS
		if(get_option('purplesalad_datepicker_css') == '') {
			update_option('purplesalad_datepicker_css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		}
		
		purplesalad_define_filters();		
		
		$old_version = get_option('purplesalad_version');
		if(empty($old_version) or $old_version < 0.15) self :: install(true);		
	}
   
   // main menu
   static function menu() {
		$purplesalad_caps = current_user_can('manage_options') ? 'manage_options' : 'purplesalad_manage';   	
   	
   	add_menu_page(__('PurpleSalad', 'purplesalad'), __('PurpleSalad', 'purplesalad'), $purplesalad_caps, "purplesalad_options", 
   		array(__CLASS__, "options"));
   	add_submenu_page('purplesalad_options', __('Manage Layouts', 'purplesalad'), __('Manage Layouts', 'purplesalad'), $purplesalad_caps, "purplesalad_layouts", 
   		array('PurpleSaladLayouts', 'manage'));	
   	add_submenu_page('purplesalad_options', __('Import/Export Items', 'purplesalad'), __('Import/Export Items', 'purplesalad'), $purplesalad_caps, "purplesalad_import", 
   		array('PurpleSaladItems', 'import_export'));		
   	add_submenu_page('purplesalad_options', __('Settings', 'purplesalad'), __('Settings', 'purplesalad'), $purplesalad_caps, "purplesalad_options", 
   		array(__CLASS__, "options"));
   	add_submenu_page('purplesalad_options', __('Help', 'purplesalad'), __('Help', 'purplesalad'), $purplesalad_caps, "purplesalad_help", 
   		array(__CLASS__, "help"));	
	}
	
	// CSS and JS
	static function scripts() {
		// CSS
		wp_register_style( 'purplesalad-css', PURPLESALAD_URL.'css/main.css?v=1');
	  wp_enqueue_style( 'purplesalad-css' );
   
   	wp_enqueue_script('jquery');
	   
	   // Hostelpro's own Javascript
		wp_register_script(
				'purplesalad-common',
				PURPLESALAD_URL.'js/common.js',
				false,
				'0.1',
				false
		);
		// wp_enqueue_script("purplesalad-common");
		
		$translation_array = array('email_required' => __('Please provide a valid email address', 'purplesalad'),
		'name_required' => __('Please provide name', 'purplesalad'),	
		'ajax_url' => admin_url('admin-ajax.php'));	
		wp_localize_script( 'purplesalad-common', 'purplesalad_i18n', $translation_array );
	}
	
	// handle Purplesalad vars in the request
	static function query_vars($vars) {
		$new_vars = array('purplesalad');
		$vars = array_merge($new_vars, $vars);
	   return $vars;
	} 	
	
	// manage general options
	static function options() {
		global $wpdb;
		
		if(!empty($_POST['ok'])) {			
			if(empty($_POST['currency'])) $_POST['currency'] = $_POST['custom_currency'];
			update_option('purplesalad_currency', $_POST['currency']);
			update_option('purplesalad_menu_slug', $_POST['menu_slug']);
			update_option('purplesalad_item_slug', $_POST['item_slug']);
			//update_option('purplesalad_sender_email', $_POST['sender_email']);
			//update_option('purplesalad_sender_name', $_POST['sender_name']);			
			update_option('purplesalad_no_decimals', @$_POST['no_decimals']);
		}		
		
		if(!empty($_POST['datepicker_settings'])) {
			// these will be the same for PRO and free versions
			// datepicker locale and CSS
			update_option('purplesalad_locale_url', $_POST['locale_url']);
			update_option('purplesalad_datepicker_css', $_POST['datepicker_css']);
		}
		
		$currency = get_option('purplesalad_currency');
		$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
		   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
		   "ILS"=>"ILS", "INR" => "INR", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
		   "SGD"=>"SGD", "ZAR"=>"ZAR");
		$currency_keys = array_keys($currencies);
		   	
		require(PURPLESALAD_PATH."/views/options.html.php");
	}	// end options()
	
	static function help() {
		require(PURPLESALAD_PATH."/views/help.html.php");
	}	
	
	static function register_widgets() {
		// register_widget('WPHostelWidget');
	}
}