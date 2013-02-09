<?php


/**
 * Creates database tables
 */
function gmop_tables_install() {

	// create the markers table
	$sql = "
		ID int(3) NOT NULL AUTO_INCREMENT,
		title varchar(50) NOT NULL,
		image_url text NOT NULL,
		priority int(3) NOT NULL default '0',
		PRIMARY KEY  (ID)";

	gmop_install_table( 'gmop_markers', $sql );

	// create the objects table
	$sql = "
		ID int(5) NOT NULL AUTO_INCREMENT,
		title varchar(50) NOT NULL,
		description text NOT NULL,
		latitude text NOT NULL,
		longitude text NOT NULL,
		url text NOT NULL,
		marker int(3) NOT NULL default '0',
		PRIMARY KEY  (ID)";

	gmop_install_table( 'gmop_objects', $sql );

}


/**
 * Populates tables with sample data
 */
function gmop_populate_tables() {
	global $wpdb;

	// create markers
	$wpdb->insert( $wpdb->gmop_markers, array(
		'title' => __( 'Primary school', GMOP_TD ),
		'image_url' => plugins_url( '/img/icon_school_elementary.png', __FILE__ ),
	) );

	$wpdb->insert( $wpdb->gmop_markers, array(
		'title' => __( 'Middle school', GMOP_TD ),
		'image_url' => plugins_url( '/img/icon_school_middle.png', __FILE__ ),
	) );

	$wpdb->insert( $wpdb->gmop_markers, array(
		'title' => __( 'High school', GMOP_TD ),
		'image_url' => plugins_url( '/img/icon_school_high.png', __FILE__ ),
	) );


	// create objects
	$wpdb->insert( $wpdb->gmop_objects, array(
		'title' => __( 'Primary school', GMOP_TD ),
		'description' => 'Szkoła Podstawowa nr 211 im. J. Korczaka z oddziałami integracyjnymi Nowy Świat 21A Warszawa',
		'url' => 'http://www.sp211.hg.pl/',
		'latitude' => '52.232268',
		'longitude' => '21.020542',
		'marker' => '1',
	) );

	$wpdb->insert( $wpdb->gmop_objects, array(
		'title' => __( 'General secondary school', GMOP_TD ),
		'description' => 'Smolna 30, Warszawa',
		'url' => 'http://www.zamoyski.edu.pl/',
		'latitude' => '52.232938',
		'longitude' => '21.022811',
		'marker' => '2',
	) );

	$wpdb->insert( $wpdb->gmop_objects, array(
		'title' => __( 'Higher school of Journalism', GMOP_TD ),
		'description' => 'im. M. Wańkowicza Nowy Świat 58, Warszawa',
		'url' => 'http://www.wsd.edu.pl/',
		'latitude' => '52.235474',
		'longitude' => '21.018691',
		'marker' => '3',
	) );

}


/**
 * Register a table with $wpdb
 *
 * @param string $key The key to be used on the $wpdb object
 * @param string $name The actual name of the table, without $wpdb->prefix
 */
function gmop_register_table( $key, $name = false ) {
	global $wpdb;

	if ( !$name )
		$name = $key;

	$wpdb->tables[] = $name;
	$wpdb->$key = $wpdb->prefix . $name;
}


/**
 * Install a table
 *
 * @param string $key The key to be used on the $wpdb object
 * @param string $columns The columns of the table
 * @param string $upgrade_method The method of upgrade
 */
function gmop_install_table( $key, $columns, $upgrade_method = 'dbDelta' ) {
	global $wpdb;

	$full_table_name = $wpdb->$key;

	$charset_collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	if ( 'dbDelta' == $upgrade_method ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( "CREATE TABLE $full_table_name ( $columns ) $charset_collate" );
		return;
	}

	if ( 'delete_first' == $upgrade_method )
		$wpdb->query( "DROP TABLE IF EXISTS $full_table_name;" );

	$wpdb->query( "CREATE TABLE IF NOT EXISTS $full_table_name ( $columns ) $charset_collate;" );
}


/**
 * Uninstall a table
 *
 * @param string $key The key to be used on the $wpdb object
 */
function gmop_uninstall_table( $key ) {
	global $wpdb;

	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->$key );
}


