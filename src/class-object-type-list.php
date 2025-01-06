<?php
namespace meloniq\GMOP;

// TODO: consider rewriting this class to use WP_List_Table
class Object_Type_List {

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
		<h2><?php _e( 'GMOP Object Types', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=gmop-object-types&gmopaction=add'; ?>" class="button add-new-h2"><?php _e( 'Add New', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You will find a list of already created object types (markers), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>
		<?php
		$table_name = $wpdb->gmop_markers;
		$gmoptypes  = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY priority DESC", OBJECT );
		if ( ! empty( $gmoptypes ) ) {
			$this->display_table( $gmoptypes );
		} else {
			echo '<div id="message" class="error fade"><p>' . __( 'No object types found!', GMOP_TD ) . '</p></div>';
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
			case 'moveup':
				$this->move_up();
				break;
			case 'movedown':
				$this->move_down();
				break;
			}
	}

	/**
	 * Delete object type.
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

		$table_name = $wpdb->gmop_markers;

		if ( 'confirm' === $action ) {
			$theid = absint( $_GET['theid'] );
			$wpdb->query( "DELETE FROM $table_name WHERE ID = '$theid'" );
			echo '<div id="message" class="updated fade"><p>' . __( 'Object type deleted.', GMOP_TD ) . '</p></div>';
		} else {
			$theid    = absint( $_GET['theid'] );
			$url_conf = 'admin.php?page=gmop-object-types&gmopaction=deleteconf&theid=' . $theid;
			$yesno    = ' <a href="' . $url_conf . '">' . __( 'Yes', GMOP_TD ) . '</a> &nbsp; <a href="admin.php?page=gmop-object-types">' . __( 'No!', GMOP_TD ) . '</a>';
			echo '<div id="message" class="updated fade"><p>' . __( 'Are you sure you want to delete object type?', GMOP_TD ) . $yesno . '</p></div>';
		}
	}

	/**
	 * Move object type up.
	 *
	 * @return void
	 */
	protected function move_up() : void {
		global $wpdb;

		if ( empty( $_GET['theid'] ) ) {
			return;
		}

		$table_name = $wpdb->gmop_markers;
		$theid      = absint( $_GET['theid'] );
		$wpdb->query( "UPDATE $table_name SET priority = priority+1 WHERE ID = '$theid'" );
		echo '<div id="message" class="updated fade"><p>' . __( 'Object type moved up.', GMOP_TD ) . '</p></div>';
	}

	/**
	 * Move object type down.
	 *
	 * @return void
	 */
	protected function move_down() : void {
		global $wpdb;

		if ( empty( $_GET['theid'] ) ) {
			return;
		}

		$table_name = $wpdb->gmop_markers;
		$theid      = absint( $_GET['theid'] );
		$wpdb->query( "UPDATE $table_name SET priority = priority-1 WHERE ID = '$theid'" );
		echo '<div id="message" class="updated fade"><p>' . __( 'Object type moved down.', GMOP_TD ) . '</p></div>';
	}

	/**
	 * Display table.
	 *
	 * @param array $gmoptypes Object types.
	 *
	 * @return void
	 */
	protected function display_table( array $gmoptypes ) : void {
		$img_edit     = '<img src="' . plugins_url( '/img/edit.png', GMOP_FILE ) . '" title="' . __( 'Edit', GMOP_TD ) . '" alt="' . __( 'Edit', GMOP_TD ).'" />';
		$img_delete   = '<img src="' . plugins_url( '/img/delete.png', GMOP_FILE ) . '" title="' . __( 'Delete', GMOP_TD ) . '" alt="' . __( 'Delete', GMOP_TD ) . '" />';
		$img_moveup   = '<img src="' . plugins_url( '/img/link_up.gif', GMOP_FILE ) . '" title="' . __( 'Move Up', GMOP_TD ) . '" alt="' . __( 'Move Up', GMOP_TD ) . '" />';
		$img_movedown = '<img src="' . plugins_url( '/img/link_down.gif', GMOP_FILE ) . '" title="' . __('Move Down', GMOP_TD ) . '" alt="' . __( 'Move Down', GMOP_TD ) . '" />';

		echo '
		<table class="widefat">
			<thead><tr>
				<th scope="col">' . __( 'ID', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'Name', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'Image', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'Order', GMOP_TD ) . '</th>
				<th scope="col">' . __( 'Action', GMOP_TD ) . '</th>
			</tr></thead>
			<tbody>';

			foreach ( $gmoptypes as $gmoptype ) {
				$url_edit     = 'admin.php?page=gmop-object-types&gmopaction=edit&theid=' . $gmoptype->ID;
				$url_delete   = 'admin.php?page=gmop-object-types&gmopaction=delete&theid=' . $gmoptype->ID;
				$url_moveup   = 'admin.php?page=gmop-object-types&gmopaction=moveup&theid=' . $gmoptype->ID;
				$url_movedown = 'admin.php?page=gmop-object-types&gmopaction=movedown&theid=' . $gmoptype->ID;

				echo '<tr>';
				echo '<td>' . $gmoptype->ID . '</td>';
				echo '<td><strong>' . $gmoptype->title . '</strong></td>';
				echo '<td><img src="' . $gmoptype->image_url . '" /></td>';
				echo '<td><a href="' . $url_moveup . '">' . $img_moveup . '</a> <a href="' . $url_movedown . '">' . $img_movedown . '</a></td>';
				echo '<td><a href="' . $url_edit . '">' . $img_edit . '</a>	<a href="' . $url_delete . '">' . $img_delete . '</a></td>';
				echo '</tr>';
			}

		echo '</tbody>
		</table>';
	}


}
