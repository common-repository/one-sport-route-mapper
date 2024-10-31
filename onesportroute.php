<?php
/**
 * Plugin Name: One Sport - Route Map
 * Plugin URI: http://www.onesportevent.com/get-free-mapping-tool-for-your-website/
 * Description: Display peoples favourite mapped routes from a shared database and allow creation of new maps and routes
 * Author: sporty - sport@onesportevent.com
 * Author URI: http://www.onesportevent.com/about-us
 * Version: 3.0
 * 
 * CHANGELOG
 * 1.0   - Initial version
 * 1.1   - Improved admin screen - reminder message added so people know they have to click 'create page'
 * 1.2   - Improved flexibility - ability to adjust path to mapping component, bing map keys, border control
 * 1.3   - Added ability to map new route
 * 1.4   - Included css and images in package, made default theme more compatible
 * 1.5   - Added partner message
 * 1.6   - Admin and installation fixes, added clean uninstall
 * 1.8   - Added club parameter to admin API
 * 1.9   - Can now restrict which activities are available when saving workouts.
 * 2.0   - Major API changes and .XAP fixes; added ability to hide/show search panel
 * 2.1   - Fixed search bug; added lat/lng; made template automatically fluid; extra instant styling options
 * 2.2   - Updated mapping tool to improve reliability, display confirmation messages on save and feedback
 * 2.3   - Updated mapper, now v4.1, brand new engine, fixed bugs with route length calculation, supports danish language, deliver of files defaults to amazon for faster more local download
 * 2.4   - Fixed overlooked layout display bug in configuration, language settings now read from user browser rather than user operating system
 * 2.5   - Fixed bug preventing mapper loading on websites under certain configuration options, generally improved stability, added gps functionality for beta users
 * 2.6   - New option to avoid double login; so if user is already logged into the blog and wants to save their route to your blog they are not prompted to login again
 * 3.0   - Major upgrades to both the mapper and routes list.  Loads of new features and options including two new present styles for the routes and the ability to deeply restyle the mapper and the routes list right from within the wordpress plug-in (can also be done via html).  Even the buttons inside the mapper can be easily swapped out for buttons of your choosing.  HTML google maps fallback option now included if user doesn't have silverlight.
 */

/**
* prevent file from being accessed directly
*/
if ('onesportroute.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
	die ('Please do not access this file directly. Thanks!');
}

/**
* Load Includes files
*/
if(!defined('TAMIL_ONESPORTROUTE_PATH')) {
	define('TAMIL_ONESPORTROUTE_PATH', dirname(__FILE__) . "/");
}

