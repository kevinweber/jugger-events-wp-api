<?php

class JuggerEventsEndpointEventsAll extends JuggerEventsController {

	protected $restBase = 'events';
	protected $transient = TRANSIENT_JUGGER_EVENTS_ALL;

	function setPosts() {
		return $this->getAllEvents();
	}
}
