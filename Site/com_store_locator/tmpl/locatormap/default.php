<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
HTMLHelper::_('jquery.framework');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addStyleSheet(Uri::root() . 'index.php?option=com_store_locator&task=pjActionLoadCss&controller=Locatormap&format=raw');
$document->addStyleSheet(Uri::root() . 'media/com_store_locator/css/pj.bootstrap.min.css');
$document->addScript(Uri::root() . "media/com_store_locator/js/custom_front.js", $options, $attributes);
$document->addScript(Uri::root() . "index.php?option=com_store_locator&task=pjActionLoadJs&controller=Locatormap&format=raw", $options);



$zoom = $this->item->defaultzoom;
$radunit = $this->item->distance_unit;
$def = $this->item->defaultaddress;

$map_height = $this->item->map_height;
// echo "<pre>".print_r($this->item, true);
// die;
// $res_width = $this->item->results_width;
$ip = $this->item->ip;
if(!empty($ip))
{
	$urlss = "http://ip-api.com/json/$ip";
	$fdtqq = file_get_contents($urlss);
	$dstjs = json_decode($fdtqq,true);
	$def = $dstjs['city'].', '.$dstjs['region'].', '.$dstjs['countryCode'].' - '.$dstjs['zip'];
}
// echo $def;
$skin_data = $this->mapskin->skin_data;
$uploadPath = JPATH_ROOT . '/images/com_store_locator/uploads';
$filpath = $uploadPath.'/'.$skin_data;
$skin_contents = file_get_contents($filpath);
$map_theme = $this->maptheme;

?>
<style>
	<?php if(!empty($map_height)) { ?>
		#pjWrapperStoreLocator_theme1 .pjSlMap, #pjWrapperStoreLocator_theme1 .pjSlResults .pjSlResultsInner
		{
			height:<?php echo $map_height; ?>px !important;
			max-height:<?php echo $map_height; ?>px !important;
		}
	<?php } ?>
[id^=pjWrapper] .panel-default>.panel-heading {
	background-color: <?php echo $map_theme->toolbar_bg;?>;
}
[id^=pjWrapper] .list-group-item {
    background: transparent !important;
}

div#stl_search_addresses {
    background: <?php echo $map_theme->results_bg;?>;
}

a.btn.btn-primary.pjSlBtnFilterBy {
	background: <?php echo $map_theme->button_color;?>;
	border-color: <?php echo $map_theme->button_color;?>;
}
.pagination {
    display: inline-block;
    padding: 0;
    margin: 20px 0;
}

.pagination li {
    display: inline-block;
    margin: 0 4px;
}
.gm-style-iw-d {
    max-width: 330px;
    overflow: hidden !important;
    overflow-y: scroll !important;
}

.gm-style-iw-d, .gm-style-iw-d * {
    box-sizing: border-box;
}
.pagination li a {
    color: black;
    padding: 8px 16px;
    text-decoration: none;
    border: 1px solid #ddd;
    cursor: pointer;
}

.pagination li.active a {
    background-color: #4CAF50;
    color: white;
    border: 1px solid #4CAF50;
}

.pagination li a:hover:not(.active) {
    background-color: #ddd;
}

.hide {
    display: none;
}
#pjWrapperStoreLocator_theme1 .pjSlResults .pjSlResult
{
	padding:14px !important;
}
div#pagination {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
    padding: 3px 0;
}
.info-window {
    position: absolute;
    top: 10px; /* Adjust as needed */
    left: 10px; /* Adjust as needed */
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    z-index: 1000; /* Make sure it's above the map */
    max-width: 300px; /* Adjust width as needed */
    display: none; /* Initially hidden */
}
a.cloz_info {
    position: absolute;
    border: 1px solid #ccc;
    line-height: 16px;
    padding: 4px 7px;
    border-radius: 100%;
    background: #fff;
    color: #000;
}
.posright {
    right: 10px;
}

.posleft {
    left: 10px;
}
.pjSlListFilters input {
    width: 100% !important;
    max-width: unset;
    
}
</style>
<?php
mt_srand();
$index = mt_rand(1, 9999);

// $theme = isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme'];
$theme = 'theme1';
?>

<div id="pjWrapperStoreLocator_<?php echo $theme;?>">
	<div id="pjSlContainer_<?php echo $index;?>" class="container-fluid pjSlContainer">
		
	</div><!-- /.container-fluid pjSlContainer -->
</div>

<script type="text/javascript">
var styles = <?php echo $skin_contents; ?>;
var pjQ = pjQ || {},
	StoreLocator_<?php echo $index; ?>;
(function () {
	"use strict";
	
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),
		
	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo Uri::root(); ?>",
		folder: "<?php echo Uri::root(); ?>",
		index: <?php echo $index; ?>,

		zoom_level: <?php echo $zoom; ?>,
		default_address: "<?php echo $def; ?>",
		distance: "<?php echo $distance; ?>",
		use_categories: "Yes",
	
		search_form_name: "stl_seach_form",
		search_form_address_name: "stl_seach_form_add",
		search_form_search_name: "stl_search_form_search",
		search_form_address: "address",
		search_form_category: "category_id",
		search_form_radius: "radius",
	
		label_opening_time: "Opening Times",
		label_full_address: "Full Address",
		label_directions: "Directions",
		label_close: "Close",
		label_from: "From",
		label_address: "Address",
		label_go: "Go",
		label_phone: "Phone",
		label_email: "Email",
		label_website: "Website",
		label_not_found: "No locations in this area",
		label_address_not_found: "Address cannot be found",
		label_sent: "Sent",
		label_empty_email: "Empty Email",
		label_invalid_email: "Invalid Email",
		label_captcha_incorrect: "Incorrect Captacha",
		label_geo_not_supported: "GEO not supported",
	
		install_url: "<?php echo Uri::root(); ?>",
		generate_xml_url: "<?php echo Uri::root(); ?>index.php?option=com_store_locator&task=generateXml&controller=Locatormap&format=raw&id=<?php echo $this->item->id; ?>",
		get_latlng_url: "<?php echo Uri::root(); ?>index.php?option=com_store_locator&task=pjActionGetLatLng&controller=Locatormap&format=raw",
		send_email_url: "<?php echo Uri::root(); ?>index.php?option=com_store_locator&task=pjActionSendEmail&controller=Locatormap&format=raw"
	};
	<?php
	// $dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	// $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>

	// Initialize the pagination
	
	loadScript("<?php echo Uri::root(); ?>media/com_store_locator/js/storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo Uri::root(); ?>media/com_store_locator/js/pjQuery.min.js", function () {
			loadScript("<?php echo Uri::root(); ?>media/com_store_locator/js/pjQuery.bootstrap.min.js", function () {
				loadScript("<?php echo Uri::root(); ?>media/com_store_locator/js/pjLoad.js?id=<?php echo $this->item->id; ?>", function () {
					StoreLocator = StoreLocator(options);					
				});
			});
		});
	});
})();
</script>