if( !class_exists('OneSportRoute') )
{
	class OneSportRoute {
		var $error;
		var $message;

		function __construct($post_id = '') { //constructor

			// Plugin Activation
			add_action('activate_one-sport-route-mapper/onesportroute.php', array(&$this, 'install'));

			// Add header includes
			add_action('init', array($this, 'init'));
			add_action('wp_head', array($this, 'wp_head'));

			// Add the admin menu
			add_action('admin_menu', array($this, 'onesportroute_menu'));
			$this->error = '';
			$this->message = '';
		}

		function __destruct() {
			// Nothing to do...
		}

		function install() {
			$settings = array();
			$settings['includeCrumb'] = 1;
			$settings['styleHideBorders'] = 0;
			$settings['nopermalinkemail'] = 1;
			$settings['disableGrips'] = 0;
			$settings['styleHideImage'] = 0;
			$settings['searchPanelOn'] = 1;
			$settings['borders'] = 0;
			$settings['includeAreas'] = 1;
			$settings['autoLogin'] = 1;
			$settings['mapHeight'] = '600px';
			$settings['mapWidth'] = '90%';
			$settings['groupBreak'] = 6;
			$this->saveSettings($settings);
		}

		function init() {
			wp_enqueue_script('jquery');
		}

		function wp_head() {
			if (is_feed()) {
				return;
			}

			$settings = array();
			$this->getSettings($settings);

			$list = explode(',', $settings['postId']);
			if((!is_page($list)) && (!is_single($list))) { return; }

			echo "<!-- OneSportEvent Routes Plugin - stylesheet and API link. -->\n";
			if($settings['stylesheet'] != '') {
				echo "<link rel='stylesheet' href='" . $settings['stylesheet'] . "' type='text/css' media='screen' />\n";
			}

			$optional_parameters_boolean = array('includeCrumb','includeAreas', 'borders', 'searchPanelOn', 'styleHideBorders', 'styleHideImage', 'autoLogin', 'disableGrips', 'nopermalinkemail');
			$optional_parameters_integer = array('pageNo', 'perPage', 'groupBreak');
			$optional_parameters = array('afterDate','keyWord', 'clubID', 'pathAdjust', 'mapKey', 'backgroundColor', 'logo', 'mapHeight', 'mapWidth', 'latitude', 'longitude');

			$optional_string = '';

			foreach($optional_parameters_boolean as $param) {
				$optional_string .= $param . (($settings[$param]) ? " : true" : " : false") . ", ";
			}
			foreach($optional_parameters_integer as $param) {
				if($settings[$param] != '') {
					$optional_string .= $param . " : " . $settings[$param] . ", ";
				}
			}
			foreach($optional_parameters as $param) {
				if($settings[$param] != '') {
					$optional_string .= $param . " : '" . $settings[$param] . "', ";
				}
			}

			if($settings['oseAreaLevel'] != '') {
				$optional_string .= "oseAreaLevel : '" . $settings['oseAreaLevel'] . "', oseAreaID : ". $settings['oseAreaID'];
			} else {
				$optional_string = substr($optional_string, 0, -2);
			}

			if($settings['partnerMsg'] != '') {
				$optional_string .= ", partnerMsg : '" . $settings['partnerMsg'] . "' ";
			}

			if($settings['activities'] != '') {
				$optional_string .= ", activities : '" . $settings['activities'] . "' ";
			}

			if($settings['borderColor'] != '') {
				$optional_string .= ", borderColor : '" . $settings['borderColor'] . "' ";
			}
			if($settings['miniStatsColors'] != '') {
				$optional_string .= ", miniStatsColors : '" . $settings['miniStatsColors'] . "' ";
			}
			if($settings['graphColors'] != '') {
				$optional_string .= ", graphColors : '" . $settings['graphColors'] . "' ";
			}
			if($settings['tabColors'] != '') {
				$optional_string .= ", tabColors : '" . $settings['tabColors'] . "' ";
			}
			if($settings['comboColors'] != '') {
				$optional_string .= ", comboColors : '" . $settings['comboColors'] . "' ";
			}
			if($settings['statsCrossColor'] != '') {
				$optional_string .= ", statsCrossColor : '" . $settings['statsCrossColor'] . "' ";
			}

					


			// Only if admin has enabled auto-login feature
			if($settings['autoLogin'] == 1) {

				// And if user is logged into website
				if ( is_user_logged_in() ) 
				{
					$current_user = wp_get_current_user();

					// Prevent prompting to login again, when the user saves a route on the blog
					if ( ($current_user instanceof WP_User) )
					{
						$optional_string .= ", userName: '" . $current_user->user_login . "' ";
						$optional_string .= ", userdisplayname: '" . $current_user->display_name . "' ";
						$optional_string .= ", email: '" . $current_user->user_email . "' ";
						$optional_string .= ", firstName: '" . $current_user->user_firstname . "' ";
						$optional_string .= ", lastName: '" . $current_user->user_lastname . "' ";
						$optional_string .= ", userPwd: '" . $current_user->user_pass . "' ";   // Undecyperable hash, we don't need or want the actual password just something unique to stop hckers
						$optional_string .= ", userID: '" . $current_user->ID . "' ";
						$optional_string .= ", webKeyGUID: '" . $settings['WebKey'] . "' ";
					}
				}
			}

			if($settings['extraParam'] != '') {
				$optional_string .= ", " . $settings['extraParam'];
			}

			echo "<script type='text/javascript' src='".$settings['path']."?WebKey=".$settings['WebKey']."'></script>\n";
			echo "<script type='text/javascript'>\n";
			echo "jQuery(document).ready(function($){";
			echo '  oseInitRoutes({'.$optional_string.'});// do stuff'."\n";
			echo "});";

			echo "</script>\n";

			$style_string = '';

			if($settings['styleRed'] != '') {
			   $style_string .= ".osered { color: " . $settings['styleRed'] . " !important; }";
			}

			if($settings['styleText'] != '') {
			   $style_string .= "#oseMaster a, #oseMaster p, #oseMaster ul, #oseMaster li, #oseMaster ol { color: " . $settings['styleText'] . " !important; }";
			}

			if($settings['styleMainBackground'] != '') {
			   $style_string .= ".oseleft_block { background: " . $settings['styleMainBackground'] . " !important; }";
			}

			if( $style_string != '' ) {
				echo "<style type='text/css'>";
				echo $style_string;
				echo "</style>\n";
			}

			echo "<!-- /OneSportEvent Routes Plugin - stylesheet and API link. -->\n";
		}

		function onesportroute_menu() {

			add_submenu_page('options-general.php', 'One Sport Routes' ,'One Sport Routes', '10', __FILE__, array($this, 'manageSettings'));
		}

		function getSettings(&$settings) {
			$settings = maybe_unserialize(get_option('onesportroute_settings'));
			// Default Values
			if($settings['path'] == '') {
				$settings['path'] = 'http://api.onesportevent.com/api/routes/v5/RoutesAPI.aspx';
			}
			if($settings['stylesheet'] == '') {
				$settings['stylesheet'] = 'http://api.onesportevent.com/api/style/v4/blue/osestyle.css';
			}

			if($settings['pathAdjust'] == '') {
				$settings['pathAdjust'] = 'http://static.onesportevent.com/map/v495/FitMapper.xap';
			}

			return;
		}

		function saveSettings(&$settings) {
			//update_option('onesportroute_settings', serialize($settings));
			update_option('onesportroute_settings', $settings);
			return;
		}

		function getAreaLevelList($selected) {
			$list = Array('Auto' => '', 'Country' => 'countryID', 'Region' => 'regionID', 'City' => 'cityID');
			$output = '<select name="oseAreaLevel">'."\n";
			foreach($list as $key => $item ) {
				$output .= '<option value="'.$item.'"';
				$output .= ($selected == $item) ? ' selected' : '';
				$output .= '>'.$key.'</option>'."\n";
			}
			$output .= '</select>';
			return $output;
		}
		function create_page(){
			
			$settings = array();
			$this->getSettings($settings);
			$findme   = 'id="oseRouteCanvas"';
			$page_set = 0;
			
			$page_data = get_page( $settings['postId'] );	// Get page info
			$content = $page_data->post_content;			// Get content
			$pos = strpos($content, $findme);				// Search content for placeholder

			if ($pos > 0) {
			}
			else
			{
				// You can also see a <a href='http://onesportevent.com/api/event/video'>video</a> on how to configure the optional parameters
					echo "<div id='message' class='updated fade'>
						  <p><b>OneSportRoutes is installed</b>.  You'll need to <a href='options-general.php?page=one-sport-route-mapper/onesportroute.php'>go to the configuration</a>, <strong>create a page</strong> and configure any optional parameters before your routes will display on your website.  Please email sport@onesportevent.com if you can't get the routes styling looking how you want it, or to request new features.</p></div>";
			}
		}

		function manageSettings() {

			// Variables
			$base_name = plugin_basename('one-sport-route-mapper/onesportroute.php');
			$base_page = 'admin.php?page='.$base_name;

			$settings = array();
			$this->getSettings($settings);

			$autosave = false;
			// Place below getSettings purposefully.
			if(!empty($_POST['create'])) {
				$title = 'Cool Routes';
				$content = '<div id="oseRouteCanvas"><!-- This is the route placeholder, do not remove this tag --></div>';
				$type = 'page';
		        $post_id = wp_insert_post(array(
					'post_type'		=> $type,
        		    'post_title'    => $title,
		            'post_content'  => $content,
		            'post_status'   => 'publish',
		        ));	
				if($post_id > 0) {
					$post_list = array();
					$post_list = explode(',', $_POST['postId']);
					$post_list[] = $post_id;
					$post_list = array_filter(array_unique($post_list));
					$settings['postId'] = implode(',', $post_list);
					$this->message = 'One Sport Event - Routes page created and settings saved successfully';
				}
				$autosave = true;
			}

			// Form Processing
			if(!empty($_POST['save']) || $autosave) {
				if(!$autosave) {
					$settings['postId'] = $_POST['postId'];
					$this->message = 'Settings saved successfully...';
				}
				$settings['WebKey'] = $_POST['WebKey'];
				$settings['stylesheet'] = $_POST['stylesheet'];
				$settings['pathAdjust'] = $_POST['pathAdjust'];
				$settings['path'] = $_POST['path'];
				$settings['oseAreaLevel'] = $_POST['oseAreaLevel'];
				$settings['oseAreaID'] = $_POST['oseAreaID'];
				$settings['clubID'] = $_POST['clubID'];
				$settings['mapHeight'] = $_POST['mapHeight'];
				$settings['mapWidth'] = $_POST['mapWidth'];
				$settings['afterDate'] = $_POST['afterDate'];
				$settings['pageNo'] = $_POST['pageNo'];
				$settings['includeCrumb'] = ($_POST['includeCrumb'] == 'on') ? 1 : 0;
				$settings['styleHideBorders'] = ($_POST['styleHideBorders'] == 'on') ? 1 : 0;
				$settings['nopermalinkemail'] = ($_POST['nopermalinkemail'] == 'on') ? 1 : 0;
				$settings['styleHideImage'] = ($_POST['styleHideImage'] == 'on') ? 1 : 0;
				$settings['searchPanelOn'] = ($_POST['searchPanelOn'] == 'on') ? 1 : 0;
				$settings['includeAreas'] = ($_POST['includeAreas'] == 'on') ? 1 : 0;
				$settings['autoLogin'] = ($_POST['autoLogin'] == 'on') ? 1 : 0;
				$settings['borders'] = ($_POST['borders'] == 'on') ? 1 : 0;
				$settings['perPage'] = $_POST['perPage'];
				$settings['keyWord'] = $_POST['keyWord'];

				$settings['latitude'] = $_POST['latitude'];
				$settings['longitude'] = $_POST['longitude'];
				
				$settings['logo'] = $_POST['logo'];
				$settings['backgroundColor'] = $_POST['backgroundColor'];
				$settings['mapKey'] = $_POST['mapKey'];
				$settings['extraParam'] = $_POST['extraParam'];
				$settings['partnerMsg'] = $_POST['partnerMsg'];
				$settings['activities'] = $_POST['activities'];
				$settings['groupBreak'] = $_POST['groupBreak'];

				$settings['borderColor'] = $_POST['borderColor'];
				$settings['disableGrips'] = ($_POST['disableGrips'] == 'on') ? 1 : 0;
				$settings['miniStatsColors'] = $_POST['miniStatsColors'];
				$settings['graphColors'] = $_POST['graphColors'];
				$settings['tabColors'] = $_POST['tabColors'];
				$settings['comboColors'] = $_POST['comboColors'];
				$settings['statsCrossColor'] = $_POST['statsCrossColor'];
	
				/* colour styles */
				$settings['styleRed'] = $_POST['styleRed'];
				$settings['styleText'] = $_POST['styleText'];
				$settings['styleMainBackground'] = $_POST['styleMainBackground'];
				

				$this->saveSettings($settings);
			}

			if(!empty($this->message)) { echo '<!-- Last Message --><div id="message" class="updated fade"><p style="color:green;">'.stripslashes($this->message).'</p></div>'; } else { echo '<div id="message" class="updated" style="display: none;"></div>'; }
?>
			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<div class="wrap">
					<h2>One Sport Event - Routes WebKey</h2>
					<br class="clear" />
					<table class="form-table">
						<tr>
							<th width="20%" scope="row" valign="top">Your API WebKey</th>
							<td width="80%"><input name="WebKey" type="text" value="<?php echo $settings['WebKey'];?>"  size="60"/><a href="http://www.onesportevent.com/get-widget-key" title="Get Webkey" style="text-decoration:none;font-weight:bold;margin-left:10px;" target="_blank">Get Webkey</a></td>
						</tr>
						<tr>
							<th width="20%" scope="row" valign="top">Your BING May Key<br/><small>(optional, but awesum looking maps!)</small></th>
							<td width="80%"><input name="mapKey" type="text" value="<?php echo $settings['mapKey'];?>"  size="60"/><a href="http://www.bingmapsportal.com/ " title="Get BING Key from Microsoft" style="text-decoration:none;font-weight:bold;margin-left:10px;" target="_blank">Get BING key from Microsoft</a></td>
						</tr>
						<tr>
							<th width="20%" scope="row" valign="top">OneSportRoute API Url</th>
							<td width="80%"><input name="path" type="text" value="<?php echo $settings['path'];?>"  size="60"/></td>
						</tr>
						<tr>
							<th width="20%" scope="row" valign="top">
							Stylesheet for branding your routes
							</th>
							<td width="80%"><input name="stylesheet" type="text" value="<?php echo $settings['stylesheet'];?>"  size="60"/></td>
						</tr>
						<tr>
						<td colspan="2">
							<small>You can copy the stylesheet yourself and change it to create your own style.   Or ask me (or any developer) to customise a style for you, or use one of the two predefined styles:<ul><li>http://api.onesportevent.com/api/style/v4/blue/osestyle.css</li><li>http://api.onesportevent.com/api/style/v4/green/osestyle.css</li><ul></small>
						</tr>
						<tr>
							<th width="20%" scope="row" valign="top">Path to mapping component<br/><small>(advanced users only)</small></th>
							<td width="80%"><input name="pathAdjust" type="text" value="<?php echo $settings['pathAdjust'];?>"  size="60"/></td>
						</tr>
					</table>
					<h2>One Sport Routes - Create new page to show routes</h2>
					<br class="clear" />
					<p style="margin-left:1em;">
						<input type="submit" name="create" value="Create New Route Page" class="button" />
					</p>
					<?php if($settings['postId'] != '') { ?>
					<table class="form-table">
						<tr>
							<th width="30%" scope="row" valign="top">Enable API on pages/posts with ID<br/><small style="color:red;">Please ignore this if you are not sure what this is!</small></th>
							<td width="70%"><input name="postId" type="text" value="<?php echo $settings['postId'];?>"  size="20"/></td>
						</tr>
					</table>
					<?php } ?>
					<h2 style="float:left;">Optional Parameters</h2><a href="http://www.onesportevent.com/route-mapping-api-documentation/" title="Visit configuration documentation" style="float:left; font-weight:bold; margin-top:25px; text-decoration:none;" target="_blank">Visit configuration documentation</a>
					<table class="form-table">
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Map height</th>
							<td width="70%"><input name="mapHeight" type="text" value="<?php echo $settings['mapHeight'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Map width</th>
							<td width="70%"><input name="mapWidth" type="text" value="<?php echo $settings['mapWidth'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Display map borders</th>
							<td width="70%"><input name="borders" type="checkbox" <?php echo ($settings['borders']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Select Area Level<br/><small>If set to Auto, Area ID will be ignored</small></th>
							<td width="70%"><?php echo $this->getAreaLevelList($settings['oseAreaLevel']);?></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Enter Area ID<br/><small>The <a style="background-color: transparent;" target="_blank" href="http://www.onesportevent.com/route-mapping-api-documentation/#regions">number</a> of the CountryID, CityID or RegionID you wish to default the view to</small></th>
							<td width="70%"><input name="oseAreaID" type="text" value="<?php echo $settings['oseAreaID'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Enter Club ID<br/><small>Allows you to exclude your members routes appearing on other clubs sites (email us for your clubid)</small></th>
							<td width="70%"><input name="clubID" type="text" value="<?php echo $settings['clubID'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Get routes only after the specified date.<br/><small>e.g. '1-jan-09' - Use dd-MMM-yy format so there is no confusion</small></th>
							<td width="70%"><input name="afterDate" type="text" value="<?php echo $settings['afterDate'];?>" /></td>
						</tr>  
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Request a specific page number<br/><small>e.g. 3 - Displays the 3rd page</small></th>
							<td width="70%"><input name="pageNo" type="text" value="<?php echo $settings['pageNo'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Display the crumb</th>
							<td width="70%"><input name="includeCrumb" type="checkbox" <?php echo ($settings['includeCrumb']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Display the search panel</th>
							<td width="70%"><input name="searchPanelOn" type="checkbox" <?php echo ($settings['searchPanelOn']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Display the Countries, Regions or City</th>
							<td width="70%"><input name="includeAreas" type="checkbox" <?php echo ($settings['includeAreas']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Automatically login user<br/><small>if the user is already logged into the blog don't prompt to login again</th>
							<td width="70%"><input name="autoLogin" type="checkbox" <?php echo ($settings['autoLogin']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Number of records per page to display<br/><small>Maxium 15.  By default 10 records per page are shown</small></th>
							<td width="70%"><input name="perPage" type="text" value="<?php echo $settings['perPage'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Number areas to display across the page<br/><small>Suggest 6, and probably at least 2</small></th>
							<td width="70%"><input name="groupBreak" type="text" value="<?php echo $settings['groupBreak'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Your logo embedded in Mapper<br/><small>only available on request</small></th>
							<td width="70%"><input name="logo" type="text" value="<?php echo $settings['logo'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Search for a single keyword.<br/><small>e.g. 'muddy' - All routes with 'muddy' in the name</small></th>
							<td width="70%"><input name="keyWord" type="text" value="<?php echo $settings['keyWord'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Default latitude<br/><small>sets the default map starting point</small></th>
							<td width="70%"><input name="latitude" type="text" value="<?php echo $settings['latitude'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Default longitude<br/><small>sets the default map starting point</small></th>
							<td width="70%"><input name="longitude" type="text" value="<?php echo $settings['longitude'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Turn off permalink email</th>
							<td width="70%"><input name="nopermalinkemail" type="checkbox" <?php echo ($settings['nopermalinkemail']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Parter Message<br/><small>Only shows if permalink email enabled and visitor saves route</small></th>
							<td width="70%"><input name="partnerMsg" type="text" style="width: 500px;" value="<?php echo $settings['partnerMsg'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Allowed Workout Activities<br/><small>Empty for all activities - see <a href="http://www.onesportevent.com/route-mapping-api-documentation/#optionalparameters" title="See configuration documentation" style="font-weight:bold; text-decoration:none;" target="_blank">documentation</a></small></th>
							<td width="70%"><input name="activities" type="text" value="<?php echo $settings['activities'];?>" /></td>
						</tr>
						<!--
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;"Generate revenue<br/><small>(not yet implimented yet).</small></th>						
							<td width="70%"><input name="generateRevenue" type="text" value="<?php echo $settings['generateRevenue'];?>" /></td>
						</tr>
						-->
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Additional Parameter(s) string.<br/><small>Leave it blank if you are not sure</small></th>
							<td width="70%"><input name="extraParam" type="text" value="<?php echo $settings['extraParam'];?>" /></td>
						</tr>
					</table>

					<div class="updated fade" id="stylingoptions" style="background-color: #FFFBCC"><p style="color: blue;">Use your own stylesheet link above to completely define your own look and feel, or instead use these quick styling options for some changes</p></div>

					<table class="form-table">
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Hilight Text Colour (RGB)<br/><small>e.g. number of routes per area, distance.  Suggested value #EB6909</small></th>
							<td width="70%"><input name="styleRed" type="text" value="<?php echo $settings['styleRed'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Normal Text (RGB)<br/><small>e.g. text in lists, paragraphs and links.  Suggested value #404041</small></th>
							<td width="70%"><input name="styleText" type="text" value="<?php echo $settings['styleText'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Main panel background (RGB)<br/><small>e.g. Suggested value white (#FFFFFF), transparent or use an image</small></th>
							<td width="70%"><input name="styleMainBackground" type="text" value="<?php echo $settings['styleMainBackground'];?>" /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Tick to hide the white borders<br/><small>On dark backgrounds if may look better to change border colors using css, or use this to turn borders off completely.</small></th>
							<td width="70%"><input name="styleHideBorders" type="checkbox" <?php echo ($settings['styleHideBorders']) ? "checked" : "";?> /></td>
						</tr>
						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Hide activity image<br/><small>Hides the running, walking, cycling.. etc image </small></th>
							<td width="70%"><input name="styleHideImage" type="checkbox" <?php echo ($settings['styleHideImage']) ? "checked" : "";?> /></td>
						</tr>
  						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Map background color<br/><small>ARGB format, e.g. #F7FF0000 (alpha, red, green, blue)</small></th>
							<td width="70%"><input name="backgroundColor" type="text" value="<?php echo $settings['backgroundColor'];?>" /></td>
						</tr>

  						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Map border colors<br/><small>ARGB format, e.g. #F7FF0000</small></th>
							<td width="70%"><input name="borderColor" type="text" value="<?php echo $settings['borderColor'];?>" /></td>
						</tr>

						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Hide map grips<br/><small>ARGB format, e.g. #F7FF0000</small></th>
							<td width="70%"><input name="disableGrips" type="checkbox" <?php echo ($settings['disableGrips']) ? "checked" : "";?> /></td>
						</tr>

  						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Mini stats area gradient colours<br/><small>Format is gradient from;to   e.g. #F7FF0000;F70000FF (ARGB) </small></th>
							<td width="70%"><input name="miniStatsColors" type="text" value="<?php echo $settings['miniStatsColors'];?>" /></td>
						</tr>

  						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Graph colours<br/><small>Format is gradient from;gradient to;point marks; graph colour   e.g. #F7FF0000;F70000FF;F700FF00;F700FFFF (ARGB) </small></th>
							<td width="70%"><input name="graphColors" type="text" value="<?php echo $settings['graphColors'];?>" /></td>
						</tr>

						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Tab gradient colours<br/><small>Format is gradient from;to   e.g. #F7FF0000;F70000FF (ARGB) </small></th>
							<td width="70%"><input name="tabColors" type="text" value="<?php echo $settings['tabColors'];?>" /></td>
						</tr>

						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Combo gradient colours<br/><small>Format is gradient from;to   e.g. #F7FF0000;F70000FF (ARGB) </small></th>
							<td width="70%"><input name="comboColors" type="text" value="<?php echo $settings['comboColors'];?>" /></td>
						</tr>

						<tr>
							<th width="30%" scope="row" valign="top" style="text-align:right;">Stats CrossColor colours<br/><small>Format is ARGB e.g. #F7FF0000</small></th>
							<td width="70%"><input name="statsCrossColor" type="text" value="<?php echo $settings['statsCrossColor'];?>" /></td>
						</tr>

   				    </table>


					<p style="text-align: center;">
						<input type="submit" name="save" value="Save Settings" class="button" />&nbsp;&nbsp;
						<input type="button" name="cancel" value="Cancel" class="button" onclick="javascript:history.go(-1)" />
					</p>
				</div>
			</form>
<?php 

		}

	}// END Class OneSportRoute
}

// Run The Plugin!
if( class_exists('OneSportRoute') ){
	$routes = new OneSportRoute();
	add_action( 'admin_notices', array($routes,'create_page') );
}

?>