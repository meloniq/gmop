<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }



if($_GET['gmopaction'] == 'add'){
	gmop_object_type_add();
}else if($_GET['gmopaction'] == 'edit' && is_numeric($_GET['theid'])){
	gmop_object_type_edit();
}else{
	gmop_object_type_show();
}
?>

<?php
//BOF Show GMOP Object Types
function gmop_object_type_show(){
	global $wpdb;

	$typetable_name = $wpdb->prefix . "gmop_markers";
?>
	<div class="wrap">
		<h2><?php _e( 'GMOP Object Types', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_type_manage.php&gmopaction=add'; ?>" class="button add-new-h2"><?php _e( 'Add New', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You will find a list of already created object types (markers), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>
<?php
	//Handle deleting
	if ($_GET['gmopaction'] == "delete") {
		$theid = $_GET['theid'];
		echo '<div id="message" class="updated fade"><p>' . __( 'Are you sure you want to delete object type?', GMOP_TD ) . ' <a href="admin.php?page=/admin/object_type_manage.php&gmopaction=deleteconf&theid='.$theid.'">' . __( 'Yes', GMOP_TD ) . '</a> &nbsp; <a href="admin.php?page=/admin/object_type_manage.php">' . __( 'No!', GMOP_TD ) . '</a></p></div>';
	}
	if ($_GET['gmopaction'] == "deleteconf") {
		$theid = $_GET['theid'];
		$wpdb->query("DELETE FROM $typetable_name WHERE ID = '$theid'");
		echo '<div id="message" class="updated fade"><p>' . __( 'Object type deleted.', GMOP_TD ) . '</p></div>';
	}
	//Handle sort
	if ($_GET['gmopaction'] == "moveup") {
		$theid = $_GET['theid'];
		$wpdb->query("UPDATE $typetable_name SET priority = priority+1 WHERE ID = '$theid'");
		echo '<div id="message" class="updated fade"><p>' . __( 'Object type moved up.', GMOP_TD ) . '</p></div>';
	}
	if ($_GET['gmopaction'] == "movedown") {
		$theid = $_GET['theid'];
		$wpdb->query("UPDATE $typetable_name SET priority = priority-1 WHERE ID = '$theid'");
		echo '<div id="message" class="updated fade"><p>' . __( 'Object type moved down.', GMOP_TD ) . '</p></div>';
	}

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

	$gmoptypes = $wpdb->get_results("SELECT * FROM $typetable_name ORDER BY priority DESC", OBJECT);

	if ($gmoptypes) {
		foreach ($gmoptypes as $gmoptype){
			echo '<tr>';
			echo '<td>' . $gmoptype->ID . '</td>';
			echo '<td><strong>' . $gmoptype->title . '</strong></td>';
			echo '<td><img src="' . $gmoptype->image_url . '" /></td>';
			echo '<td><a href="admin.php?page=/admin/object_type_manage.php&gmopaction=moveup&theid='.$gmoptype->ID.'"><img src="/img/link_up.gif" title="' . __( 'Move Up', GMOP_TD ) . '" alt="' . __( 'Move Up', GMOP_TD ) . '" /></a>	<a href="admin.php?page=/admin/object_type_manage.php&gmopaction=movedown&theid='.$gmoptype->ID.'"><img src="/img/link_down.gif" title="' . __('Move Down', GMOP_TD ) . '" alt="' . __( 'Move Down', GMOP_TD ) . '" /></a></td>';
			echo '<td><a href="admin.php?page=/admin/object_type_manage.php&gmopaction=edit&theid='.$gmoptype->ID.'"><img src="/img/edit.png" title="' . __( 'Edit', GMOP_TD ) . '" alt="' . __( 'Edit', GMOP_TD ).'" /></a>	<a href="admin.php?page=/admin/object_type_manage.php&gmopaction=delete&theid='.$gmoptype->ID.'"><img src="/img/delete.png" title="' . __( 'Delete', GMOP_TD ) . '" alt="' . __( 'Delete', GMOP_TD ) . '" /></a></td>';
			echo '</tr>';
		}
	} else { 
		echo '<tr> <td colspan="5">' . __( 'No object types found.', GMOP_TD ) . '</td> </tr>'; 
	}

	echo '</tbody>
	</table>';
?>
	</div><!-- wrap ends -->
<?php
}
//EOF Show GMOP Object Types
?>



<?php
//BOF Add GMOP Object Type
function gmop_object_type_add(){
	global $wpdb;

	$typetable_name = $wpdb->prefix . "gmop_markers";
	//Insert new object type
	if ($_POST['Submit']) {
		$post_title = $wpdb->escape($_POST['gmop_title']);
		$post_image_url = $wpdb->escape($_POST['gmop_image_url']);
		if(!empty($post_title) && !empty($post_image_url)){
			$updatedb = "INSERT INTO $typetable_name (title, image_url) VALUES ('$post_title', '$post_image_url')";
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>' . __( 'New object type created.', GMOP_TD ) . '</p></div>';
		}else{
			echo '<div id="message" class="updated fade"><p>' . __( 'New object type was not created!', GMOP_TD ) . '</p></div>';
		}
	}
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
	<div class="wrap">
		<h2><?php _e( 'GMOP Add Object Type', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_type_manage.php'; ?>" class="button add-new-h2"><?php _e( 'Show Object Types', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You can create new object type (marker), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>

		<form method="post" action="<?php echo 'admin.php?page=/admin/object_type_manage.php&gmopaction=add'; ?>" enctype="multipart/form-data">
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
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Add New Object Type', GMOP_TD ); ?>" /> </p>
		</form>
	</div><!-- wrap ends -->
<?php
}
//EOF Add GMOP Object Type
?>



<?php
//BOF Edit GMOP Object Type
function gmop_object_type_edit(){
	global $wpdb;

	$typetable_name = $wpdb->prefix . "gmop_markers";
	//Update object type
	if ($_POST['Submit']) {
		$post_theid = $wpdb->escape($_POST['gmop_theid']);
		$post_title = $wpdb->escape($_POST['gmop_title']);
		$post_image_url = $wpdb->escape($_POST['gmop_image_url']);
		if(!empty($post_theid) && !empty($post_title) && !empty($post_image_url)){
			$updatedb = "UPDATE $typetable_name SET title = '$post_title', image_url = '$post_image_url' WHERE ID = '$post_theid'";
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>' . __( 'Object type updated.', GMOP_TD ) . '</p></div>';
		}else{
			echo '<div id="message" class="updated fade"><p>' . __( 'Object type was not updated!', GMOP_TD ) . '</p></div>';
		}
	}
	$theid = $_GET['theid'];
	$gmoptype = $wpdb->get_row("SELECT * FROM $typetable_name WHERE ID = '$theid'", OBJECT);

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
	<div class="wrap">
		<h2><?php _e( 'GMOP Edit Object Type', GMOP_TD ); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_type_manage.php'; ?>" class="button add-new-h2"><?php _e( 'Show Object Types', GMOP_TD ); ?></a></h2>
		<p class="admin-msg"><?php _e( 'Below You can edit already created object type (marker), which can be used to mark some objects on google map.', GMOP_TD ); ?></p>

		<form method="post" action="<?php echo 'admin.php?page=/admin/object_type_manage.php&gmopaction=edit&theid='.$theid.''; ?>" enctype="multipart/form-data">
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
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Edit Object Type', GMOP_TD ); ?>" /> </p>
		</form>
	</div><!-- wrap ends -->
<?php
}
//EOF Edit GMOP Object Type
?>
