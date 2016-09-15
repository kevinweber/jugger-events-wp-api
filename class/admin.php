<?php
class JuggerEventsAPI {

	function __construct() {
        add_action( 'init', array( $this, 'register_custom_post_types'), 50 );
	}

    /**
    * Add REST API support to an already registered post type.
    */
    function register_custom_post_types() {
        global $wp_post_types;

        $post_type_name = 'jugger_event';

        if( isset( $wp_post_types[ $post_type_name ] ) ) {
            $wp_post_types[$post_type_name]->show_in_rest = true;
        }
    }
}