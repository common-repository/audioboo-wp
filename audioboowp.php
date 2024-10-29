<?php
/*
Plugin Name: AudioBoo WP
Plugin URI: http://audioboo.fm
Description: AudioBoo plugin. See also: <a href="http://audioboo.fm/">AudioBoo website</a>.
Version: 1.2
Author: BestBefore
Author URI: http://bestbefore.tv/
*/

//define('MAGPIE_CACHE_AGE', 120);
//define('MAGPIE_CACHE_ON', 0); //2.7 Cache Bug
define('MAGPIE_INPUT_ENCODING', 'UTF-8');
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

$audioboo_options['widget_fields']['widgettitle'] = array('label'=>'Widget title:', 'type'=>'text', 'default'=>'');
$audioboo_options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'user', 'default'=>'');
$audioboo_options['widget_fields']['num'] = array('label'=>'Number of audioboos:', 'type'=>'text', 'default'=>'5');
$audioboo_options['widget_fields']['title'] = array('label'=>'Show title:', 'type'=>'checkbox', 'default'=>true);
$audioboo_options['widget_fields']['createDate'] = array('label'=>'Show create date:', 'type'=>'checkbox', 'default'=>true);
$audioboo_options['widget_fields']['link'] = array('label'=>'Show link:', 'type'=>'checkbox', 'default'=>false);
$audioboo_options['widget_fields']['defaultCSS'] = array('label'=>'default', 'type'=>'radio', 'default'=>true);
$audioboo_options['widget_fields']['customCSS'] = array('label'=>'custom', 'type'=>'radio', 'default'=>false);
$audioboo_options['widget_fields']['noneCSS'] = array('label'=>'none', 'type'=>'radio', 'default'=>false);

$audioboo_options['prefix'] = 'audioboo';
$audioboo_LOCALTEST = false;
$audioboo_LOCALPATH = "/../audioBooJS/wordpressres1.2/";

