<?php
/*
	Plugin Name: Google Maps Objects Plus
	Plugin URI: http://blog.meloniq.net/downloads/gmaps-objects-plus/
	Description: Showing maps based on location saved in post meta with closest objects definied in administration panel. Developed by <a href="http://www.meloniq.net">MELONIQ.NET</a>.
	Author: MELONIQ.NET
	Version: 1.0.0
	Author URI: http://blog.meloniq.net
*/

// Avoid calling page directly
if (eregi(basename(__FILE__),$_SERVER['PHP_SELF'])) {
	echo "Whoops! You shouldn't be doing that.";
	exit;
}

global $gmop_dbversion;
$gmop_version = '1.0.0';
define('GMOP_VERSION', '1.0.0');
$gmop_dbversion = '100';
// Init options & tables during activation & deregister init option
register_activation_hook( plugin_basename(__FILE__), 'gmop_activate' );

/* PLUGIN and WP-CONTENT directory constants if not already defined */
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

if ( ! defined( 'GMOP_PLUGIN_BASENAME' ) )
	define( 'GMOP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'GMOP_PLUGIN_NAME' ) )
	define( 'GMOP_PLUGIN_NAME', trim( dirname( GMOP_PLUGIN_BASENAME ), '/' ) );
if ( ! defined( 'GMOP_PLUGIN_DIR' ) )
	define( 'GMOP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . GMOP_PLUGIN_NAME );
if ( ! defined( 'GMOP_PLUGIN_URL' ) )
	define( 'GMOP_PLUGIN_URL', WP_PLUGIN_URL . '/' . GMOP_PLUGIN_NAME );
	
	
/**
 * Load Text-Domain
 */
load_plugin_textdomain( 'gmop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Initialize admin menu, shortcode, styles, scripts
 */
if ( is_admin() ) {	
	add_action('admin_menu', 'gmop_add_menu_links');
  add_action('admin_enqueue_scripts', 'gmop_load_admin_scripts');			
} else {
	add_shortcode('GMOP', 'gmop_shortcode');			
	add_action('wp_print_styles', 'gmop_load_styles');
	add_action('wp_head', 'gmop_load_scripts');
	// Add a author to the header
	add_action('wp_head', create_function('', 'echo "\n<meta name=\'Google Maps Objects Plus\' content=\'http://www.meloniq.net\' />\n";') );
}

/**
 * Load scripts
 */
function gmop_load_scripts() {
	wp_enqueue_script('jquery');
//  wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('gmop_js', plugins_url(GMOP_PLUGIN_NAME.'/js/script.js'), array('jquery'), '2.50', true);
	wp_print_scripts('gmop_js');
}		

function gmop_load_admin_scripts() {
  wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('media-upload'); // needed for image upload
	wp_enqueue_script('thickbox'); // needed for image upload
	wp_enqueue_style('thickbox'); // needed for image upload
}

/**
 * Load styles
 */
function gmop_load_styles() {
	wp_register_style('GMOP_Plugin_Style', plugins_url(GMOP_PLUGIN_NAME.'/style.css'));
	wp_enqueue_style('GMOP_Plugin_Style');	
}		

/**
 * Replace shortcode
 */
function gmop_shortcode(  ) {
//TODO
}

/**
 * Populate administration menu of the plugin
 */
function gmop_add_menu_links() {
	global $wp_version, $_registered_pages;
	add_menu_page( __('GMOP Overview', 'mnet-gmop'),__('GMOP', 'mnet-gmop'), 'activate_plugins', 'gmop_main', 'gmop_menu_overview_page'  );
	add_submenu_page('gmop_main', __('Objects', 'mnet-gmop'), __('Objects', 'mnet-gmop'), 'administrator', GMOP_PLUGIN_NAME.'/admin/object_manage.php');
	add_submenu_page('gmop_main', __('Object Types', 'mnet-gmop'), __('Object Types', 'mnet-gmop'), 'administrator', GMOP_PLUGIN_NAME.'/admin/object_type_manage.php');
	add_submenu_page('gmop_main', __('Settings', 'mnet-gmop'), __('Settings', 'mnet-gmop'), 'administrator', GMOP_PLUGIN_NAME.'/admin/main_options.php');
			// Register the pages to WP
	$code_pages = array('object_manage.php','object_type_manage.php', 'main_options.php');
	foreach($code_pages as $code_page) {
		$hookname = get_plugin_page_hookname(GMOP_PLUGIN_NAME.'/admin/'.$code_page, '' );
		$_registered_pages[$hookname] = true;
	}	 
}
		
/**
 * Create GMOP main page content
 */
function gmop_menu_overview_page() {
	global $title;
	include_once (dirname (__FILE__) . '/admin/main_overview.php');
}

/**
 * Create GMOP main page content
 */
function gmop_activate() {
	global $wpdb, $gmop_dbversion;
	include_once (dirname (__FILE__) . '/admin/main_install.php');
	gmop_install_tables($gmop_dbversion);
}
		
/**
 * Create GMOP map
 */
function gmop_map($postid, $base_address, $base_title) {
	global $wpdb;

  if(is_single()){
    $gmop_api_key = get_option('gmop_api_key');
    $gmop_gmaps_loc = get_option('gmop_gmaps_loc');
    $gmop_latitude = get_post_meta($postid, 'gmop_latitude', true);
    $gmop_longitude = get_post_meta($postid, 'gmop_longitude', true);
    $geocode = true;

    if(empty($gmop_latitude) || empty($gmop_longitude)){
      $base_url = $gmop_gmaps_loc . "/maps/geo?output=csv&key=" . $gmop_api_key;
      $request_url = $base_url . "&q=" . urlencode($base_address);

      $ch = curl_init();  		
      curl_setopt($ch, CURLOPT_URL, $request_url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 20);				
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $csv = curl_exec($ch);
      curl_close($ch);
      
      $csvSplit = split(",", $csv);
      $status = $csvSplit[0];
      $lat = $csvSplit[2];
      $lng = $csvSplit[3];
      if (strcmp($status, "200") == 0) {
        // successful geocode
        update_post_meta($postid, 'gmop_latitude', $lat);
        update_post_meta($postid, 'gmop_longitude', $lng);
        $gmop_latitude = get_post_meta($postid, 'gmop_latitude', true);
        $gmop_longitude = get_post_meta($postid, 'gmop_longitude', true);
      } else {
        // failure to geocode
        $geocode = false;
        $gmop_latitude = get_option('gmop_default_latitude');
        $gmop_longitude = get_option('gmop_default_longitude');
      }
    }
    
    $typetable_name = $wpdb->prefix . "gmop_markers";
    $gmoptypes = $wpdb->get_results("SELECT * FROM $typetable_name ORDER BY priority DESC", OBJECT);

    echo '<script src="'.$gmop_gmaps_loc.'/maps?file=api&amp;v=3&amp;key='.$gmop_api_key.'" type="text/javascript"></script>';
    echo '<script src="'.plugins_url(GMOP_PLUGIN_NAME.'/js/markerclusterer_packed.js').'" type="text/javascript"></script>';
    echo '<script src="'.plugins_url(GMOP_PLUGIN_NAME.'/cache/data.json').'" type="text/javascript"></script>';
    echo '';
?>
<div id="gmop_control">
  <p id="gmop_controls">
<?php
  if ($gmoptypes) {
    foreach ($gmoptypes as $gmoptype){
      echo '<label id="gmopgroup' . $gmoptype->ID . '_label">';
      echo '<input type="checkbox" checked="" onclick="toggleGroupp(\'gmopgroup' . $gmoptype->ID . '\')" id="gmopgroup' . $gmoptype->ID . 'Checkbox">';
      echo ' <img src="' . $gmoptype->image_url . '"> ' . $gmoptype->title . ' ';
      echo '</label>';
    }
  }
// onclick="toggleGroup('gmopgroup1')" 
?>
  </p>
</div>

<div id="gmop_maps"></div>

    <script type="text/javascript">
      //<![CDATA[
<?php
  if ($gmoptypes) {
    echo 'function refreshGroup() { ';
    foreach ($gmoptypes as $gmoptype){
      echo 'toggleGroupp(\'gmopgroup' . $gmoptype->ID . '\'); ';
    }
    echo ' }';
  }
?>

        if(GBrowserIsCompatible()) {
          var address = "<?php echo $base_address; ?>";
          var marker_address = "<p style='font-family:Arial; font-size:11px;'><strong><?php echo $base_title; ?></strong><br>" + address + "</p>";
          
          var map = new GMap2(document.getElementById('gmop_maps'));
          var icon = [];
          icon['0'] = new GIcon(G_DEFAULT_ICON);
          icon['0'].image = "http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png";
<?php
  if ($gmoptypes) {
    foreach ($gmoptypes as $gmoptype){
      echo 'icon[\'' . $gmoptype->ID . '\'] = new GIcon(G_DEFAULT_ICON); ';
      echo 'icon[\'' . $gmoptype->ID . '\'].image = "' . $gmoptype->image_url . '"; ';
      echo 'icon[\'' . $gmoptype->ID . '\'].iconSize = new GSize(24, 24); ';
    }
  }
?>
          var markers = [];
<?php
  if ($gmoptypes) {
    echo 'var markerGroups = { ';
    foreach ($gmoptypes as $gmoptype){
      echo '"gmopgroup' . $gmoptype->ID . '": [], ';
    }
    echo ' };';
  }
?>

<?php
  if (!$geocode) {
?>
          document.getElementById("map_canvas").innerHTML = "<p style='font-family:Arial; font-size:75%;'>" + address + " <strong><?php _e('Address not found!','mnet-gmop'); ?></strong></p>";
<?php 
  } 
?>

          map.setCenter(new GLatLng(<?php echo $gmop_latitude; ?>, <?php echo $gmop_longitude; ?>), 13);
          map.addControl(new GLargeMapControl3D());
          map.addControl(new GOverviewMapControl()); 


          var latlng = new GLatLng(<?php echo $gmop_latitude; ?>, <?php echo $gmop_longitude; ?>);
          var marker = new GMarker(latlng, {icon: icon['0']});
          var contentString = marker_address;
          var fn = markerClick(contentString, latlng);
      	  GEvent.addListener(marker, "click", fn);
          markers.push(marker);

          for (var i = 0; i < data.count; ++i) {
            var latlng = new GLatLng(data.objects[i].object_latitude, data.objects[i].object_longitude);
            var marker = new GMarker(latlng, {icon: icon[data.objects[i].object_marker_id]});
            
            var contentString = "<p style='font-family:Arial; font-size:11px;'><strong>" + data.objects[i].object_title + "</strong><br>" + data.objects[i].object_desc + "<br><strong>URL: </strong><a href='" + data.objects[i].object_url + "'>" + data.objects[i].object_url + "</a></p>";
            var fn = markerClick(contentString, latlng);
      	    GEvent.addListener(marker, "click", fn);
	          
	          var type = data.objects[i].object_marker;
	          markerGroups[type].push(marker);
            markers.push(marker);
          }
          
          var mcOptions = {maxZoom: 13};
          var markerCluster = new MarkerClusterer(map, markers, mcOptions);
          var markerClusterG = new MarkerClusterer(map, markerGroups, mcOptions);

          GEvent.addListener(map, "mousemove", function() {
            refreshGroup();
          });
          GEvent.addListener(map, "tilesloaded", function() {
            refreshGroup();
          });


        }else {
          alert("<?php _e('Sorry, Google Maps API is not compatible with Your Web Browser','mnet-gmop'); ?>");
        } 
      //]]>
    </script>


<div id="gmop_nearby_objects">
  <div class="clearfix">
    <div class="header">
      <h2><?php _e('Nearby objects','mnet-gmop'); ?></h2>
    </div>
    <div id="gmop_list_tab_set">
<?php
  if ($gmoptypes) {
    $i = 0;
    foreach ($gmoptypes as $gmoptype){
      if($i){ $tabclass = "unhighlight"; }else{ $tabclass = "highlight"; }
      echo '<div onclick="toggle_object_tabs(this);" class="tab ' . $tabclass . '" id="block' . $gmoptype->ID . '_tab"> ' . $gmoptype->title . ' </div>';
      $i++;
    }
  }
?>
    </div>
  </div>
  <div class="clearfix" id="gmop_content">

<?php
  if ($gmoptypes) {
    $gmop_search_distance = 0.5;
    $gmop_latitude_plus = $gmop_latitude + $gmop_search_distance;
    $gmop_latitude_minus = $gmop_latitude - $gmop_search_distance;
    $gmop_longitude_plus = $gmop_longitude + $gmop_search_distance;
    $gmop_longitude_minus = $gmop_longitude - $gmop_search_distance;
    $objecttable_name = $wpdb->prefix . "gmop_objects";
    $i = 0;
    foreach ($gmoptypes as $gmoptype){
      if($i){ $tabstyle = "none"; }else{ $tabstyle = "block"; }
      echo '<div style="display: ' . $tabstyle . ';" id="block' . $gmoptype->ID . '_tab_content">';
      echo '<table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>';
      echo '<tr> <th width="80%" class="first">' . __('Object name', 'mnet-gmop') . '</th> <th width="20%" align="center">' . __('Distance', 'mnet-gmop') . '</th> </tr>';

      $markerid = $gmoptype->ID;
      $gmopobjects = $wpdb->get_results("SELECT * FROM $objecttable_name WHERE marker = '$markerid' AND (latitude BETWEEN $gmop_latitude_minus AND $gmop_latitude_plus ) AND (longitude BETWEEN $gmop_longitude_minus AND $gmop_longitude_plus ) ORDER BY ID ASC LIMIT 5", OBJECT);
      if ($gmopobjects) {
        foreach ($gmopobjects as $gmopobject){
          $gmopobjectdistance = round(gmop_distance($gmop_latitude, $gmop_longitude, $gmopobject->latitude, $gmopobject->longitude), 2);
          echo '<tr><td class="first">' . $gmopobject->title . '</td>';
          echo '<td align="center">' . $gmopobjectdistance . ' km</td></tr>';
          echo '<tr><td class="comment" colspan="2">' . $gmopobject->description . '</td></tr>';
        }      
      }else{
        echo '<tr><td class="comment" colspan="2">' . __('No objects found.', 'mnet-gmop') . '</td></tr>';
      }

      echo '</tbody></table>';
      echo '</div>';
      $i++;
    }
  }
?>

  </div>
</div>
<script language="javascript">
<?php
  if ($gmoptypes) {
    $gmoptabarray = 'var valid_object_tabs_ids = new Array(';
    foreach ($gmoptypes as $gmoptype){
      $gmoptabarray .= '\'block' . $gmoptype->ID . '_tab\', ';
    }
    $gmoptabarray = substr($gmoptabarray, 0, -2);
    $gmoptabarray .= ');';
    echo $gmoptabarray;
  }
?>
</script>

<?php    
  }

}


// Calculate distance between two coordinates
function gmop_distance($lat1, $lon1, $lat2, $lon2) {
  $distance = (3958*3.1415926*sqrt(($lat2-$lat1)*($lat2-$lat1) + cos($lat2/57.29578)*cos($lat1/57.29578)*($lon2-$lon1)*($lon2-$lon1))/180);
  return $distance;
}
?>