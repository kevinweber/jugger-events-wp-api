<?php

class JuggerEventsEndpointEventsUpcoming extends JuggerEventsController {

	protected $restBase = 'events/upcoming';
	protected $transient = TRANSIENT_JUGGER_EVENTS_UPCOMING;

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}

	function setPosts() {
		return $this->getUpcomingEvents();
	}

	function isUpcoming($dateString) {
		return new DateTime($dateString) > new DateTime();
	}

	function getUpcomingEvents() {
		$allEvents = $this->getAllEvents();

		$allEvents = array_filter($allEvents, function($value, $key) {
			$dateString = get_post_meta($value->ID)['jugger_event_datetime_start'][0];

			return $this->isUpcoming($dateString);
		}, ARRAY_FILTER_USE_BOTH);

		return $allEvents;
	}
}
