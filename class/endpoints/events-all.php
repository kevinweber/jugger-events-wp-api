<?php

class JuggerEventsEndpointEventsAll extends JuggerEventsController {

	protected $restBase = 'events';

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}

	function setPosts() {
		return $this->getAllEvents();
	}
}
