<?php
namespace meloniq\GMOP;

class Object_Cache {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Render page.
	 *
	 * @return void
	 */
	public function render_page() : void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'GMOP ReGenerate Objects Cache', GMOP_TD ); ?></h1>
			<p class="admin-msg"><?php _e( 'Please wait, generating cache of objects in progress...', GMOP_TD ); ?></p>
			<?php
			$message = __( 'Cache was updated!', GMOP_TD );
			$result  = $this->generate_cache_object();
			if ( ! $result ) {
				$message = __( 'Cache was not updated! No objects found!', GMOP_TD );
			}
			?>
			<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		</div>
		<?php
	}

	/**
	 * Generate Cache Object.
	 *
	 * @return bool
	 */
	public function generate_cache_object() : bool {
		global $wpdb;

		$objecttable_name = $wpdb->gmop_objects;
		$typetable_name   = $wpdb->gmop_markers;

		$gmopcount_total = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $objecttable_name;") );
		$gmopobjects = $wpdb->get_results( "SELECT * FROM $objecttable_name ORDER BY ID ASC", OBJECT );
		$gmopfilename = plugin_dir_path( GMOP_FILE ) . '/cache/data.json';

		if ( ! $gmopobjects ) {
			return false;
		}

		$gmopcontent = 'var data = { "count": ' . $gmopcount_total . ', "objects": [ ';
		foreach ( $gmopobjects as $gmopobject ) {
			$gmopcontent .= '{"object_id": ' . $gmopobject->ID . ', ';
			$gmopcontent .= '"object_title": "' . addslashes( $gmopobject->title ) . '", ';
			$gmopcontent .= '"object_desc": "' . addslashes( $gmopobject->description ) . '", ';
			$gmopcontent .= '"object_url": "' . $gmopobject->url . '", ';
			$gmopcontent .= '"object_latitude": "' . $gmopobject->latitude . '", ';
			$gmopcontent .= '"object_longitude": "' . $gmopobject->longitude . '", ';
			$gmopcontent .= '"object_marker_id": ' . $gmopobject->marker . ', ';
			$gmopcontent .= '"object_marker": "gmopgroup' . $gmopobject->marker . '"}, ';
		}

		// remove last comma
		$gmopcontent = substr( $gmopcontent, 0, -2 );
		$gmopcontent .= ' ]}';

		// TODO: store in wp-content/uploads/gmop
		$file = fopen( $gmopfilename,'w' ); # create new file for save, if file exist his previous content will be removed
		flock( $file, LOCK_EX );      # lock file
		fwrite( $file,$gmopcontent ); # save data to file
		flock( $file, LOCK_UN );      # unlock file
		fclose( $file );              # close file

		return true;
	}

}
