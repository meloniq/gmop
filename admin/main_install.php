<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

// creates all tables for the plugin called during register_activation hook

function gmop_install_tables() {
	global $wpdb;
	$previous_version = get_option('gmop_version');


	// Check for capability
	if ( ! current_user_can( 'activate_plugins' ) )
		return;

	// add charset & collate like wp core
	$charset_collate = '';

	if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}
	
	if ( ! $previous_version ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE {$wpdb->prefix}gmop_markers (
					ID int(3) unsigned NOT NULL auto_increment,
					title varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					image_url text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					priority int(3) NOT NULL default '0',
					PRIMARY KEY	(ID)
				) $charset_collate;
				CREATE TABLE {$wpdb->prefix}gmop_objects (
					ID int(5) unsigned NOT NULL auto_increment,
					title varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					description text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					url text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					latitude text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					longitude text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					marker int(3) NOT NULL default '0',
					PRIMARY KEY	(ID)
				) $charset_collate;
				";
//					priority int(3) NOT NULL default '0',
		dbDelta($sql);

		$icon_url = get_option('home') . "/wp-content/plugins/mnet-gmaps-objects-plus/img/";
		$sql = "INSERT INTO {$wpdb->prefix}gmop_markers (title, image_url) VALUES ('" . __( 'Primary school', GMOP_TD ) . "', '".$icon_url."icon_school_elementary.png')";
		dbDelta($sql);
		$sql = "INSERT INTO {$wpdb->prefix}gmop_markers (title, image_url) VALUES ('" . __( 'Middle school', GMOP_TD ) . "', '".$icon_url."icon_school_middle.png')";
		dbDelta($sql);
		$sql = "INSERT INTO {$wpdb->prefix}gmop_markers (title, image_url) VALUES ('" . __( 'High school', GMOP_TD ) . "', '".$icon_url."icon_school_high.png')";
		dbDelta($sql);

		$sql = "INSERT INTO {$wpdb->prefix}gmop_objects (title, description, url, latitude, longitude, marker) VALUES ('" . __( 'Primary school', GMOP_TD )."', 'Szkoła Podstawowa nr 211 im. J. Korczaka z oddziałami integracyjnymi Nowy Świat 21A Warszawa', 'http://www.sp211.hg.pl/', '52.232268', '21.020542', '1')";
		dbDelta($sql);
		$sql = "INSERT INTO {$wpdb->prefix}gmop_objects (title, description, url, latitude, longitude, marker) VALUES ('" . __( 'General secondary school', GMOP_TD )."', 'Smolna 30, Warszawa', 'http://www.zamoyski.edu.pl/', '52.232938', '21.022811', '2')";
		dbDelta($sql);
		$sql = "INSERT INTO {$wpdb->prefix}gmop_objects (title, description, url, latitude, longitude, marker) VALUES ('" . __( 'Higher school of Journalism', GMOP_TD )."', 'im. M. Wańkowicza Nowy Świat 58, Warszawa', 'http://www.wsd.edu.pl/', '52.235474', '21.018691', '3')";
		dbDelta($sql);

		update_option( 'gmop_version', GMOP_VERSION );
	}

	$options = get_option('gmop_options');
	// set the default settings, if we didn't upgrade
	if ( empty( $options ) ) gmop_default_options();


}

/**
 * Setup the default option array for the plugin
 * 
 * @access internal
 * @return void
 */
function gmop_default_options() {

	update_option('gmop_api_key', '');
	update_option('gmop_gmaps_loc', 'http://maps.google.pl');
	update_option('gmop_default_latitude', '52.234528294213646');
	update_option('gmop_default_longitude', '21.005859375');
	update_option('gmop_options', 'installed');
}

?>