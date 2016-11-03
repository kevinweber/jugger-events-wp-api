<?php

class JuggerEventsEndpointEventsAll extends JuggerEventsController {

	protected $restBase = 'events';

	function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}

	function setPosts() {
		return $this->getAllEvents();
	}

	function getAllEvents() {
		return get_posts( array(
				'posts_per_page' => -1,
				'post_type' => $this->postType
		));
	}
}
