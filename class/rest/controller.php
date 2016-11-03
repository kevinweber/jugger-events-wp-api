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

abstract class JuggerEventsController extends WP_REST_Posts_Controller {

	/**
	 * The namespace.
	 *
	 * @var String
	 */
	protected $namespace = 'jugger';

 	/**
 	 * The post type for the current object.
 	 *
 	 * @var String
 	 */
 	protected $postType = 'jugger-event';

 	/**
 	 * Rest base for the current object.
 	 *
 	 * @var String
 	 */
 	protected $restBase;

	/**
	 * Determine how long endpoint data is cached.
	 *
	 * @var Integer
	 */
	protected $cacheLength = 60 * 60 * 2;

	/**
	 * Define order of provided events.
	 *
	 * @var String<'ASC'|'DESC'>
	 */
	protected $order = 'ASC';

	/**
	 * WordPress posts that should be exposed.
	 *
	 * @var Array
	 */
	protected $posts = [];
	abstract function setPosts();


	function init() {
		$this->posts = $this->setPosts();
		$this->registerCustomRouteEvents();
	}

	function registerCustomRouteEvents() {
  	register_rest_route( $this->namespace, '/' . $this->restBase, array(
	 	 'methods'  => WP_REST_Server::READABLE,
     'callback' => array( $this, 'createEvents' ),
	 	));
	}

	function validate($object, $property = null, $offset = null) {
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

			return $this->order == 'ASC' ? $t1 - $t2 : $t2 - $t1;
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

	function createEvents(WP_REST_Request $request) {
	   if ( false === ( $all_events = get_transient( TRANSIENT_JUGGER_EVENTS_ALL ) ) ) {
	       $all_events = $this->posts;
				 $new_events_object = [];

				foreach ($all_events as $post) {
					$new_event = $this->getEventObject($post);

					array_push($new_events_object, $new_event);
				}

				$this->sortByDate($new_events_object, "dateTimeStart");
				$all_events = $new_events_object;

	      // Cache for 2 hours
	      // Change "TRANSIENT_JUGGER_EVENTS_ALL" (in wp-config.php) to no longer access cached content
	      set_transient( TRANSIENT_JUGGER_EVENTS_ALL, $all_events, $this->cacheLength );
	   }

	   return $all_events;
	}

	function getAllEvents() {
		return get_posts( array(
				'posts_per_page' => -1,
				'post_type' => $this->postType
		));
	}
}
