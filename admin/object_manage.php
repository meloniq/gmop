<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


if($_GET['gmopaction'] == 'add'){
	gmop_object_add();
}else if($_GET['gmopaction'] == 'edit' && is_numeric($_GET['theid'])){
	gmop_object_edit();
}else if($_GET['gmopaction'] == 'regenerate'){
	gmop_object_regenerate();
}else{
	gmop_object_show();
}
?>

<?php
//BOF Show GMOP Objects
function gmop_object_show(){
	global $wpdb;

	$objecttable_name = $wpdb->prefix . "gmop_objects";
	$typetable_name = $wpdb->prefix . "gmop_markers";

	if($_GET['gmopsort'] == 'asc'){
		$gmopsort	= "ASC";
	}else{
		$gmopsort	= "DESC";
	}

	if($_GET['gmopsortby'] == 'marker'){
		$gmopsortby	= "marker";
	}else if($_GET['gmopsortby'] == 'title'){
		$gmopsortby	= "title";
	}else{
		$gmopsortby	= "ID";
	}

	$itemsonpage = 20;
	$pageno = $wpdb->escape($_GET['pageno']);
	if( ($pageno < 2) || (!is_numeric($pageno)) ){
		$pageno = 1;
		$pmin = 0;
		$pmax = $itemsonpage;
	}else{
		$pmin = ($pageno * $itemsonpage) - $itemsonpage;
		$pmax = $pageno * $itemsonpage;
	}

	$linkparam = 'admin.php?page=/admin/object_manage.php';
	if(isset($_GET['gmopsort'])){
		$linkparam .= '&gmopsort='.$_GET['gmopsort'];
	}
	if(isset($_GET['gmopsortby'])){
		$linkparam .= '&gmopsortby='.$_GET['gmopsortby'];
	}
	$linkparam .= '&pageno=';
	
	$gmopcount_total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $objecttable_name;"));
	$gmopcount = ceil($gmopcount_total / $itemsonpage);
	$pagination = '';
	for($i=1; $i<=$gmopcount; $i++){
		if($pageno == $i){
			$pagination .= '<a href="' . $linkparam . $i . '"><strong>'.$i.'</strong></a>&nbsp;';
		}else{
			$pagination .= '<a href="' . $linkparam . $i . '">'.$i.'</a>&nbsp;';
		}
	}
?>
	<div class="wrap">
		<h2><?php _e('GMOP Objects','mnet-gmop'); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_manage.php&gmopaction=add'; ?>" class="button add-new-h2"><?php _e('Add New','mnet-gmop'); ?></a>
		&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_manage.php&gmopaction=regenerate'; ?>" class="button add-new-h2"><?php _e('ReGenerate Cache','mnet-gmop'); ?></a></h2>
		<p class="admin-msg"><?php _e('Below You will find a list of already created objects, which can be show on google map.','mnet-gmop'); ?></p>
		<p><?php _e('Page:','mnet-gmop'); ?>&nbsp;<?php echo $pagination; ?></p>
<?php
	//Handle deleting
	if ($_GET['gmopaction'] == "delete") {
		$theid = $_GET['theid'];
		echo '<div id="message" class="updated fade"><p>'.__('Are you sure you want to delete object?', 'mnet-gmop').' <a href="admin.php?page=/admin/object_manage.php&gmopaction=deleteconf&theid='.$theid.'">'.__('Yes', 'mnet-gmop').'</a> &nbsp; <a href="admin.php?page=/admin/object_manage.php">'.__('No!', 'mnet-gmop').'</a></p></div>';
	}
	if ($_GET['gmopaction'] == "deleteconf") {
		$theid = $_GET['theid'];
		$wpdb->query("DELETE FROM $objecttable_name WHERE ID = '$theid'");
		echo '<div id="message" class="updated fade"><p>' . __('Object deleted.', 'mnet-gmop') . '</p></div>';
	}

	$gmopobjects = $wpdb->get_results("SELECT * FROM $objecttable_name ORDER BY $gmopsortby $gmopsort LIMIT $pmin , $pmax", OBJECT);
	$gmoptypes = $wpdb->get_results("SELECT * FROM $typetable_name ORDER BY ID ASC", OBJECT_K);

	echo '
	<table class="widefat">
		<thead><tr>
			<th scope="col">' . __('ID', 'mnet-gmop') . ' <a href="admin.php?page=/admin/object_manage.php&gmopsort=asc&gmopsortby=ID&pageno='.$pageno.'"><img src="/img/link_up.gif" title="'.__('Ascending', 'mnet-gmop').'" alt="'.__('Ascending', 'mnet-gmop').'" /></a>	<a href="admin.php?page=/admin/object_manage.php&gmopsort=desc&gmopsortby=ID&pageno='.$pageno.'"><img src="/img/link_down.gif" title="'.__('Descending', 'mnet-gmop').'" alt="'.__('Descending', 'mnet-gmop').'" /></a></th>
			<th scope="col">' . __('Name', 'mnet-gmop') . ' <a href="admin.php?page=/admin/object_manage.php&gmopsort=asc&gmopsortby=title&pageno='.$pageno.'"><img src="/img/link_up.gif" title="'.__('Ascending', 'mnet-gmop').'" alt="'.__('Ascending', 'mnet-gmop').'" /></a>	<a href="admin.php?page=/admin/object_manage.php&gmopsort=desc&gmopsortby=title&pageno='.$pageno.'"><img src="/img/link_down.gif" title="'.__('Descending', 'mnet-gmop').'" alt="'.__('Descending', 'mnet-gmop').'" /></a></th>
			<th scope="col" style="width:300px;">' . __('Description', 'mnet-gmop') . '</th>
			<th scope="col">' . __('URL', 'mnet-gmop') . '</th>
			<th scope="col">' . __('Marker', 'mnet-gmop') . ' <a href="admin.php?page=/admin/object_manage.php&gmopsort=asc&gmopsortby=marker&pageno='.$pageno.'"><img src="/img/link_up.gif" title="'.__('Ascending', 'mnet-gmop').'" alt="'.__('Ascending', 'mnet-gmop').'" /></a>	<a href="admin.php?page=/admin/object_manage.php&gmopsort=desc&gmopsortby=marker&pageno='.$pageno.'"><img src="/img/link_down.gif" title="'.__('Descending', 'mnet-gmop').'" alt="'.__('Descending', 'mnet-gmop').'" /></a></th>
			<th scope="col">' . __('Action', 'mnet-gmop') . '</th>
		</tr></thead>
		<tbody>';

	if ($gmopobjects) {
		foreach ($gmopobjects as $gmopobject){
			echo '<tr>';
			echo '<td>' . $gmopobject->ID . '</td>';
			echo '<td><strong>' . $gmopobject->title . '</strong></td>';
			echo '<td>' . $gmopobject->description . '</td>';
			echo '<td>' . $gmopobject->url . '</td>';
			$markerid = $gmopobject->marker;
			echo '<td><img src="' . $gmoptypes[$markerid]->image_url . '" /><br />' . $gmoptypes[$markerid]->title . '</td>';
			echo '<td><a href="admin.php?page=/admin/object_manage.php&gmopaction=edit&theid='.$gmopobject->ID.'"><img src="/img/edit.png" title="'.__('Edit', 'mnet-gmop').'" alt="'.__('Edit', 'mnet-gmop').'" /></a>	<a href="admin.php?page=/admin/object_manage.php&gmopaction=delete&theid='.$gmopobject->ID.'"><img src="/img/delete.png" title="'.__('Delete', 'mnet-gmop').'" alt="'.__('Delete', 'mnet-gmop').'" /></a></td>';
			echo '</tr>';
		}
	} else { 
		echo '<tr> <td colspan="6">'.__('No objects found.', 'mnet-gmop').'</td> </tr>'; 
	}

	echo '</tbody>
	</table>';
?>
		<p><?php _e('Page:','mnet-gmop'); ?>&nbsp;<?php echo $pagination; ?></p>
	</div><!-- wrap ends -->
<?php
}
//EOF Show GMOP Objects
?>



