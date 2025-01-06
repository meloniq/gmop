<?php
namespace meloniq\GMOP;

class Frontend {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() : void {
		wp_enqueue_script( 'gmop_js', plugins_url( '/js/script.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'gmop_css', plugins_url( '/style.css', __FILE__ ) );
	}

}
