<?php

class JuggerEventsEndpointEventsAll extends JuggerEventsController {

	protected $rest_base = 'events';

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}
}
