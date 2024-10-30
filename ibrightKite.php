<?php
/*
Plugin Name: iBrightKite
Plugin URI: http://www.davemcdermid.co.uk/2008/10/bright-kite-for-wordpress/
Description: Displays your bright kite location on your blog. trendy.
Version: 1.0.4
Author: Dave McDermid
Author URI: http://www.davemcdermid.co.uk/
*/

/* When the plugin is activated, this function will be executed */

register_activation_hook( __FILE__, 'ibrightkite_activate' );

function ibrightkite_activate() {
	$ibrightkite_options = array(
		"username" => '',
		"widget_title" => 'iBrightKite',
		"mode" => 0,
		"map_api" => '',
		"map_zoom" => 14,
		"map_width" => 280,
		"map_height" => 200
	);
	add_option("ibrightkite_options", $ibrightkite_options, '', 'yes');
}

/* When the plugin is deactivated, this function will be executed 
   And yes, we keep your database clear. :) */

register_deactivation_hook( __FILE__, 'ibrightkite_deactivate' );

function ibrightkite_deactivate() {
	delete_option("ibrightkite_options");
}

/* Add iBrightKite on Plugins' Menu */

function ibrightkite_add_menu() {
 if (function_exists('add_options_page')) {
    add_submenu_page('plugins.php', 'iBrightKite - people care where you are', 'iBrightKite', 8, basename(__FILE__), 'ibrightkite_options_page');
  }
}
add_action('admin_menu', 'ibrightkite_add_menu');

function ibrightkite_options_page() { 
	
	$ibrightkite_options = get_option('ibrightkite_options');

	$brightkite_modes = array(
		0 => 'Google Map',
		1 => 'Text Location');


	if ($_POST['ibrightkite_send']) {
		
		if (!empty($_POST['ibrightkite_username'])) {
			$ibrightkite_options['username'] = $_POST['ibrightkite_username'];
			$ibrightkite_options['mode'] = $_POST['ibrightkite_mode'];
			$ibrightkite_options['map_api'] = $_POST['ibrightkite_map_api'];
			$ibrightkite_options['map_zoom'] = $_POST['ibrightkite_map_zoom'];
			$ibrightkite_options['map_width'] = $_POST['ibrightkite_map_width'];
			$ibrightkite_options['map_height'] = $_POST['ibrightkite_map_height'];
			$ibrightkite_options['cache'] = $_POST['ibrightkite_cache'];
			update_option('ibrightkite_options', $ibrightkite_options);
			echo '<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div>';
		}
		
	}	
?>

<div class="wrap">
	<h2>iBrightKite Configuration</h2>
	<form id="ibrightkite" class="form-table" method="post" action="">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_username">BrightKite username</label>
					</th>
					<td>
						<input type="text" name="ibrightkite_username" id="ibrightkite_username" value="<?php echo $ibrightkite_options['username'] ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_mode">BrightKite display mode</label>
					</th>
					<td>
						<select name="ibrightkite_mode" id="ibrightkite_mode">
						<?php
						foreach($brightkite_modes as $mode=>$mdisplay)
							echo '<option value="'.$mode.'"'.($ibrightkite_options['mode']==$mode ? 'selected="selected"':'').'>'.$mdisplay.'</option>';
						?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_map_api">Google Map API key</label>
					</th>
					<td>
						<input type="text" name="ibrightkite_map_api" id="ibrightkite_map_api" value="<?php echo $ibrightkite_options['map_api'] ?>" />
						<a href="http://code.google.com/apis/maps/signup.html" target="_blank">get your api key here</a>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_map_zoom">Map zoom (0= zoomed out, 19= zoomed in)</label>
					</th>
					<td>
						<select name="ibrightkite_map_zoom" id="ibrightkite_map_zoom">
						<?php
						for($i=0;$i<20;$i++)
							echo '<option value="'.$i.'"'.($ibrightkite_options['map_zoom']==$i ? 'selected="selected"':'').'>level '.$i.'</option>';
						?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_map_width">Map width</label>
					</th>
					<td>
						<input type="text" name="ibrightkite_map_width" id="ibrightkite_map_width" value="<?php echo $ibrightkite_options['map_width'] ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_map_height">Map height</label>
					</th>
					<td>
						<input type="text" name="ibrightkite_map_height" id="ibrightkite_map_height" value="<?php echo $ibrightkite_options['map_height'] ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ibrightkite_cache">Cache location for</label>
					</th>
					<td>
						<select name="ibrightkite_cache" id="ibrightkite_cache">
							<option value="0"<?php if($ibrightkite_options['cache']==0) echo ' selected="selected"' ?>>always load from brightkite</option>
							<option value="10"<?php if($ibrightkite_options['cache']==10) echo ' selected="selected"' ?>>10 minutes</option>
							<option value="30"<?php if($ibrightkite_options['cache']==30) echo ' selected="selected"' ?>>30 minutes</option>
							<option value="60"<?php if($ibrightkite_options['cache']==60) echo ' selected="selected"' ?>>60 minutes</option>
							<option value="120"<?php if($ibrightkite_options['cache']==120) echo ' selected="selected"' ?>>2 hours</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="ibrightkite_send" id="ibrightkite_send" value="true" />
		<p class="submit"><input type="submit" value="Save Changes" /></p>
	</form>
</div>

<?php }

