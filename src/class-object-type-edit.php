<?php
namespace meloniq\GMOP;

class Object_Type_Edit {

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

		$this->save_form();
		$this->print_scripts();
		?>
		<h2><?php _e( 'GMOP Edit Object Type', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=gmop-object-types'; ?>" class="button add-new-h2"><?php _e( 'Show Object Types', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You can edit already created object type (marker), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>
		<?php
		$table_name = $wpdb->gmop_markers;
		$theid      = absint( $_GET['theid'] );
		$gmoptype   = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = '$theid'", OBJECT);
		if ( ! empty( $gmoptype ) ) {
			$this->display_form( $gmoptype );
		} else {
			echo '<div id="message" class="error fade"><p>' . __( 'Object type not found!', GMOP_TD ) . '</p></div>';
		}
		// TODO: Add nonce verification
	}

	/**
	 * Display form.
	 *
	 * @param object $gmoptype Object type.
	 *
	 * @return void
	 */
	protected function display_form( object $gmoptype ) : void {
		?>
		<form method="post" action="" enctype="multipart/form-data">
		<input name="gmop_theid" type="hidden" id="gmop_theid" value="<?php echo $gmoptype->ID; ?>" />
		<table class="widefat">
			<thead>
				<tr>
					<th width="200px" scope="col"><?php _e( 'Edit Object Type', GMOP_TD ); ?></th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Name', GMOP_TD ); ?></th>
					<td><input name="gmop_title" type="text" id="gmop_title" value="<?php echo $gmoptype->title; ?>" style="min-width:500px;" />
					<br /><small><?php _e( 'Give a name to this object type (eg. Bank, School, Library etc.).', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Image URL', GMOP_TD ); ?></th>
					<td><input name="gmop_image_url" type="text" id="gmop_image_url" value="<?php echo $gmoptype->image_url; ?>" style="min-width:398px;" />
					<input type="button" value="<?php _e( 'Add Image', GMOP_TD ); ?>" rel="gmop_image_url" class="upload_button button" id="upload_image_button">
					<br /><small><?php _e( 'Upload image or paste image url.', GMOP_TD ); ?></small>
					<div class="gmop_image_url_prev upload_image_preview" id="gmop_image_url_prev"><img src="<?php echo $gmoptype->image_url; ?>"></div></td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php _e( 'Edit Object Type', GMOP_TD ); ?>" /> </p>
		</form>
	<?php
	}


	/**
	 * Save form.
	 *
	 * @return void
	 */
	protected function save_form() : void {
		global $wpdb;

		if ( empty( $_POST['submit'] ) ) {
			return;
		}

		// Update object type
		$table_name = $wpdb->gmop_markers;
		$theid      = ! empty( $_POST['gmop_theid'] ) ? absint( $_POST['gmop_theid'] ) : 0;
		$title      = ! empty( $_POST['gmop_title'] ) ? sanitize_text_field( $_POST['gmop_title'] ) : '';
		$image_url  = ! empty( $_POST['gmop_image_url'] ) ? esc_url_raw( $_POST['gmop_image_url'] ) : '';

		if ( ! empty( $theid ) && ! empty( $title ) && ! empty( $image_url ) ) {
			// TODO: Prepare for SQL injection
			$query   = "UPDATE $table_name SET title = '$title', image_url = '$image_url' WHERE ID = '$theid'";
			$results = $wpdb->query( $query );
			$message = __( 'Object type updated.', GMOP_TD );
		} else {
			$message = __( 'Object type was not updated!', GMOP_TD );
		}
		echo '<div id="message" class="updated fade"><p>' . $message . '</p></div>';
	}

	/**
	 * Print scripts.
	 *
	 * @return void
	 */
	protected function print_scripts() : void {
		?>
		<script type="text/javascript">
		jQuery(function() {
			/* upload logo and images */
			jQuery('.upload_button').click(function() {
				formfield = jQuery(this).attr('rel');
				tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
				return false;
			});

			/* send the uploaded image url to the field */
			window.send_to_editor = function(html) {
				imgurl = jQuery('img',html).attr('src'); // get the image url
				imgoutput = '<img src="' + imgurl + '" />'; //get the html to output for the image preview
				jQuery('#' + formfield).val(imgurl);
				jQuery('#' + formfield).siblings('.upload_image_preview').slideDown().html(imgoutput);
				tb_remove();
			}
		});
		</script>
	<?php
	}

}
