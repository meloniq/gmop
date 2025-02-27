<?php
namespace meloniq\GMOP;

class Overview {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts() : void {
		// TODO: enqueue only on plugin pages

		// needed for tabs
		wp_enqueue_script( 'jquery-ui-tabs' );
		// needed for image upload
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() : void {
		add_menu_page(
			__( 'GMOP Overview', GMOP_TD ),
			__( 'GMOP', GMOP_TD ),
			'manage_options',
			'gmop',
			array( $this, 'render_page' ),
			'dashicons-location-alt'
		);
	}

	/**
	 * Render page.
	 *
	 * @return void
	 */
	public function render_page() : void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'GMOP Overview', GMOP_TD ); ?></h1>
			<h3><?php _e( 'How to start using this plugin?', GMOP_TD ); ?></h3>
			<p><?php _e( '1) Go to Settings tab and generate new Google Maps API key for Your domain.', GMOP_TD ); ?></p>
			<p><?php _e( '2) Go to Object Types tab. You will find there 3 preinstalled object types, remove, modify or add new types which You wanna use.', GMOP_TD ); ?></p>
			<p><?php _e( '3) Go to Objects tab. You will find there 3 preinstaled objects, remove, modify or add new which You wanna show on maps.', GMOP_TD ); ?></p>
			<p><?php _e( '4) When You will finish with objects, click ReGenerate Cache button in the top of page. Cache file of objects will be re-generated. <br />Note: cache folder inside plugin dir MUST have writing permission.', GMOP_TD ); ?></p>
			<p><?php _e( '5) Open template file where You wanna include maps (eg. single.php or loop-single.php) and in prepared place for map add below code:', GMOP_TD ); ?></p>
			<p><code>
				if ( function_exists( 'gmop_map' ) ) { <br />
					$base_address = get_post_meta( $post->ID, 'location', true ); <br />
					$base_title = get_the_title( $post->ID ); <br />
					if ( ! empty( $base_address ) ) { <br />
						gmop_map( $post->ID, $base_address, $base_title ); <br />
					} <br />
				}
			</code></p>
			<p><?php _e( 'By variable $base_address You need to pass address in format "Street, City, Postal Code, Country".', GMOP_TD ); ?></p>
		</div>
		<?php
	}

}
