<?php
namespace meloniq\GMOP;

class Object_Type_Add {

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
		$this->save_form();
		$this->print_scripts();

		// TODO: Add nonce verification
		?>
		<h2><?php _e( 'GMOP Add Object Type', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=gmop-object-types'; ?>" class="button add-new-h2"><?php _e( 'Show Object Types', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You can create new object type (marker), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>

		<form method="post" action="" enctype="multipart/form-data">
		<table class="widefat">
			<thead>
				<tr>
					<th width="200px" scope="col"><?php _e( 'Add Object Type', GMOP_TD ); ?></th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Name', GMOP_TD ); ?></th>
					<td><input name="gmop_title" type="text" id="gmop_title" value="" style="min-width:500px;" />
					<br /><small><?php _e( 'Give a name to this object type (eg. Bank, School, Library etc.).', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Image URL', GMOP_TD ); ?></th>
					<td><input name="gmop_image_url" type="text" id="gmop_image_url" value="" style="min-width:398px;" />
					<input type="button" value="<?php _e( 'Add Image', GMOP_TD ); ?>" rel="gmop_image_url" class="upload_button button" id="upload_image_button">
					<br /><small><?php _e( 'Upload image or paste image url.', GMOP_TD ); ?></small>
					<div class="gmop_image_url_prev upload_image_preview" id="gmop_image_url_prev"><img src=""></div></td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php _e( 'Add New Object Type', GMOP_TD ); ?>" /> </p>
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

		// TODO: Verify nonce

		// Insert new object type
		$table_name = $wpdb->gmop_markers;
		$title      = ! empty( $_POST['gmop_title'] ) ? sanitize_text_field( $_POST['gmop_title'] ) : '';
		$image_url  = ! empty( $_POST['gmop_image_url'] ) ? esc_url_raw( $_POST['gmop_image_url'] ) : '';

		if ( ! empty( $title ) && ! empty( $image_url ) ) {
			// TODO: Prepare for SQL injection
			$query   = "INSERT INTO $table_name (title, image_url) VALUES ('$title', '$image_url')";
			$results = $wpdb->query( $query );
			$message = __( 'New object type created.', GMOP_TD );
		} else {
			$message = __( 'New object type was not created!', GMOP_TD );
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
