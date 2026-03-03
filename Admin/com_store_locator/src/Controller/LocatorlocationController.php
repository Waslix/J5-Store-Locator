<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Locatorlocation controller class.
 *
 * @since  1.0.0
 */
class LocatorlocationController extends FormController
{
	protected $view_list = 'locatorlocations';
	
	public function locationimport()
	{
		// Check for request forgeries
		Session::checkToken() or die('Invalid Token');
		
		$model =  $this->getModel();
		$file = $this->input->files->get('csvfile');
		
		if (!empty($file['tmp_name'])) {
			if ($model->import($file['tmp_name'])) {
				$this->setMessage('File imported successfully');
			} else {
				$this->setMessage('Error importing file', 'error');
			}
		}
		
		$this->setRedirect('index.php?option=com_store_locator&view=locationimport');
	}

	public function locationexport()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$exp_format = $input->get('export_format', '', 'string');
		$opts = array(
			'format' => $exp_format,
		);
		$model = $this->getModel();
		$data = $model->getexportData($opts);
		
		$exn = 'csv';
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="component_export_' . date('Y-m-d') . '.'.$exn.'"');
		header('Content-Length: ' . strlen($data));
		header('Connection: close');
		
		echo $data;
		jexit();
	}

	public function getAllUsers()
	{
		$db = Factory::getDbo();
		
		try {
			$query = $db->getQuery(true);
			$query->select([
				$db->quoteName('id'),
				$db->quoteName('name'),
				$db->quoteName('username'),
				$db->quoteName('email')
			])
			->from($db->quoteName('#__users'))
			->order($db->quoteName('name'));
	
			$db->setQuery($query);
			return $db->loadObjectList();
		} catch (Exception $e) {
			Factory::getApplication()->enqueueMessage('Error retrieving users: ' . $e->getMessage(), 'error');
			return [];
		}
	}

	private function getCategories()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(['id', 'title', 'alias']))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_store_locator'))
			->order($db->quoteName('title') . ' ASC');
		
		$db->setQuery($query);
		
		try {
			$categories = $db->loadObjectList();
		} catch (Exception $e) {
			Factory::getApplication()->enqueueMessage('Error retrieving categories: ' . $e->getMessage(), 'error');
			return [];
		}
		
		return $categories;
	}

	/**
	 * Get an array of country names suitable for dropdown options.
	 * Keys and values are both the full country name (to match the stored data).
	 *
	 * @return array
	 */
	private function getCountryOptions()
	{
		// Use Joomla's built-in country list: returns an array of country_code => country_name
		$countries = HTMLHelper::_('select.countries');
		$options = array();
		foreach ($countries as $code => $name) {
			// Use the name as both key and value because the column stores names
			$options[$name] = $name;
		}
		return $options;
	}

	private function get_maps()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_maps'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private function getadminentries()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_admin_location_entries'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private function getresulttemplate()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_location_results'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private function getcardtemplates()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_card_template'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private function getlocationdetails()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_location_details'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function get_location_lists()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_location_lists'));
		
		$db->setQuery($query);
		$rs = null;
		$msg = '';
		$result = array();
		try {
			$result = $db->loadObjectList();
			$rs = true;
		} catch (RuntimeException $e) {
			$rs = false;
			$msg = $e->getMessage();
		}

		if($rs)
		{
			$html = '<li class="item item-level-1"><a class="no-dropdown ps-2" href="index.php?option=com_store_locator&view=locationmenulists" aria-label="Export"><span class="sidebar-item-title"><span>Location List</span></a>';
			$html .= '<ul class="parent_list">';
			foreach($result as $rs)
			{
				$html .= '<li class="item item-level-2"><a class="no-dropdown ps-2" href="index.php?option=com_store_locator&amp;view=locationmenulistsingle&id='.$rs->id.'">';
				$html .= '<span class="sidebar-item-title">'.$rs->title.'</span></a></li>';
			}
			$html .= '</ul>';
			$html .= '</li>';
			$response = array(
				'success' => $rs,
				'data' => $html
			);
		}
		else
		{
			$response = array(
				'success' => $rs,
				'message' => $msg
			);
		}
		echo json_encode($response);
		$app->close();
	}

	public function get_field_criteria() 
	{
		$app = Factory::getApplication();
		$input = $app->input;
		// Check for request forgeries
		Session::checkToken() or die('Invalid Token');
		$field_of = $input->get('field', '', 'string');
		$db = Factory::getDbo();
		$columns = $db->getTableColumns('#__store_locator_locations');
		$is_in_main = false;
		if(isset($columns[$field_of]))
		{
			$is_in_main = true;
		}
		$options = array();
		if($is_in_main)
		{
			if($field_of=="state")
			{
				$options = array(
					1	=> 'Published',
					0	=> 'Unpublished',
					2	=> 'Archived',
					-2	=> 'Trashed'
				);
				$type = 'options';
			}
			elseif(($field_of=='created_by') || ($field_of=='modified_by') || ($field_of=='user'))
			{
				$users = $this->getAllUsers();
				$options = array();
				foreach($users as $us)
				{
					$options[$us->id] = $us->name;
				}
				$type = 'options';
			}
			elseif(($field_of=='locationlistingtitle') || ($field_of=='email') || ($field_of=='website') || ($field_of=='phone') || ($field_of=='street') || ($field_of=='city') || ($field_of=='user_state') || ($field_of=='zip_code') || ($field_of=='latitude') || ($field_of=='longitude'))
			{
				$type = 'text';
			}
			elseif($field_of=='catid')
			{
				$cats = $this->getCategories();
				$options = array();
				foreach($cats as $ct)
				{
					$options[$ct->id] = $ct->title;
				}
				$type = 'options';
			}
			elseif($field_of=='country')
			{
				// Use Joomla's country list
				$options = $this->getCountryOptions();
				$type = 'options';
			}
			elseif($field_of=='map')
			{
				$mps = $this->get_maps();
				$options = array();
				foreach($mps as $ct)
				{
					$options[$ct->id] = $ct->maptitle;
				}
				$type = 'options';
			}
			elseif($field_of=='admin_entry')
			{
				$adm = $this->getadminentries();
				$options = array();
				foreach($adm as $ct)   // FIXED: was $mps
				{
					$options[$ct->id] = $ct->template_title;
				}
				$type = 'options';
			}
			elseif($field_of=='result_template')
			{
				$rsk = $this->getresulttemplate();
				$options = array();
				foreach($rsk as $ct)
				{
					$options[$ct->id] = $ct->template_title;
				}
				$type = 'options';
			}
			elseif($field_of=='card_template')
			{
				$crd = $this->getcardtemplates();
				$options = array();
				foreach($crd as $ct)
				{
					$options[$ct->id] = $ct->template_title;
				}
				$type = 'options';
			}
			elseif($field_of=='details_tempate')
			{
				$dtml = $this->getlocationdetails();
				$options = array();
				foreach($dtml as $ct)
				{
					$options[$ct->id] = $ct->template_title;
				}
				$type = 'options';
			}
		}
		else
		{
			$opdt = $this->get_custom_field_data($field_of);
			$dfm = $opdt[0];
			if(($dfm->type=='checkbox') || ($dfm->type=='list') || ($dfm->type=='radio'))
			{
				$prms = $dfm->params;
				$prms = json_decode($prms, true);
				$fopt = $prms['field_options']['text'];
				$fvl = $prms['field_options']['value'];
				foreach($fopt as $ke=>$fo)
				{
					$fm = $fvl[$ke];
					$options[$fm] = $fo;
				}
				$type = 'options';
			}
			else
			{
				$type = 'text';
			}
		}
		$response = array(
			'success' => true,
			'data' => $options,
			'type' => $type,
			'message' => ''
		);
		echo json_encode($response);
		$app->close();
	}

	private function get_custom_field_data($field) {
		$int = (int) filter_var($field, FILTER_SANITIZE_NUMBER_INT);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__store_locator_fields'))
			->where($db->quoteName('id') . ' = ' . $db->quote($int));
		
		$db->setQuery($query);

		try {
			$result = $db->loadObjectList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		return $result;
	}

	public function your_task()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		// Check for request forgeries
		Session::checkToken() or die('Invalid Token');
		
		$response = array(
			'success' => true,
			'data' => array(),
			'message' => ''
		);
		
		try {
			$street = $input->get('street', '', 'string');
			$city = $input->get('city', '', 'string');
			$state = $input->get('state', '', 'string');
			$country = $input->get('country', '', 'string');
			$zip = $input->get('zip', '', 'string');
			$address = array();
			$address[] = $zip;
			$address[] = $street;
			$address[] = $city;
			$address[] = $state;
			$address[] = $country;
	
			foreach ($address as $key => $value)
			{
				$tmp = preg_replace('/\s+/', '+', $value);
				$address[$key] = $tmp;
			}
			$_address = join(",+", $address);
			$geo = $this->getLatLng($_address);
			$response = array('code' => 100);
			if (isset($geo['lat']) && !is_array($geo['lat']))
			{
				$response['data'] = $geo;
				$response['code'] = 200;
			}
			
			echo json_encode($response);
		} catch (Exception $e) {
			$response['success'] = false;
			$response['message'] = $e->getMessage();
			echo json_encode($response);
		}
		
		$app->close();
	}

	public function getLatLng($address)
	{
		try {
			// Get API key from component parameters
			$config = ComponentHelper::getParams('com_store_locator');
			$key = $config->get('googlemapsapikey');
			
			// Build URL
			$url = sprintf(
				"https://maps.googleapis.com/maps/api/geocode/json?key=%s&address=%s&sensor=false",
				$key,
				urlencode($address)
			);
			
			// Send request
			$http = HttpFactory::getHttp();
			$response = $http->get($url);
			
			// Process response
			if ($response->code == 200) {
				$geoObj = json_decode($response->body);
				
				$data = [];
				if ($geoObj->status === 'OK') {
					$data['lat'] = $geoObj->results[0]->geometry->location->lat;
					$data['lng'] = $geoObj->results[0]->geometry->location->lng;
					$data['code'] = 200;
				} else {
					$data['lat'] = null;
					$data['lng'] = null;
					$data['code'] = 100;
				}
				
				return $data;
			}
		}
		catch (Exception $e) {
			// Handle errors
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return [
				'code' => 100,
				'lat' => null,
				'lng' => null,
				'error' => $e->getMessage()
			];
		}
	}
}