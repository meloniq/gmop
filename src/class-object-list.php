<?php
namespace meloniq\GMOP;

// TODO: consider rewriting this class to use WP_List_Table
class Object_List {

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
		global $wpdb;

		$this->handle_actions();

		?>
		<h2><?php _e( 'GMOP Objects', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=gmop-objects&gmopaction=add'; ?>" class="button add-new-h2"><?php _e( 'Add New', GMOP_TD ); ?></a>
		&nbsp;<a href="<?php echo 'admin.php?page=gmop-objects&gmopaction=regenerate'; ?>" class="button add-new-h2"><?php _e( 'ReGenerate Cache', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You will find a list of already created objects, which can be show on google map.', GMOP_TD ); ?></p>
		<?php
		$gmopobjects = $this->get_objects();
		if ( ! empty( $gmopobjects ) ) {
			$this->display_pagination();

			$this->display_table( $gmopobjects );

			$this->display_pagination();
		} else {
			echo '<div id="message" class="error fade"><p>' . __( 'No objects found!', GMOP_TD ) . '</p></div>';
		}
	}

	/**
	 * Handle actions.
	 *
	 * @return void
	 */
	protected function handle_actions() : void {
		if ( empty( $_GET['gmopaction'] ) ) {
			return;
		}

		switch ( $_GET['gmopaction'] ) {
			case 'delete':
				$this->delete();
				break;
			case 'deleteconf':
				$this->delete( 'confirm' );
				break;
		}
	}

	/**
	 * Delete object.
	 *
	 * @param string $action Action.
	 *
	 * @return void
	 */
	protected function delete( string $action = '' ) : void {
		global $wpdb;

		if ( empty( $_GET['theid'] ) ) {
			return;
		}

		$table_name = $wpdb->gmop_objects;

		// Handle deleting
		if ( 'confirm' === $action ) {
			$theid = absint( $_GET['theid'] );
			$wpdb->query( "DELETE FROM $table_name WHERE ID = '$theid'" );
			echo '<div id="message" class="updated fade"><p>' . __( 'Object deleted.', GMOP_TD ) . '</p></div>';
		} else {
			$theid    = absint( $_GET['theid'] );
			$url_conf = 'admin.php?page=gmop-objects&gmopaction=deleteconf&theid=' . $theid;
			$yesno    = ' <a href="' . $url_conf . '">' . __( 'Yes', GMOP_TD ) . '</a> &nbsp; <a href="admin.php?page=gmop-objects">' . __( 'No!', GMOP_TD ) . '</a>';
			echo '<div id="message" class="updated fade"><p>' . __( 'Are you sure you want to delete object?', GMOP_TD ) . $yesno . '</p></div>';
		}
	}

	/**
	 * Display table.
	 *
	 * @param array $gmopobjects Objects.
	 *
	 * @return void
	 */
	protected function display_table( array $gmopobjects ) : void {
		$pageno = ! empty( $_GET['pageno'] ) ? absint( $_GET['pageno'] ) : 1;

		// TODO: use add_query_arg() instead of manual string concatenation
		$url_asc_id      = 'admin.php?page=gmop-objects&gmopsort=asc&gmopsortby=ID&pageno=' . $pageno;
		$url_desc_id     = 'admin.php?page=gmop-objects&gmopsort=desc&gmopsortby=ID&pageno=' . $pageno;
		$url_asc_title   = 'admin.php?page=gmop-objects&gmopsort=asc&gmopsortby=title&pageno=' . $pageno;
		$url_desc_title  = 'admin.php?page=gmop-objects&gmopsort=desc&gmopsortby=title&pageno=' . $pageno;
		$url_asc_marker  = 'admin.php?page=gmop-objects&gmopsort=asc&gmopsortby=marker&pageno=' . $pageno;
		$url_desc_marker = 'admin.php?page=gmop-objects&gmopsort=desc&gmopsortby=marker&pageno=' . $pageno;

		$img_asc    = '<img src="' . plugins_url( '/img/link_up.gif', GMOP_FILE ) . '" title="' . __( 'Ascending', GMOP_TD ) . '" alt="' . __( 'Ascending', GMOP_TD ) . '" />';
		$img_desc   = '<img src="' . plugins_url( '/img/link_down.gif', GMOP_FILE ) . '" title="' . __( 'Descending', GMOP_TD ) . '" alt="' . __( 'Descending', GMOP_TD ) . '" />';
		$img_edit   = '<img src="' . plugins_url( '/img/edit.png', GMOP_FILE ) . '" title="' . __( 'Edit', GMOP_TD ) . '" alt="' . __( 'Edit', GMOP_TD ) . '" />';
		$img_delete = '<img src="' . plugins_url( '/img/delete.png', GMOP_FILE ) . '" title="' . __( 'Delete', GMOP_TD ) . '" alt="' . __( 'Delete', GMOP_TD ) . '" />';

		echo '
		<table class="widefat">
			<thead><tr>
				<th scope="col">' . __( 'ID', GMOP_TD ) . ' <a href="' . $url_asc_id . '">' . $img_asc . '</a> <a href="' . $url_desc_id . '">' . $img_desc . '</a></th>
				<th scope="col">' . __( 'Name', GMOP_TD ) . ' <a href="' . $url_asc_title . '">' . $img_asc . '</a> <a href="' . $url_desc_title . '">' . $img_desc . '</a></th>
				<th scope="col" style="width:300px;">' . __( 'Description', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'URL', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'Marker', GMOP_TD ) . ' <a href="' . $url_asc_marker . '">' . $img_asc . '</a> <a href="' . $url_desc_marker . '">' . $img_desc . '</a></th>
				<th scope="col">' . __( 'Action', GMOP_TD ) . '</th>
			</tr></thead>
			<tbody>';

			$gmoptypes = $this->get_object_types();
			foreach ( $gmopobjects as $gmopobject ) {
				$url_edit   = 'admin.php?page=gmop-objects&gmopaction=edit&theid=' . $gmopobject->ID;
				$url_delete = 'admin.php?page=gmop-objects&gmopaction=delete&theid=' . $gmopobject->ID;
				$marker_id  = $gmopobject->marker;

				echo '<tr>';
				echo '<td>' . $gmopobject->ID . '</td>';
				echo '<td><strong>' . $gmopobject->title . '</strong></td>';
				echo '<td>' . $gmopobject->description . '</td>';
				echo '<td>' . $gmopobject->url . '</td>';
				echo '<td><img src="' . $gmoptypes[ $marker_id ]->image_url . '" /><br />' . $gmoptypes[ $marker_id ]->title . '</td>';
				echo '<td><a href="' . $url_edit . '">' . $img_edit . '</a> <a href="' . $url_delete . '">' . $img_delete . '</a></td>';
				echo '</tr>';
			}

		echo '</tbody>
		</table>';
	}

	/**
	 * Get object types.
	 *
	 * @return array
	 */
	protected function get_object_types() : array {
		global $wpdb;

		$table_name = $wpdb->gmop_markers;
		$gmoptypes  = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY ID ASC", OBJECT_K );

		return $gmoptypes;
	}

	/**
	 * Get objects.
	 *
	 * @return array
	 */
	protected function get_objects() : array {
		global $wpdb;

		$table_name = $wpdb->gmop_objects;

		$gmopsort = ! empty( $_GET['gmopsort'] ) ? $_GET['gmopsort'] : '';
		if ( $gmopsort === 'asc' ) {
			$sort = 'ASC';
		} else {
			$sort = 'DESC';
		}

		$gmopsortby = ! empty( $_GET['gmopsortby'] ) ? $_GET['gmopsortby'] : '';
		if ( $gmopsortby === 'marker') {
			$sortby	= 'marker';
		} else if ( $gmopsortby === 'title' ) {
			$sortby	= 'title';
		} else {
			$sortby	= 'ID';
		}

		$per_page = 20;
		$pageno = ! empty( $_GET['pageno'] ) ? absint( $_GET['pageno'] ) : 1;
		if ( $pageno < 2 ) {
			$pageno = 1;
			$pmin = 0;
			$pmax = $per_page;
		} else {
			$pmin = ( $pageno * $per_page ) - $per_page;
			$pmax = $pageno * $per_page;
		}

		$objects = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY $sortby $sort LIMIT $pmin , $pmax", OBJECT );

		return $objects;
	}

	/**
	 * Display pagination.
	 *
	 * @return void
	 */
	protected function display_pagination() : void {
		global $wpdb;

		$table_name = $wpdb->gmop_objects;

		$per_page = 20;
		$pageno = ! empty( $_GET['pageno'] ) ? absint( $_GET['pageno'] ) : 1;

		// TODO: use add_query_arg() instead of manual string concatenation
		$linkparam = 'admin.php?page=gmop-objects';
		if ( isset( $_GET['gmopsort'] ) ) {
			$linkparam .= '&gmopsort=' . $_GET['gmopsort'];
		}
		if ( isset( $_GET['gmopsortby'] ) ) {
			$linkparam .= '&gmopsortby=' . $_GET['gmopsortby'];
		}
		$linkparam .= '&pageno=';

		$objects_qty = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name;" );
		$max_pages = ceil( $objects_qty / $per_page );
		$pagination = '';
		for ( $i=1; $i<=$max_pages; $i++ ) {
			if ( $pageno == $i ) {
				$pagination .= '<a href="' . $linkparam . $i . '"><strong>' . $i . '</strong></a>&nbsp;';
			} else {
				$pagination .= '<a href="' . $linkparam . $i . '">' . $i . '</a>&nbsp;';
			}
		}
	?>
		<p><?php _e( 'Page:', GMOP_TD ); ?>&nbsp;<?php echo $pagination; ?></p>
	<?php
	}

}
