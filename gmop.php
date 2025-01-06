<?php
/**
 * Plugin Name:       Google Maps Objects Plus
 * Plugin URI:        https://blog.meloniq.net/
 * Description:       Showing maps based on location saved in post meta with closest objects definied in administration panel.
 *
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Version:           1.1
 *
 * Author:            MELONIQ.NET
 * Author URI:        https://blog.meloniq.net
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gmop
*/

namespace meloniq\GMOP;

// If this file is accessed directly, then abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GMOP_TD', 'gmop' );
define( 'GMOP_FILE', __FILE__ );


/**
 * Setup Plugin data.
 *
 * @return void
 */
function setup() {
	global $mnet_gmop;

	require_once( dirname( __FILE__ ) . '/src/class-install.php' );
	require_once( dirname( __FILE__ ) . '/src/class-shortcode.php' );
	require_once( dirname( __FILE__ ) . '/src/class-frontend.php' );

	$mnet_gmop['install']   = new Install();
	$mnet_gmop['shortcode'] = new Shortcode();
	$mnet_gmop['frontend']  = new Frontend();

	// admin pages
	if ( is_admin() ) {
		require_once( dirname( __FILE__ ) . '/src/class-overview.php' );
		require_once( dirname( __FILE__ ) . '/src/class-settings.php' );
		require_once( dirname( __FILE__ ) . '/src/class-objects.php' );
		require_once( dirname( __FILE__ ) . '/src/class-object-types.php' );

		$mnet_gmop['overview']     = new Overview();
		$mnet_gmop['settings']     = new Settings();
		$mnet_gmop['objects']      = new Objects();
		$mnet_gmop['object_types'] = new Object_Types();
	}
}
add_action( 'after_setup_theme', 'meloniq\GMOP\setup' );


/**
 * Load Text-Domain
 */
function load_textdomain() {
	load_plugin_textdomain( 'gmop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'meloniq\GMOP\load_textdomain' );


/**
 * Load functions, register tables
 */
require_once( dirname( __FILE__ ) . '/gmop-functions.php' );

gmop_register_table( 'gmop_markers' );
gmop_register_table( 'gmop_objects' );

