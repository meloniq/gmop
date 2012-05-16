<?php ?>

<h2><?php echo $title;?></h2>
<h3><?php _e('How to start using this plugin?','mnet-gmop'); ?></h3>
<p><?php _e('1) Go to Settings tab and generate new Google Maps API key for Your domain.','mnet-gmop'); ?></p>
<p><?php _e('2) Go to Object Types tab. You will find there 3 preinstalled object types, remove, modify or add new types which You wanna use.','mnet-gmop'); ?></p>
<p><?php _e('3) Go to Objects tab. You will find there 3 preinstaled objects, remove, modify or add new which You wanna show on maps.','mnet-gmop'); ?></p>	
<p><?php _e('4) When You will finish with objects, click ReGenerate Cache button in the top of page. Cache file of objects will be re-generated. <br />Note: cache folder inside plugin dir MUST have writing permission.','mnet-gmop'); ?></p>	
<p><?php _e('5) Open template file where You wanna include maps (eg. single.php or loop-single.php) and in prepared place for map add below code:','mnet-gmop'); ?></p>
<p><code>
            if (function_exists('gmop_map')){ <br />
              $base_address = get_post_meta($post->ID, 'location', true); <br />
              $base_title = get_the_title($post->ID); <br />
              if($base_address !=''){ <br />
                gmop_map($post->ID, $base_address, $base_title); <br />
              } <br />
            } 
</code></p>
<p><?php _e('By variable $base_address You need to pass address in format "Street, City, Postal Code, Country".','mnet-gmop'); ?></p>
