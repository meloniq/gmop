<?php
namespace meloniq\GMOP;

class Object_Add {

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
		$gmop_default_latitude = get_option( 'gmop_default_latitude' );
		$gmop_default_longitude = get_option( 'gmop_default_longitude' );
	?>
		<h2><?php _e( 'GMOP Add New Object', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=gmop-objects'; ?>" class="button add-new-h2"><?php _e( 'Show Objects', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You can create new object, which can be show on google map.', GMOP_TD ); ?></p>

		<form method="post" action="" enctype="multipart/form-data" name="storageform" id="storageform" >
		<table class="widefat">
			<thead>
				<tr>
					<th width="200px" scope="col"><?php _e( 'Add New Object', GMOP_TD ); ?></th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Name', GMOP_TD ); ?></th>
					<td><input name="gmop_title" type="text" id="gmop_title" value="" style="min-width:500px;" />
					<br /><small><?php _e( 'Give a name to this object (eg. Primary School no 112).', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Description', GMOP_TD ); ?></th>
					<td><textarea style="width:500px;height:150px;" id="gmop_description" name="gmop_description"></textarea>
					<br /><small><?php _e( 'Describe this object, write something more about it.', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Destination URL', GMOP_TD ); ?></th>
					<td><input name="gmop_url" type="text" id="gmop_url" value="" style="min-width:500px;" />
					<br /><small><?php _e( 'Provide a URL to website where user may find more information about this object.', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Latitude', GMOP_TD ); ?></th>
					<td><input name="gmop_latitude" type="text" id="gmop_latitude" value="<?php echo $gmop_default_latitude; ?>" style="min-width:500px;" />
					<br /><small><?php _e( 'Type here latitude of object or mark point on below map.', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Longitude', GMOP_TD ); ?></th>
					<td><input name="gmop_longitude" type="text" id="gmop_longitude" value="<?php echo $gmop_default_longitude; ?>" style="min-width:500px;" />
					<br /><small><?php _e( 'Type here longitude of object or mark point on below map.', GMOP_TD ); ?></small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Object type/Marker', GMOP_TD ); ?></th>
					<td>
				<?php
					$gmoptypes = $this->get_object_types();
					if ( $gmoptypes ) {
						echo '<ul>';
						foreach ( $gmoptypes as $gmoptype ) {
							echo '<li>';
							echo '<input type="radio" name="gmop_marker" value="' . $gmoptype->ID . '" /> ';
							echo '<img src="' . $gmoptype->image_url . '" /> ';
							echo $gmoptype->title;
							echo '</li>';
						}
						echo '</ul>';
					} else {
						_e( 'No object types found. Create some first', GMOP_TD );
					}
				?>
					<br /><small><?php _e( 'Choose one of available object types (markers).', GMOP_TD ); ?></small></td>
				</tr>
				<input type="hidden" value="1" class="check" id="lockcheck">
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button button-primary" value="<?php _e( 'Add New Object', GMOP_TD ); ?>" /> </p>
		</form>

		<div id="gmop_map"></div>
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

		// Insert new object
		$table_name  = $wpdb->gmop_objects;
		$title       = ! empty( $_POST['gmop_title'] ) ? sanitize_text_field( $_POST['gmop_title'] ) : '';
		$description = ! empty( $_POST['gmop_description'] ) ? sanitize_text_field( $_POST['gmop_description'] ) : '';
		$url         = ! empty( $_POST['gmop_url'] ) ? esc_url_raw( $_POST['gmop_url'] ) : '';
		$latitude    = ! empty( $_POST['gmop_latitude'] ) ? sanitize_text_field( $_POST['gmop_latitude'] ) : '';
		$longitude   = ! empty( $_POST['gmop_longitude'] ) ? sanitize_text_field( $_POST['gmop_longitude'] ) : '';
		$marker      = ! empty( $_POST['gmop_marker'] ) ? absint( $_POST['gmop_marker'] ) : '';

		if ( ! empty( $title ) && ! empty( $latitude ) && ! empty( $longitude ) && ! empty( $marker ) ) {
			// TODO: Prepare for SQL injection
			$query   = "INSERT INTO $table_name (title, description, url, latitude, longitude, marker) VALUES ('$title', '$description', '$url', '$latitude', '$longitude', '$marker')";
			$results = $wpdb->query( $query );
			$message = __( 'New object created.', GMOP_TD );
		} else {
			$message = __( 'New object was not created!', GMOP_TD );
		}
		echo '<div id="message" class="updated fade"><p>' . $message . '</p></div>';
	}

	/**
	 * Get object types.
	 *
	 * @return array
	 */
	protected function get_object_types() : array {
		global $wpdb;

		$table_name = $wpdb->gmop_markers;
		$types      = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY priority DESC", OBJECT );

		return $types;
	}


	/**
	 * Print scripts.
	 *
	 * @return void
	 */
	protected function print_scripts() : void {
		// TODO: Enqueue scripts
		$gmop_api_key   = get_option( 'gmop_api_key' );
		$gmop_gmaps_loc = get_option( 'gmop_gmaps_loc' );
		echo '<script src="' . $gmop_gmaps_loc . '/maps?file=api&amp;v=2&amp;key=' . $gmop_api_key . '" type="text/javascript"></script>';

		$gmop_default_latitude = get_option( 'gmop_default_latitude' );
		$gmop_default_longitude = get_option( 'gmop_default_longitude' );
		// TODO: Move to separate file, php params pass via wp_localize_script
		?>
		<script type="text/javascript">
		//<![CDATA[
		var newmap;
		var js_logged_in = false;

		Function.prototype.method = function (name, func) {
			this.prototype[name] = func;
			return this;
		};

		function mapmaker () {
			this.map;
			this.icon0;
			this.html;
			this.newpoints = new Array();
			this.overlay;
			this.point;
			this.editmode = false;
			this.editnum;
			this.originalLat;
			this.originalLng;
			this.savedmap = false;
		};

		function init () {
			newmap = new mapmaker();
			newmap.Create();

			newmap.addpoint("<?php echo $gmop_default_latitude; ?>","<?php echo $gmop_default_longitude; ?>","<?php _e( 'Move marker', GMOP_TD ); ?>","<?php _e( 'Move this marker to right place', GMOP_TD ); ?>");
			newmap.editMarker(0);
		};

		mapmaker.method('Create', function () {
			this.map = new GMap2(document.getElementById("gmop_map"), {draggableCursor: 'arrow', draggingCursor: 'arrow'});
			this.map.setCenter(new GLatLng(<?php echo $gmop_default_latitude; ?>,<?php echo $gmop_default_longitude; ?>), 8);
			this.map.addControl(new GLargeMapControl());
			this.map.addControl(new GMapTypeControl());

			this.map.setMapType(G_NORMAL_MAP);

			GEvent.bind(this.map, "click", this,this.onClick);
			this.icon0 = new GIcon();
			this.icon0.image = "https://www.google.com/mapfiles/marker.png";
			this.icon0.shadow = "https://www.google.com/mapfiles/shadow50.png";
			this.icon0.iconSize = new GSize(20, 34);
			this.icon0.shadowSize = new GSize(37, 34);
			this.icon0.iconAnchor = new GPoint(9, 34);
			this.icon0.infoWindowAnchor = new GPoint(9, 2);
			this.icon0.infoShadowAnchor = new GPoint(18, 25);
		});

		mapmaker.method('onClick', function(overlay, point) {
			if (this.editmode == false) {
				if(document.getElementById("lockcheck").checked == true || document.getElementById("lockcheck").value==1) {
					document.getElementById("gmop_longitude").value = point.x;
					document.getElementById("gmop_latitude").value = point.y;
				} else {
					if (overlay != null) return;
					if (this.map.getZoom() < 17) this.map.setCenter(point, this.map.getZoom() + 1 );
				}
			} else {
				document.getElementById("gmop_longitude").value = point.x;
				document.getElementById("gmop_latitude").value = point.y;
				var num = this.editnum;
				newmap.moveMarker(num);
			}
		});

		mapmaker.method('moveMarker', function(num) {
			this.newpoints[num][0] = document.forms.storageform.gmop_latitude.value;
			this.newpoints[num][1] = document.forms.storageform.gmop_longitude.value;
			if(num > -1) {
				return this.newpoints[num][4].setPoint(new GLatLng(this.newpoints[num][0],this.newpoints[num][1]));
			}
		});

		mapmaker.method('editMarker', function(num) {
			if(newmap.editmode != true) {
				this.originalLat = this.newpoints[num][0];
				this.originalLng = this.newpoints[num][1];
				this.map.setCenter(new GLatLng(this.newpoints[num][0],this.newpoints[num][1]),this.map.getZoom());
				//populate form fields
				document.forms.storageform.gmop_latitude.value = this.newpoints[num][0];
				document.forms.storageform.gmop_longitude.value = this.newpoints[num][1];
				newmap.map.closeInfoWindow();
				this.editmode = true;
				this.editnum = num;
			}
		});

		mapmaker.method('addpoint', function(gmop_latitude,gmop_longitude,name,stuff) {
			var point = new GPoint(gmop_longitude,gmop_latitude);
			//Add overlay to map
			var marker = newmap.createMarker(point,this.icon0,stuff);
			this.map.addOverlay(marker);
			var newpoint = new Array(gmop_latitude,gmop_longitude,name,stuff,marker);
			this.newpoints[this.newpoints.length] = newpoint;
			return false;
		});

		mapmaker.method('createMarker', function(point, icon, stuff) {
			var html = '<div id="popup">' + stuff + '<\/div>';
			var marker = new GMarker(point, icon);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(html);
			});
			return marker;
		});

		function addLoadEvent(func) {
			var oldonload = window.onload;
			if (typeof window.onload != 'function'){
				window.onload = func
			} else {
				window.onload = function() {
					oldonload();
					func();
				}
			}
		}

		addLoadEvent(init);
		//]]>
		</script>
		<style>
			div#gmop_map {
				border: 3px solid #BBBBBB;
				height: 400px;
			}
		</style>
	<?php
	}


}
