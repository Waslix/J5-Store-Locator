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

// echo "<pre>".print_r($this->details, true)."</pre>";
$temp = $this->details['template'];
$carr = $this->carr;
$v = $this->item;
$html = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($v,$carr) {
	$key = $matches[1];
	// First check if it's a custom field in $carr
	if (isset($carr[$key])) {
		$ty = $carr[$key]['type'];
		$dl = $carr[$key]['detail_link'];
		if($ty=='image')
		{
			if($dl)
			{
				return '<a href="'.Uri::root().'locations/'.$v->id.'"><img src="'.Uri::root(). $carr[$key]['value'].'"/></a>';
			}
			else
			{
				return '<img src="'.Uri::root(). $carr[$key]['value'].'"/>';
			}
			
		}
		else
		{
			if($dl)
			{
				return '<a href="'.Uri::root().'locations/'.$v->id.'">'.$carr[$key]['value'].'</a>';
			}
			else
			{
				return $carr[$key]['value'];
			}
			
		}
	}
	
	// Then handle regular fields
	switch ($key) {
		case 'locationlistingtitle':
		case 'street':
		case 'city':
		case 'user_state':
		case 'zip_code':
		case 'phone':
		case 'email':
		case 'website':
			return isset($v->$key) ? $v->$key : '';
		case 'country':
			return isset($v->$key) ? $this->getCountryName($v->$key) : '';
		case 'opening_times':
			return isset($v->$key) ? nl2br($v->$key) : '';
		case 'image':
			return isset($v->$key) ? '<img src="'.Uri::root() . $v->$key.'"/>' : '';
		case 'catid':
			return isset($v->$key) ? $this->getCategoryById($v->$key) : '';
		default:
			return $matches[0]; // Return original placeholder if no match
	}
}, $temp);

?>
<div class="single-location-listing <?php echo $this->details['css_class']; ?>">
	<?php echo $html; ?>
</div>