<?php
namespace meloniq\GMOP;

class Install {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		register_activation_hook( GMOP_FILE, array( $this, 'activate' ) );
	}

	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public function activate() : void {
		$this->install_tables();
		$this->add_sample_data();
	}

	/**
	 * Install tables.
	 *
	 * @return void
	 */
	protected function install_tables() : void {
		// create the markers table
		$sql = "
			ID int(3) NOT NULL AUTO_INCREMENT,
			title varchar(50) NOT NULL,
			image_url text NOT NULL,
			priority int(3) NOT NULL default '0',
			PRIMARY KEY  (ID)";

		$this->create_table( 'gmop_markers', $sql );

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

		$this->create_table( 'gmop_objects', $sql );
	}

	/**
	 * Create table.
	 *
	 * @param string $table_name
	 * @param string $sql
	 * @param string $upgrade_method (Optional)
	 *
	 * @return void
	 */
	protected function create_table( string $table_name, string $sql, string $upgrade_method = 'dbDelta' ) : void {
		global $wpdb;

		$full_table_name = $wpdb->$table_name;

		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}

			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}

		if ( 'dbDelta' == $upgrade_method ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( "CREATE TABLE $full_table_name ( $columns ) $charset_collate" );
			return;
		}

		if ( 'delete_first' == $upgrade_method ) {
			$wpdb->query( "DROP TABLE IF EXISTS $full_table_name;" );
		}

		$wpdb->query( "CREATE TABLE IF NOT EXISTS $full_table_name ( $columns ) $charset_collate;" );
	}

	/**
	 * Uninstall a table.
	 *
	 * @param string $table_name The key to be used on the $wpdb object
	 *
	 * @return void
	 */
	protected function uninstall_table( string $table_name ) : void {
		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->$table_name );
	}

	/**
	 * Add sample data.
	 *
	 * @return void
	 */
	protected function add_sample_data() : void {
		$sample_data = get_option( 'gmop_sample_data' );

		if ( $sample_data === 'done' ) {
			return;
		}

		$this->populate_tables();
		$this->set_initial_options();

		update_option( 'gmop_sample_data', 'done' );
	}

	/**
	 * Populates tables with sample data.
	 *
	 * @return void
	 */
	protected function populate_tables() : void {
		global $wpdb;

		// create markers
		$wpdb->insert( $wpdb->gmop_markers, array(
			'title' => __( 'Primary school', GMOP_TD ),
			'image_url' => plugins_url( '/img/icon_school_elementary.png', GMOP_FILE ),
		) );

		$wpdb->insert( $wpdb->gmop_markers, array(
			'title' => __( 'Middle school', GMOP_TD ),
			'image_url' => plugins_url( '/img/icon_school_middle.png', GMOP_FILE ),
		) );

		$wpdb->insert( $wpdb->gmop_markers, array(
			'title' => __( 'High school', GMOP_TD ),
			'image_url' => plugins_url( '/img/icon_school_high.png', GMOP_FILE ),
		) );


		// create objects
		$wpdb->insert( $wpdb->gmop_objects, array(
			'title' => __( 'Primary school', GMOP_TD ),
			'description' => 'Szkoła Podstawowa nr 211 im. J. Korczaka z oddziałami integracyjnymi Nowy Świat 21A Warszawa',
			'url' => 'https://www.sp211.edu.pl/',
			'latitude' => '52.232268',
			'longitude' => '21.020542',
			'marker' => '1',
		) );

		$wpdb->insert( $wpdb->gmop_objects, array(
			'title' => __( 'General secondary school', GMOP_TD ),
			'description' => 'Smolna 30, Warszawa',
			'url' => 'https://zamoyski.edu.pl/',
			'latitude' => '52.232938',
			'longitude' => '21.022811',
			'marker' => '2',
		) );

		$wpdb->insert( $wpdb->gmop_objects, array(
			'title' => __( 'Higher school of Journalism', GMOP_TD ),
			'description' => 'im. M. Wańkowicza Nowy Świat 58, Warszawa',
			'url' => 'https://www.wsd.edu.pl/',
			'latitude' => '52.235474',
			'longitude' => '21.018691',
			'marker' => '3',
		) );
	}

	/**
	 * Set initial options.
	 *
	 * @return void
	 */
	protected function set_initial_options() : void {
		update_option( 'gmop_api_key', '' );
		update_option( 'gmop_gmaps_loc', 'https://maps.google.pl' );
		update_option( 'gmop_default_latitude', '52.234528294213646' );
		update_option( 'gmop_default_longitude', '21.005859375' );
	}

}
