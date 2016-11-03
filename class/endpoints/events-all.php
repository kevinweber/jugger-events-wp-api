<?php

class JuggerEventsEndpointEventsAll extends JuggerEventsController {

	protected $restBase = 'events';
	protected $transient = TRANSIENT_JUGGER_EVENTS_ALL;

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}

	function setPosts() {
		return $this->getAllEvents();
	}
}