<?php
//BOF Add GMOP Object
function gmop_object_add(){
	global $wpdb;

	$objecttable_name = $wpdb->prefix . "gmop_objects";
	$typetable_name = $wpdb->prefix . "gmop_markers";
	//Insert new object
	if ($_POST['Submit']) {
		$post_title = $wpdb->escape($_POST['gmop_title']);
		$post_description = $wpdb->escape($_POST['gmop_description']);
		$post_url = $wpdb->escape($_POST['gmop_url']);
		$post_latitude = $wpdb->escape($_POST['gmop_latitude']);
		$post_longitude = $wpdb->escape($_POST['gmop_longitude']);
		$post_marker = $wpdb->escape($_POST['gmop_marker']);
		if(!empty($post_title) && !empty($post_latitude) && !empty($post_longitude) && !empty($post_marker)){
			$updatedb = "INSERT INTO $objecttable_name (title, description, url, latitude, longitude, marker) VALUES ('$post_title', '$post_description', '$post_url', '$post_latitude', '$post_longitude', '$post_marker')";
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>' . __('New object created.','mnet-gmop') . '</p></div>';
		}else{
			echo '<div id="message" class="updated fade"><p>' . __('New object was not created!','mnet-gmop') . '</p></div>';
		}
	}

	$gmoptypes = $wpdb->get_results("SELECT * FROM $typetable_name ORDER BY priority DESC", OBJECT);

	$gmop_api_key = get_option('gmop_api_key');
	$gmop_gmaps_loc = get_option('gmop_gmaps_loc');
	$gmop_default_latitude = get_option('gmop_default_latitude');
	$gmop_default_longitude = get_option('gmop_default_longitude');

	echo '<script src="'.$gmop_gmaps_loc.'/maps?file=api&amp;v=2&amp;key='.$gmop_api_key.'" type="text/javascript"></script>';

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

	newmap.addpoint("<?php echo $gmop_default_latitude; ?>","<?php echo $gmop_default_longitude; ?>","<?php _e('Move marker','mnet-gmop'); ?>","<?php _e('Move this marker to right place','mnet-gmop'); ?>");
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
	this.icon0.image = "http://www.google.com/mapfiles/marker.png";
	this.icon0.shadow = "http://www.google.com/mapfiles/shadow50.png";
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
	<div class="wrap">
		<h2><?php _e('GMOP Add New Object','mnet-gmop'); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_manage.php'; ?>" class="button add-new-h2"><?php _e('Show Objects','mnet-gmop'); ?></a></h2>
		<p class="admin-msg"><?php _e('Below You can create new object, which can be show on google map.','mnet-gmop'); ?></p>

		<form method="post" action="<?php echo 'admin.php?page=/admin/object_manage.php&gmopaction=add'; ?>" enctype="multipart/form-data" name="storageform" id="storageform" >
		<table class="widefat">
			<thead>
				<tr>
					<th width="200px" scope="col"><?php _e('Add New Object','mnet-gmop'); ?></th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>

			<tr>
				<th scope="row"><?php _e('Name', 'mnet-gmop'); ?></th>
				<td><input name="gmop_title" type="text" id="gmop_title" value="" style="min-width:500px;" />
				<br /><small><?php _e('Give a name to this object (eg. Primary School no 112).', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Description', 'mnet-gmop'); ?></th>
				<td><textarea style="width:500px;height:150px;" id="gmop_description" name="gmop_description"></textarea>
				<br /><small><?php _e('Describe this object, write something more about it.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Destination URL', 'mnet-gmop'); ?></th>
				<td><input name="gmop_url" type="text" id="gmop_url" value="" style="min-width:500px;" />
				<br /><small><?php _e('Provide a URL to website where user may find more information about this object.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Latitude', 'mnet-gmop'); ?></th>
				<td><input name="gmop_latitude" type="text" id="gmop_latitude" value="<?php echo $gmop_default_latitude; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Type here latitude of object or mark point on below map.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Longitude', 'mnet-gmop'); ?></th>
				<td><input name="gmop_longitude" type="text" id="gmop_longitude" value="<?php echo $gmop_default_longitude; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Type here longitude of object or mark point on below map.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Object type/Marker', 'mnet-gmop'); ?></th>
				<td>
<?php
	if ($gmoptypes) {
		echo '<ul>';
		foreach ($gmoptypes as $gmoptype){
			echo '<li>';
			echo '<input type="radio" name="gmop_marker"	value="' . $gmoptype->ID . '" /> ';
			echo '<img src="' . $gmoptype->image_url . '" /> ';
			echo $gmoptype->title;
			echo '</li>';
		}
		echo '</ul>';
	} else { 
		_e('No object types found. Create some first', 'mnet-gmop'); 
	}
?>
				<br /><small><?php _e('Choose one of available object types (markers).', 'mnet-gmop'); ?></small></td>
			</tr>

			<input type="hidden" value="1" class="check" id="lockcheck">

			</tbody>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Add New Object', 'mnet-gmop'); ?>" /> </p>
		</form>

			<div id="gmop_map"></div>

	</div><!-- wrap ends -->
<?php
}
//EOF Add GMOP Object
?>



<?php
//BOF Edit GMOP Object
function gmop_object_edit(){
	global $wpdb;

	$objecttable_name = $wpdb->prefix . "gmop_objects";
	$typetable_name = $wpdb->prefix . "gmop_markers";
	//Update object
	if ($_POST['Submit']) {
		$post_theid = $wpdb->escape($_POST['gmop_theid']);
		$post_title = $wpdb->escape($_POST['gmop_title']);
		$post_description = $wpdb->escape($_POST['gmop_description']);
		$post_url = $wpdb->escape($_POST['gmop_url']);
		$post_latitude = $wpdb->escape($_POST['gmop_latitude']);
		$post_longitude = $wpdb->escape($_POST['gmop_longitude']);
		$post_marker = $wpdb->escape($_POST['gmop_marker']);
		if(!empty($post_theid) && !empty($post_title) && !empty($post_latitude) && !empty($post_longitude) && !empty($post_marker)){
			$updatedb = "UPDATE $objecttable_name SET title = '$post_title', description = '$post_description', url = '$post_url', latitude = '$post_latitude', longitude = '$post_longitude', marker = '$post_marker'	WHERE ID = '$post_theid'";
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>' . __('Object updated.','mnet-gmop') . '</p></div>';
		}else{
			echo '<div id="message" class="updated fade"><p>' . __('Object was not updated!','mnet-gmop') . '</p></div>';
		}
	}

	$gmoptypes = $wpdb->get_results("SELECT * FROM $typetable_name ORDER BY priority DESC", OBJECT);
	$theid = $_GET['theid'];
	$gmopobject = $wpdb->get_row("SELECT * FROM $objecttable_name WHERE ID = '$theid'", OBJECT);

	$gmop_api_key = get_option('gmop_api_key');
	$gmop_gmaps_loc = get_option('gmop_gmaps_loc');
	echo '<script src="'.$gmop_gmaps_loc.'/maps?file=api&amp;v=2&amp;key='.$gmop_api_key.'" type="text/javascript"></script>';
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

	newmap.addpoint("<?php echo $gmopobject->latitude; ?>","<?php echo $gmopobject->longitude; ?>","<?php _e('Move marker','mnet-gmop'); ?>","<?php _e('Move this marker to right place','mnet-gmop'); ?>");
	newmap.editMarker(0);
};



mapmaker.method('Create', function () {
	this.map = new GMap2(document.getElementById("gmop_map"), {draggableCursor: 'arrow', draggingCursor: 'arrow'});
	this.map.setCenter(new GLatLng(<?php echo $gmopobject->latitude; ?>,<?php echo $gmopobject->longitude; ?>), 13);
	this.map.addControl(new GLargeMapControl());
	this.map.addControl(new GMapTypeControl());

	this.map.setMapType(G_NORMAL_MAP);

	GEvent.bind(this.map, "click", this,this.onClick);
	this.icon0 = new GIcon();
	this.icon0.image = "http://www.google.com/mapfiles/marker.png";
	this.icon0.shadow = "http://www.google.com/mapfiles/shadow50.png";
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
	<div class="wrap">
		<h2><?php _e('GMOP Edit Object','mnet-gmop'); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_manage.php'; ?>" class="button add-new-h2"><?php _e('Show Objects','mnet-gmop'); ?></a></h2>
		<p class="admin-msg"><?php _e('Below You can edit object, which can be show on google map.','mnet-gmop'); ?></p>

		<form method="post" action="<?php echo 'admin.php?page=/admin/object_manage.php&gmopaction=edit&theid='.$theid.''; ?>" enctype="multipart/form-data" name="storageform" id="storageform" >
		<input name="gmop_theid" type="hidden" id="gmop_theid" value="<?php echo $gmopobject->ID; ?>" />
		<table class="widefat">
			<thead>
				<tr>
					<th width="200px" scope="col"><?php _e('Edit Object','mnet-gmop'); ?></th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>

			<tr>
				<th scope="row"><?php _e('Name', 'mnet-gmop'); ?></th>
				<td><input name="gmop_title" type="text" id="gmop_title" value="<?php echo $gmopobject->title; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Give a name to this object (eg. Primary School no 112).', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Description', 'mnet-gmop'); ?></th>
				<td><textarea style="width:500px;height:150px;" id="gmop_description" name="gmop_description"><?php echo $gmopobject->description; ?></textarea>
				<br /><small><?php _e('Describe this object, write something more about it.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Destination URL', 'mnet-gmop'); ?></th>
				<td><input name="gmop_url" type="text" id="gmop_url" value="<?php echo $gmopobject->url; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Provide a URL to website where user may find more information about this object.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Latitude', 'mnet-gmop'); ?></th>
				<td><input name="gmop_latitude" type="text" id="gmop_latitude" value="<?php echo $gmopobject->latitude; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Type here latitude of object or mark point on below map.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Longitude', 'mnet-gmop'); ?></th>
				<td><input name="gmop_longitude" type="text" id="gmop_longitude" value="<?php echo $gmopobject->longitude; ?>" style="min-width:500px;" />
				<br /><small><?php _e('Type here longitude of object or mark point on below map.', 'mnet-gmop'); ?></small></td>
			</tr>

			<tr>
				<th scope="row"><?php _e('Object type/Marker', 'mnet-gmop'); ?></th>
				<td>
<?php
	if ($gmoptypes) {
		echo '<ul>';
		foreach ($gmoptypes as $gmoptype){
			if( $gmoptype->ID == $gmopobject->marker ){ $gmop_checked = 'checked="checked"'; } else { $gmop_checked = ''; }
			echo '<li>';
			echo '<input type="radio" name="gmop_marker"	value="' . $gmoptype->ID . '" '.$gmop_checked.' /> ';
			echo '<img src="' . $gmoptype->image_url . '" /> ';
			echo $gmoptype->title;
			echo '</li>';
		}
		echo '</ul>';
	} else { 
		_e('No object types found. Create some first', 'mnet-gmop'); 
	}
?>
				<br /><small><?php _e('Choose one of available object types (markers).', 'mnet-gmop'); ?></small></td>
			</tr>

			<input type="hidden" value="1" class="check" id="lockcheck">

			</tbody>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Object', 'mnet-gmop'); ?>" /> </p>
		</form>

			<div id="gmop_map"></div>

	</div><!-- wrap ends -->
<?php
}
//EOF Edit GMOP Object
?>


<?php
//BOF ReGenerate GMOP Object
function gmop_object_regenerate(){
	global $wpdb;

	$objecttable_name = $wpdb->prefix . "gmop_objects";
	$typetable_name = $wpdb->prefix . "gmop_markers";

?>
	<div class="wrap">
		<h2><?php _e('GMOP ReGenerate Objects Cache','mnet-gmop'); ?>&nbsp;<a href="<?php echo 'admin.php?page=/admin/object_manage.php'; ?>" class="button add-new-h2"><?php _e('Show Objects','mnet-gmop'); ?></a></h2>
		<p class="admin-msg"><?php _e('Please wait, generating cache of objects in progress...','mnet-gmop'); ?></p>
<?php

	$gmopcount_total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $objecttable_name;"));
	$gmopobjects = $wpdb->get_results("SELECT * FROM $objecttable_name ORDER BY ID ASC", OBJECT);
	$gmopfilename = '/cache/data.json';

	if ($gmopobjects) {
		$gmopcontent = 'var data = { "count": ' . $gmopcount_total . ', "objects": [ ';
		foreach ($gmopobjects as $gmopobject){
			$gmopcontent .= '{"object_id": ' . $gmopobject->ID . ', ';
			$gmopcontent .= '"object_title": "' . addslashes($gmopobject->title) . '", ';
			$gmopcontent .= '"object_desc": "' . addslashes($gmopobject->description) . '", ';
			$gmopcontent .= '"object_url": "' . $gmopobject->url . '", ';
			$gmopcontent .= '"object_latitude": "' . $gmopobject->latitude . '", ';
			$gmopcontent .= '"object_longitude": "' . $gmopobject->longitude . '", ';
			$gmopcontent .= '"object_marker_id": ' . $gmopobject->marker . ', ';
			$gmopcontent .= '"object_marker": "gmopgroup' . $gmopobject->marker . '"}, ';
		}
		$gmopcontent = substr($gmopcontent, 0, -2);
		$gmopcontent .= ' ]}';

		$file = fopen($gmopfilename,'w'); # create new file for save, if file exist his previous content will be removed
		flock($file, LOCK_EX);				# lock file
		fwrite($file,$gmopcontent);				 # save data to file
		flock($file, LOCK_UN);				# unlock file
		fclose($file);								# close file

		//echo $gmopfilename;
		echo '<div id="message" class="updated fade"><p>' . __('Cache was updated!','mnet-gmop') . '</p></div>'; 
	} else { 
		echo '<div id="message" class="updated fade"><p>' . __('Cache was not updated! No objects found!','mnet-gmop') . '</p></div>'; 
	}



?>
	</div><!-- wrap ends -->
<?php
}
//EOF ReGenerate GMOP Object
?>

