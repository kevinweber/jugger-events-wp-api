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

class JuggerEventsController extends WP_REST_Posts_Controller {

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'jugger';

 	/**
 	 * The post type for the current object.
 	 *
 	 * @var string
 	 */
 	protected $post_type = 'jugger-event';

 	/**
 	 * Rest base for the current object.
 	 *
 	 * @var string
 	 */
 	protected $rest_base;

	// /**
	// * Add REST API support to an already registered post type.
	// */
	//
	// function register_custom_post_types() {
	// 	global $wp_post_types;
	//
	//   if( isset( $wp_post_types[ $this->post_type ] ) ) {
	//   	$wp_post_types[$this->post_type]->show_in_rest = true;
	//   }
	// }

	function init() {
		$this->registerCustomRouteEvents();
	}

	function registerCustomRouteEvents() {
  	register_rest_route( $this->namespace, '/' . $this->rest_base, array(
	 	 'methods'  => WP_REST_Server::READABLE,
     'callback' => array( $this, 'jugger_events_all' ),
	 	));
	}

	function getAllEvents() {
		return get_posts( array(
				'posts_per_page' => -1,
				'post_type' => $this->post_type
		));
	}

	static function validate($object, $property = null, $offset = null) {
		if (!is_null($property) && isset($object[$property]) && !is_null($offset)) {
			return $object[$property][$offset];
		} else if (!is_null($object) && isset($object[$property])) {
			return $object;
		} else {
			return "";
		}
	}

	function sortByDate(&$arrayToSort, $sortByProperty) {
    usort($arrayToSort, function($a, $b) use ($sortByProperty) {
			$t1 = strtotime($a[$sortByProperty]);
			$t2 = strtotime($b[$sortByProperty]);

			return $t1 - $t2;
    });
	}

	function getEventObject($post) {
		$post_meta = get_post_meta($post->ID);
		$coordinates = explode(",", $post_meta["map"][0]);

		return [
				"id" => $post->ID,
				"dateTimeStart" => $this->validate($post_meta, "jugger_event_datetime_start", 0),
				"dateTimeEnd" => $this->validate($post_meta, "jugger_event_datetime_end", 0),
				"description" => $this->validate($post->post_content),
				"link" => $this->validate($post->guid),
				"location" => [
					"address" => $this->validate($post_meta, "jugger_event_address", 0),
					"latitude" => $coordinates[0],
					"longitude" => $coordinates[1]
				],
				"title" => $this->validate($post->post_title),
				// Only allow one type per event
				"type" => $this->validate($post_meta, "jugger_event_type", 0),
		];
	}

	function jugger_events_all(WP_REST_Request $request) {
	   if ( false === ( $all_events = get_transient( TRANSIENT_JUGGER_EVENTS_ALL ) ) ) {
	       $all_events = $this->getAllEvents();
				 $new_events_object = [];

				foreach ($all_events as $post) {
					$new_event = $this->getEventObject($post);

					array_push($new_events_object, $new_event);
				}

				// Sort array by date with the latest event last
				$this->sortByDate($new_events_object, "dateTimeStart");

				$all_events = $new_events_object;

	      // Cache for 2 hours
	      // Change "TRANSIENT_JUGGER_EVENTS_ALL" (in wp-config.php) to no longer access cached content
	      set_transient( TRANSIENT_JUGGER_EVENTS_ALL, $all_events, 60*60*2 );
	   }

	   return $all_events;
	}
}