// audioboo widget stuff
function widget_audioboo_init()
{
	if ( !function_exists('register_sidebar_widget') )
		return;
	
	$check_options = get_option('widget_audioboo');
  	if ($check_options['number']=='') {
    	$check_options['number'] = 1;
    	update_option('widget_audioboo', $check_options);
  	}

	// ------------------------------------------------------
	/* BLOG PAGE */
	function widget_audioboo($args, $number = 1) {

		global $audioboo_options;
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		//include_once(ABSPATH . WPINC . '/rss.php');
		$options = get_option('widget_audioboo');
		
		// fill options with default values if value is not set
		$item = $options[$number];
		foreach($audioboo_options['widget_fields'] as $key => $field) {
			if (! isset($item[$key])) {
				$item[$key] = $field['default'];
			}
		}

		// These lines generate our output.
		// Display audioboo messages

    	echo $before_widget . $before_title . $item['widgettitle'] . $after_title;
		
		echo '<div class="audioboo_widget_div">';
		echo '<div id="audioboo_player_div"></div>';
		echo '</div>';

		// ADD JAVASCRIPT
		global $audioboo_LOCALTEST, $audioboo_LOCALPATH;

		$audioboojs_url = '';
		if ($audioboo_LOCALTEST)
			$audioboojs_url = get_bloginfo('wpurl') . $audioboo_LOCALPATH . 'ab.js';
		else
			$audioboojs_url = 'http://static.audioboo.fm/wordpressres12/ab.js';

		// add JavaScript - (set global variables)
		$options = get_option('widget_audioboo');
		$item = $options[1];
		echo '<script type="text/javascript">';
		$audiobooNum = ($item['num'] != '') ? $item['num'] : 1;
		$audiobooShowTitle = ($item['title'] == true) ? "true" : "false";
		$audiobooShowDate = ($item['createDate'] == true) ? "true" : "false";
		$audiobooShowLink = ($item['link'] == true) ? "true" : "false";
		$audiobooUserName = $item['username'];

		echo 'var audiobooNum=' . $audiobooNum . ';';
		echo 'var audiobooShowTitle=' . $audiobooShowTitle . ';';
		echo 'var audiobooShowDate=' . $audiobooShowDate . ';';
		echo 'var audiobooShowLink=' . $audiobooShowLink . ';';
		echo 'var audiobooUserName="' . $audiobooUserName . '";';

		syslog(LOG_ALERT, "widget_audioboo username " . $audiobooUserName);

		if ($item["defaultCSS"] == 1)
	    	//$myStyleFile = WP_PLUGIN_URL.'/audioboo-wp/audioboo.css';
			echo 'var audiobooBoosDefaultCSS=true;';	
		else if ($item["customCSS"] == 1) {
			$myStyleFile = get_bloginfo('template_url', 'display') . "/" . $item["customCSS_name"];
			//$myStyleFile = $item["customCSS_name"];
			echo 'var audiobooBoosCSS="' . $myStyleFile . '";';	
		}
		
		echo '</script>';

		echo '<script type="text/javascript" src="' . $audioboojs_url . '"></script>';

		echo $after_widget;
	}



	// ------------------------------------------------------
	/* ADMIN */
	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_audioboo_control($number) {
	
		global $audioboo_options;

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_audioboo');

		$username = $options[1]['username'];
		syslog(LOG_ALERT, "widget_audioboo_control username " . $username);
		//$audioBoosURL = '';

		if ( isset($_POST['audioboo-submit']) ) {
			// form submited (POST)
			// $postUsername = $_POST['audioboo_username'];
			// syslog(LOG_ALERT, "audioboo-submit " . $postUsername);
			// // if we submited audoboo's admin form
			// if ($postUsername == $username) {
			// 	$audioBoosURL = $_POST["audioboo-boosURL"];
			// 	if ( isset($_POST['audioboo-boosURL']) ) {
			// 		if ($audioBoosURL) {
			// 		  	if ($options['audioboo-boosURL']!=$audioBoosURL) {
			// 		    	$options['audioboo-boosURL'] = $audioBoosURL;
			// 		    	update_option('widget_audioboo', $options);
			// 		  	}
			// 		} else {
			// 			// delete option???? check it
			// 		}
			// 	}				
			// } else {
			// 	unset($options['audioboo-boosURL']);
			// 		    	update_option('widget_audioboo', $options);
			// }	

			foreach($audioboo_options['widget_fields'] as $key => $field) {
				$options[$number][$key] = $field['default'];
				$field_name = sprintf('%s_%s', $audioboo_options['prefix'], $key);

				if ($field['type'] == 'text' || $field['type'] == 'user') {
					$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
				} elseif (($field['type'] == 'checkbox')) {
					$options[$number][$key] = isset($_POST[$field_name]);
				}
			}

			if ( isset($_POST['audiobooradiogroup']) ) {
				//$options[$number][$key]
				$key = substr($_POST["audiobooradiogroup"], strlen("audioboo_"));
				// reset old settings
				$options[$number]['defaultCSS'] = 0;
				$options[$number]['customCSS'] = 0;
				$options[$number]['noneCSS'] = 0;

				$options[$number][$key] = 1;
			}			
			
			// is customCSS set??
			if ( isset($_POST['audioboo_customCSS_name']) ) {
				$options[$number]["customCSS_name"] = strip_tags(stripslashes($_POST["audioboo_customCSS_name"]));
			}
			
			update_option('widget_audioboo', $options);
		} else {
			// probably GET
			if (empty($options[$number]['defaultCSS']) && empty($options[$number]['customCSS']) && empty($options[$number]['noneCSS'])) {
				// CSS is not selected yet... set def value
				$options[$number]['defaultCSS'] = 1;
				update_option('widget_audioboo', $options);
			}
		}

		$cssRadioGroup = '';
		foreach($audioboo_options['widget_fields'] as $key => $field) {
			
			$field_name = sprintf('%s_%s', $audioboo_options['prefix'], $key);
			$field_checked = '';
			
			if ($field['type'] == 'user') {				
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
				printf('<p style="text-align:right;" class="audioboo_field"><label for="%s">%s <input id="%s" name="%s" type="text" value="%s" class="text"/>', $field_name, __($field['label']), $field_name, $field_name, $field_value);

				printf('</label></p>');
				continue;
			} elseif ($field['type'] == 'text') {
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
			} elseif ($field['type'] == 'checkbox') {
				$field_value = 1;
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
			} elseif ($field['type'] == 'radio') {
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
				
				$cssRadioGroup .= '<div class="audioboo_field_radio_row"><input type="radio" name="audiobooradiogroup" value="' . $field_name .'" '. $field_checked . '>' . $field['label'] . "</div>";
				if ($key == 'customCSS') {
					$cssRadioGroup .= '<span style="font-size: 9px;">(selected themes folder)</span>';
					$rt_field_value = htmlspecialchars($options[$number][$key . "_name"], ENT_QUOTES);
					$rt_field_name = $field_name . "_name";
					$cssRadioGroup .= sprintf('<br><input class="audiobooradiogroupinputtext" id="%s" name="%s" type="text" value="%s"/>', $rt_field_name, $rt_field_name, $rt_field_value);
				}
				//$cssRadioGroup .= '<br>';
				continue;
			}
			
			printf('<p style="text-align:right;" class="audioboo_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);
		}

		if ($cssRadioGroup != '') {
			printf('<hr><div class="audioboo_field_radiocontainer">CSS:<div class="audioboo_field_radio">%s</div></div>', $cssRadioGroup);
		}

		echo '<input type="hidden" id="audioboo-submit" name="audioboo-submit" value="1" />';
	}
	
	
	// ------------------------------------------------------
	/* ADMIN */
	function widget_audioboo_setup() {
				
		$options = $newoptions = get_option('widget_audioboo');
		
		if ( isset($_POST['audioboo-number-submit']) ) {
			$number = (int) $_POST['audioboo-number'];
			$newoptions['number'] = $number;
		}
		
		if ( $options != $newoptions ) {
			update_option('widget_audioboo', $newoptions);
			widget_audioboo_register();
		}
	}

	
	function widget_audioboo_register() {
		
		$options = get_option('widget_audioboo');
		$dims = array('width' => 300, 'height' => 300);
		$class = array('classname' => 'widget_audioboo');

		for ($i = 1; $i <= 9; $i++) {
			$name = sprintf(__('audioboo #%d'), $i);
			$id = "audioboo-$i"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_audioboo' : /* unregister */ '', $class, $i);
			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_audioboo_control' : /* unregister */ '', $dims, $i);
		}
		
		add_action('sidebar_admin_setup', 'widget_audioboo_setup');
	}

	widget_audioboo_register();
}

// ------------------------------------------------------
// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_audioboo_init');

// ------------------------------------------------------
/* add CSS for admin */
function audiobooAdminCSS()
{
   	$myStyleFile = WP_PLUGIN_URL.'/audioboo-wp/audiobooadmin.css';
	echo "<link rel='stylesheet' href='". $myStyleFile ."' type='text/css' media='all' />";
}

add_action('admin_head', 'audiobooAdminCSS');

?>