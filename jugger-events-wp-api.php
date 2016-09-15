<?php
/*
Plugin Name: Jugger Events - API
Plugin URI: http://kevinw.de/jugger/
Description: Configuration of the API for jugger.events
Version: 1.0
Author: Kevin Weber
Author URI: http://kevinw.de/
License: MIT
Text Domain: jugger-events-wp-api
*/

if ( !defined( 'JUGGER_EVENTS_API_PATH' ) )
	define( 'JUGGER_EVENTS_API_PATH', plugin_dir_path( __FILE__ ) );

if ( !defined( 'JUGGER_EVENTS_API_FILE' ) ) {
	define( 'JUGGER_EVENTS_API_FILE', __FILE__ );
}

include JUGGER_EVENTS_API_PATH . 'class/admin.php';

new JuggerEventsAPI();