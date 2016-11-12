<?php

class JuggerEventsEndpointEventsPast extends JuggerEventsController {

	protected $restBase = 'events/past';
	protected $order = 'DESC';
	protected $transient = TRANSIENT_JUGGER_EVENTS_PAST;

	function setPosts() {
		return $this->getPastEvents();
	}

	function isPast($dateString) {
		return new DateTime($dateString) < new DateTime();
	}

	function getPastEvents() {
		$allEvents = $this->getAllEvents();

		$allEvents = array_filter($allEvents, function($value, $key) {
			$dateString = get_post_meta($value->ID)['jugger_event_datetime_start'][0];

			return $this->isPast($dateString);
		}, ARRAY_FILTER_USE_BOTH);

		return $allEvents;
	}
}
