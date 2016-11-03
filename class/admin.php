<?php

// Documentation/more examples:
// https://webdevstudios.com/2016/05/24/wp-api-adding-custom-endpoints/

/**
 * Extend the main WP_REST_Posts_Controller to a private endpoint controller.
 *
 * Transients must be defined as follows, e.g. in wp-config.php:
 *  **
 *  * Transients.
 *  * Used for cache (in)validation.
 *  *
 *  define('TRANSIENT_JUGGER_EVENTS_ALL', 'my-random-transient-123');
 *
 */

class JuggerEventsAPI {

	function __construct() {
		require_once JUGGER_EVENTS_API_PATH . 'class/rest/controller.php';

		include JUGGER_EVENTS_API_PATH . 'class/endpoints/events-all.php';
		new JuggerEventsEndpointEventsAll();

		include JUGGER_EVENTS_API_PATH . 'class/endpoints/events-upcoming.php';
		new JuggerEventsEndpointEventsUpcoming();

		include JUGGER_EVENTS_API_PATH . 'class/endpoints/events-past.php';
		new JuggerEventsEndpointEventsPast();
	}
}
