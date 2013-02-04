<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	$gmop_api_key =	$_REQUEST['gmop_api_key'];
	$gmop_gmaps_loc =	$_REQUEST['gmop_gmaps_loc'];
	$gmop_default_latitude =	$_REQUEST['gmop_default_latitude'];
	$gmop_default_longitude =	$_REQUEST['gmop_default_longitude'];
	update_option('gmop_api_key', $gmop_api_key);
	update_option('gmop_gmaps_loc', $gmop_gmaps_loc);
	update_option('gmop_default_latitude', $gmop_default_latitude);
	update_option('gmop_default_longitude', $gmop_default_longitude);
	$message = __('Options updated','mnet-gmop');
}
$gmop_api_key = get_option('gmop_api_key');
$gmop_gmaps_loc = get_option('gmop_gmaps_loc');
$gmop_default_latitude = get_option('gmop_default_latitude');
$gmop_default_longitude = get_option('gmop_default_longitude');

?>
<div class="wrap">
	<h2><?php _e('GMOP Settings','mnet-gmop'); ?></h2>
	<?php
	if (isset($message)) {
	?>
	<div id="message" class="updated fade"><p>
		<?php 
		echo $message;
		?>
	</p></div><!-- updated fade ends -->
	<?php 
	}
	$gmaps_domains = array( 'http://maps.google.com', 
													'http://maps.google.at',
													'http://maps.google.com.au',
													'http://maps.google.com.ba',
													'http://maps.google.be',
													'http://maps.google.com.br',
													'http://maps.google.ca',
													'http://maps.google.ch',
													'http://maps.google.cz',
													'http://maps.google.de',
													'http://maps.google.dk',
													'http://maps.google.es',
													'http://maps.google.fi',
													'http://maps.google.fr',
													'http://maps.google.it',
													'http://maps.google.co.jp',
													'http://maps.google.nl',
													'http://maps.google.no',
													'http://maps.google.co.nz',
													'http://maps.google.pl',
													'http://maps.google.ru',
													'http://maps.google.se',
													'http://maps.google.tw',
													'http://maps.google.co.uk'
													);
	?>	
	<div id="poststuff">	
		<div id="postdiv" class="postarea">			
			<div class="postbox">
				<h3 class="hndle"><span><?php _e('Plugin Info','mnet-gmop') ?></span></h3>
				<div class="inside">
				<?php echo "<strong>".__('GMOP Version: ','mnet-gmop')."</strong>".GMOP_VERSION; ?><br />
				<?php echo "<strong>".__('GMOP Database Version: ','mnet-gmop')."</strong>".get_option('gmop_db_version'); ?>
				</div><!-- inside ends -->
			</div><!-- postbox ends -->		
		</div>
	</div>
	<form name="post" action="" method="post" id="post">
		<div id="poststuff">
			<div id="postdiv" class="postarea">			

				<div class="postbox">
					<h3 class="hndle"><span><?php _e('Google Maps API Key','mnet-gmop') ?></span></h3>
					<div class="inside">
						<input type="text" name="gmop_api_key" id="gmop_api_key" value="<?php echo $gmop_api_key; ?>" style="min-width:500px;" />
						<br />
						<small><?php _e('Get free API key for','mnet-gmop') ?> <a href="http://code.google.com/apis/maps/signup.html" target="_new" title=""><?php _e('Google Maps','mnet-gmop') ?></a>.</small> 
					</div><!-- inside ends -->
				</div><!-- postbox ends -->									

				<div class="postbox">
					<h3 class="hndle"><span><?php _e('Google Maps Location','mnet-gmop') ?></span></h3>
					<div class="inside">
						<select name="gmop_gmaps_loc" id="gmop_gmaps_loc">
						<?php 
						foreach($gmaps_domains as $key){
							if($key == $gmop_gmaps_loc){ 
								$selected = 'selected="selected"'; 
							}else{ 
								$selected = ''; 
							}
							echo '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';
						} 
						?>
						</select>
					</div><!-- inside ends -->
				</div><!-- postbox ends -->									

				<div class="postbox">
					<h3 class="hndle"><span><?php _e('Default latitude','mnet-gmop') ?></span></h3>
					<div class="inside">
						<input type="text" name="gmop_default_latitude" id="gmop_default_latitude" value="<?php echo $gmop_default_latitude; ?>" style="min-width:500px;" />
						<br />
						<small><?php _e('Default latitude on add object page.','mnet-gmop') ?></small> 
					</div><!-- inside ends -->
				</div><!-- postbox ends -->									

				<div class="postbox">
					<h3 class="hndle"><span><?php _e('Default longitude','mnet-gmop') ?></span></h3>
					<div class="inside">
						<input type="text" name="gmop_default_longitude" id="gmop_default_longitude" value="<?php echo $gmop_default_longitude; ?>" style="min-width:500px;" />
						<br />
						<small><?php _e('Default longitude on add object page.','mnet-gmop') ?></small> 
					</div><!-- inside ends -->
				</div><!-- postbox ends -->									



				<p class="submit">
					<span id="autosave"></span>
					<input type="submit" name="submit" value="<?php _e('Save Options','mnet-gmop') ?>" style="font-weight: bold;" />
				</p>				
			</div><!-- postdiv ends -->
		</div><!-- poststuff ends -->
	</form>

</div><!-- wrap ends -->