/* All setup, ready to let the world know where we are */
function ibrightkite() {
	$ibrightkite_options = get_option("ibrightkite_options");
	//check that cachng is enabled, and hasn't expired
	if($ibrightkite_options['cache'] >0 && $ibrightkite_options['cache_ts'] && (date('U')-$ibrightkite_options['cache_ts']) < ($ibrightkite_options['cache']*60)) {
		$xml = $ibrightkite_options['cache_x'];
		$person = getXMLinner($xml,'person');
		//echo 'cache '. ($ibrightkite_options['cache_ts']-date('U'))/60 .' minutes left';
	}
	else {
		$xml = getXMLstring($ibrightkite_options['username']);
		$ibrightkite_options['cache_x'] = $xml;
		$ibrightkite_options['cache_ts'] = date('U');		
		update_option('ibrightkite_options', $ibrightkite_options);
		$person = getXMLinner($xml,'person');
		//echo 'live';
	}
	if($person !='') {
		//echo '<pre>'.$person.'</pre>';
		$place = getXMLinner($person,'place');
		$loc = getXMLinner($place,'display_location');
		$lat = getXMLinner($place,'latitude');
		$lng = getXMLinner($place,'longitude');
		$when = getXMLinner($person,'last_checked_in_as_words');

		echo '<a href="http://brightkite.com/people/'. $ibrightkite_options['username'] .'" class="checkedin">checked in '.$when.' ago</a>';
		if($ibrightkite_options['mode']==0) {
			echo '<img src="http://maps.google.com/staticmap?center='.$lat.','.$lng.'&zoom='.$ibrightkite_options['map_zoom'].'&size='.$ibrightkite_options['map_width'].'x'.$ibrightkite_options['map_height'].'&maptype=mobile&markers='.$lat.','.$lng.',bluex&key='.$ibrightkite_options['map_api'].'" alt="'.$loc.'" title="'.$loc.'" />';
		}
		elseif($ibrightkite_options['mode']==1) {
			echo '<span class="ibrightkite_location">'.$loc.'</span>';
		}
	}
	else {
		echo '<span class="ibrightkite_location">I\'m lost.</span>';
	}
}

//if the tag is found, strips it and returns the contents.
function getXMLinner($x,$t) {
	if(!ereg('.*<'.$t.'[^>]*>',$x))
		return '';
	$x = ereg_replace('.*<'.$t.'[^>]*>','',$x);
	$x = ereg_replace('</'.$t.'>.*','',$x);
	return $x;
}

//simply returns the xml doc as a string. nothing fancy.
function getXMLstring($username) {
	$url = 'http://brightkite.com/people/' . $username .'.xml';
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

//not used at the moment, depends on simplexml (php5+)
function getXML($username) {
	$url = 'http://brightkite.com/people/' . $username .'.xml';
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);
	if ($content) {
		if (function_exists('simplexml_load_file')) {
			$xml = new SimpleXMLElement($content);
			if(!$xml)
				return false;
			else
				return $xml;
		} else {
			return false;
		}
	} else {
		return false;
	}	
}

/* Add iBrightKite Widget */

function widget_ibrightkite_init() {
	
	if (!function_exists('register_sidebar_widget')) {
		return;
	}
	
	function widget_ibrightkite($args) {
	    extract($args);
		$ibrightkite_options = get_option('ibrightkite_options');
		$title = $ibrightkite_options['widget_title'];
	?>
		<?php echo $before_widget; ?>
			<?php echo $before_title
                . $title
                . $after_title; ?>
				<div id="ibrightkite_display">
		            <?php ibrightkite(); ?>
				</div>
		<?php echo $after_widget; ?>
	<?php
	}
	register_sidebar_widget('iBrightKite', 'widget_ibrightkite');
	
	function widget_ibrightkite_control() {
		$ibrightkite_options = get_option('ibrightkite_options');
		$title = $ibrightkite_options['widget_title'];
		
		if (!empty($_POST['ibrightkite_widget_title'])) {
			$title = strip_tags(stripslashes($_POST['ibrightkite_widget_title']));
			$ibrightkite_options['widget_title'] = $title;
			update_option('ibrightkite_options', $ibrightkite_options);
		}
		
		$title = htmlspecialchars($title, ENT_QUOTES);
		?>
			
			<p>
				<label for="ibrightkite_widget_title">
					Title:
					<input type="text" id="ibrightkite_widget_title" name="ibrightkite_widget_title" value="<?php echo $title; ?>" />
				</label>
			</p>
			
		<?php
		
	}
	register_widget_control('ibrightkite', 'widget_ibrightkite_control', 200, 50);
}
add_action('widgets_init', 'widget_ibrightkite_init');

?>