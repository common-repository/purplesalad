<?php
/*
Plugin Name: PurpleSalad Restaurant Plugin
Plugin URI: http://purplesalad.net
Description: Plugin for Restaurants - manage menus and more
Author: Kiboko Labs
Version: 0.7.2
Author URI: http://purplesalad.net
License: GPLv2 or later
*/

define( 'PURPLESALAD_PATH', dirname( __FILE__ ) );
define( 'PURPLESALAD_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'PURPLESALAD_URL', plugin_dir_url( __FILE__ ));

// require controllers and models
require(PURPLESALAD_PATH."/helpers/htmlhelper.php");
require(PURPLESALAD_PATH."/models/purplesalad.php");
require(PURPLESALAD_PATH."/models/menu.php");
require(PURPLESALAD_PATH."/models/menu-item.php");
require(PURPLESALAD_PATH."/controllers/layouts.php");
require(PURPLESALAD_PATH."/controllers/items.php");
require(PURPLESALAD_PATH."/controllers/shortcodes.php");

add_action('init', array("PurpleSalad", "init"));
add_action('init', array("PurpleSaladMenu", "register_menu_type"));
add_action('init', array("PurpleSaladMenuItem", "register_item_type"));

register_activation_hook(__FILE__, array("PurpleSalad", "install"));
add_action('admin_menu', array("PurpleSalad", "menu"));
add_action('admin_enqueue_scripts', array("PurpleSalad", "scripts"));

// show the things on the front-end
add_action( 'wp_enqueue_scripts', array("PurpleSalad", "scripts"));

// widgets
add_action( 'widgets_init', array("PurpleSalad", "register_widgets") );

// other actions
add_action('wp_ajax_purplesalad_ajax', 'purplesalad_ajax');
add_action('wp_ajax_nopriv_purplesalad_ajax', 'purplesalad_ajax');
