<?php
namespace meloniq\GMOP;

class Shortcode {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'GMOP', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render shortcode.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function render_shortcode( $atts ) : string {
		// TODO: implement

		return '';
	}

}
