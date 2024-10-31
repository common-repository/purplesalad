<?php 
// contains little procedural functions to output various HTML strings
// safe redirect
function purplesalad_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

// new line for CSV
function purplesalad_define_newline() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$unewline = "\r\n";
	if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
	   $unewline = "\r\n";
	} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
	   $unewline = "\r";
	} else {
	   $unewline = "\n";
	}
	return $unewline;
}


function purplesalad_get_mime_type() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$USER_BROWSER_AGENT="";

	if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		$USER_BROWSER_AGENT='OPERA';
	} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		$USER_BROWSER_AGENT='IE';
	} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		$USER_BROWSER_AGENT='OMNIWEB';
	} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		$USER_BROWSER_AGENT='MOZILLA';
	} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
    	$USER_BROWSER_AGENT='KONQUEROR';
	} else {
    	$USER_BROWSER_AGENT='OTHER';
	}

	$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
				? 'application/octetstream'
				: 'application/octet-stream';
	return $mime_type;
}

// displays session flash, errors etc, and clears them if required
function purplesalad_display_alerts() {
	global $error, $success;
	
	if(!empty($_SESSION['hostelpro_flash']))
	{
		echo "<div class='hostelpro-alert'><p>".$_SESSION['hostelpro_flash']."</p></div>";
		unset($_SESSION['hostelpro_flash']);
	}
	
	if(!empty($error)){
		echo '<div class="hostelpro-error"><p>'.$error.'</p></div>';
	}
	
	if(!empty($success)){
		echo '<div class="hostelpro-success"><p>'.$success.'</p></div>';
	}
}

function purplesalad_datetotime($date) {
	list($year, $month, $day) = explode("-",$date);
	return mktime(1, 0, 0, $month, $day, $year);
}


// function to conditionally add DB fields
function purplesalad_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
}

// get the current admin email
function purplesalad_admin_email() {
	$admin_email = get_option('purplesalad_sender_email');
	$admin_name = get_option('purplesalad_sender_name');
	if(empty($admin_email)) $admin_email = get_option('admin_email');
	
	if(!empty($admin_name)) $admin_email = $admin_name . ' <'.$admin_email.'>';
	
	return $admin_email;
}

/*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Tristan Jahier
 * thanks to http://tristan-jahier.fr/blog/2013/08/convertir-un-format-de-date-php-en-format-de-date-jqueryui-datepicker
 */
if(!function_exists('dateformat_PHP_to_jQueryUI')) { 
	function dateformat_PHP_to_jQueryUI($php_format) {
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}
}

// enqueue the localized and themed datepicker
function purplesalad_enqueue_datepicker() {
	$locale_url = get_option('purplesalad_locale_url');	
	wp_enqueue_script('jquery-ui-datepicker');	
	if(!empty($locale_url)) {
		// extract the locale
		$parts = explode("datepicker-", $locale_url);
		$sparts = explode(".js", $parts[1]);
		$locale = $sparts[0];
		wp_enqueue_script('jquery-ui-i18n-'.$locale, $locale_url, array('jquery-ui-datepicker'));
	}
	wp_enqueue_style('jquery-style', get_option('purplesalad_datepicker_css'));
}

// manually apply Wordpress filters on the content
// to avoid calling apply_filters('the_content')	
function purplesalad_define_filters() {
	global $wp_embed;

	add_filter( 'purplesalad_content', 'wptexturize' ); // Questionable use!
	add_filter( 'purplesalad_content', 'convert_smilies' );
   add_filter( 'purplesalad_content', 'convert_chars' );
	add_filter( 'purplesalad_content', 'shortcode_unautop' );
	add_filter( 'purplesalad_content', 'do_shortcode' );
	
	// Compatibility with specific plugins
	// qTranslate
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) add_filter('purplesalad_content', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage');
	
	// WP Quick LaTeX
	if(function_exists('quicklatex_parser')) add_filter( 'purplesalad_content',  'quicklatex_parser', 7);
}	

// nl2br but without screwing tables and other tags
function purplesalad_nl2br($content) {
	$content = preg_replace("/\>(\r?\n){1,}/", ">", $content);	
	
	$content = nl2br($content);
	
	// remove br inside pre
	$match = array();
	if(preg_match_all('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s', $content, $match)){		
	    foreach($match as $a){
	        foreach($a as $b){	        		
	           $content = str_replace($b, str_replace("<br />", "", $b), $content);	           
	        }
	    }
	}
	
	return $content;
}