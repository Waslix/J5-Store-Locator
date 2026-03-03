<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Site\Controller;

\defined('_JEXEC') or die;

use \Joomla\CMS\Application\SiteApplication;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Multilanguage;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\Http\Http;
use \Joomla\Http\Transport\Curl;

/**
 * Locatorlocation class.
 *
 * @since  1.6.0
 */
class LocatormapController extends BaseController
{

	public function searchStores($location, $radius = 50)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // If searching by ZIP code
        if (is_numeric($location)) {
            $query->select('*')
                  ->from($db->quoteName('#__store_locator_locations'))
                  ->where($db->quoteName('zip_code') . ' = ' . $db->quote($location))
                  ->where($db->quoteName('state') . ' = 1');
        } else {
            // Use Google Geocoding API to get coordinates for the location
            $coordinates = $this->getCoordinatesFromAddress($location);
			
            if ($coordinates) {
                // Haversine formula for calculating distance
                $lat = $coordinates['lat'];
                $lng = $coordinates['lng'];

                $query->select('a.*');

				// Add the distance calculation as a select field
				$distanceCalc = '(3959 * 2 * ASIN(SQRT( POWER(SIN((' . $db->quote($lat) . ' - a.latitude) *  pi()/180 / 2), 2) ' .
								'+COS(' . $db->quote($lat) . ' * pi()/180) * COS(a.latitude * pi()/180) * ' .
								'POWER(SIN((' . $db->quote($lng) . ' - a.longitude) * pi()/180 / 2), 2) )))';

				$query->select($distanceCalc . ' AS distance');
				$query->from($db->quoteName('#__store_locator_locations') . ' AS a');
				$query->having('distance <= ' . (int)$radius);
				$query->order('distance');

            }
        }

        $db->setQuery($query);
        return $db->loadObjectList();
    }

	public function getCategoryById($catId)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select($db->quoteName(['id', 'title', 'alias', 'description', 'published']))
              ->from($db->quoteName('#__categories'))
              ->where($db->quoteName('id') . ' = ' . (int)$catId)
              ->where($db->quoteName('extension') . ' = ' . $db->quote('com_store_locator'));
              
        $db->setQuery($query);
        
        try {
            $rk = $db->loadObject();
			return $rk->title;
        } catch (Exception $e) {
            return null;
        }
    }

    private function getCoordinatesFromAddress($address)
    {
		$config = ComponentHelper::getParams('com_store_locator');
		$key = $config->get('googlemapsapikey');
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $key;
        
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data['status'] === 'OK') {
            return array(
                'lat' => $data['results'][0]['geometry']['location']['lat'],
                'lng' => $data['results'][0]['geometry']['location']['lng']
            );
        }
        
        return false;
    }
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function your_task()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$location = $input->get('location', '', 'string');
		$radius = $input->get('radius', '', 'string');
		$stores = $this->searchStores($location, $radius);
		$formatted = array();
        foreach ($stores as $store) {
			$csdata = $this->get_custom_data($store->id);
			$carr = array();
			foreach($csdata as $csd)
			{
				$carr['field_'.$csd->field_id] = $csd->field_value;
			}
			$cts = explode(",",$store->catid);
			$clist = array();
			foreach($cts as $cl)
			{
				$clist[] = $this->getCategoryById($cl);
			}
			
			$sd = implode(", ", $clist);

            $formatted[] = array(
                'id' => $store->id,
                'name' => $store->locationlistingtitle,
                'address' => $store->street,
				'city' => $store->city,
				'state' => $store->user_state,
                'zip_code' => $store->zip_code,
                'latitude' => (float)$store->latitude,
                'longitude' => (float)$store->longitude,
                'phone' => $store->phone,
                'email' => $store->email,
				'image' => $store->image,
				'category' => $sd,
                'distance' => isset($store->distance) ? round($store->distance, 1) : null,
				'custom_fields' => $this->get_custom_fields()
            );
			if (isset($carr)) {
				$formatted[count($formatted) - 1] = array_merge($formatted[count($formatted) - 1], $carr);
			}
        }
        
        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'stores' => $formatted
        ));
        
        $app->close();
	}

	private function get_custom_data($cid) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_custom_data'))
			->where($db->quoteName('location_id') . ' = ' . $db->quote($cid));
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
	
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		return $result;
	}

	private function get_loc_data($lid) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_locations'))
			->where($db->quoteName('id') . ' = ' . $db->quote($lid));
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
	
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		return $result;
	}
	
	private function get_custom_fields() {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_fields'));
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return $result;
	}

	public function edit()
	{
		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $this->app->getUserState('com_store_locator.edit.locatormap.id');
		$editId     = $this->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$this->app->setUserState('com_store_locator.edit.locatormap.id', $editId);

		// Get the model.
		$model = $this->getModel('Locatormap', 'Site');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId && $previousId !== $editId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_store_locator&view=locatormapform&layout=edit', false));
	}

	/**
	 * Method to save data
	 *
	 * @return    void
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public function publish()
	{
		// Checking if the user can remove object
		$user = $this->app->getIdentity();

		if ($user->authorise('core.edit', 'com_store_locator') || $user->authorise('core.edit.state', 'com_store_locator'))
		{
			$model = $this->getModel('Locatormap', 'Site');

			// Get the user data.
			$id    = $this->input->getInt('id');
			$state = $this->input->getInt('state');

			// Attempt to save the data.
			$return = $model->publish($id, $state);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(Text::sprintf('Save failed: %s', $model->getError()), 'warning');
			}

			// Clear the profile id from the session.
			$this->app->setUserState('com_store_locator.edit.locatormap.id', null);

			// Flush the data from the session.
			$this->app->setUserState('com_store_locator.edit.locatormap.data', null);

			// Redirect to the list screen.
			$this->setMessage(Text::_('COM_STORE_LOCATOR_ITEM_SAVED_SUCCESSFULLY'));
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();

			if (!$item)
			{
				// If there isn't any menu item active, redirect to list view
				$this->setRedirect(Route::_('index.php?option=com_store_locator&view=locatormaps', false));
			}
			else
			{
				$this->setRedirect(Route::_('index.php?Itemid='. $item->id, false));
			}
		}
		else
		{
			throw new \Exception(500);
		}
	}

	/**
	 * Check in record
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function checkin()
	{
		// Check for request forgeries.
		$this->checkToken('GET');

		$id 	= $this->input->getInt('id', 0);
		$model 	= $this->getModel();
		$item 	= $model->getItem($id);

		// Checking if the user can remove object
		$user = $this->app->getIdentity();

		if ($user->authorise('core.manage', 'com_store_locator') || $item->checked_out == $user->id) { 

			$return = $model->checkin($id);

			if ($return === false)
			{
				// Checkin failed.
				$message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
				$this->setRedirect(Route::_('index.php?option=com_store_locator&view=locatormap' . '&id=' . $id, false), $message, 'error');
				return false;
			}
			else
			{
				// Checkin succeeded.
				$message = Text::_('COM_STORE_LOCATOR_CHECKEDIN_SUCCESSFULLY');
				$this->setRedirect(Route::_('index.php?option=com_store_locator&view=locatormap' . '&id=' . $id, false), $message);
				return true;
			}
		}
		else
		{
			throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	/**
	 * Remove data
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function remove()
	{
		// Checking if the user can remove object
		$user = $this->app->getIdentity();

		if ($user->authorise('core.delete', 'com_store_locator'))
		{
			$model = $this->getModel('Locatormap', 'Site');

			// Get the user data.
			$id = $this->input->getInt('id', 0);

			// Attempt to save the data.
			$return = $model->delete($id);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(Text::sprintf('Delete failed', $model->getError()), 'warning');
			}
			else
			{
				// Check in the profile.
				if ($return)
				{
					$model->checkin($return);
				}

				$this->app->setUserState('com_store_locator.edit.locatormap.id', null);
				$this->app->setUserState('com_store_locator.edit.locatormap.data', null);

				$this->app->enqueueMessage(Text::_('COM_STORE_LOCATOR_ITEM_DELETED_SUCCESSFULLY'), 'success');
				$this->app->redirect(Route::_('index.php?option=com_store_locator&view=locatormaps', false));
			}

			// Redirect to the list screen.
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();
			$this->setRedirect(Route::_($item->link, false));
		}
		else
		{
			throw new \Exception(500);
		}
	}
	
	public function pjActionGetLatLng()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$_address = $input->get('address');
		$_address = preg_replace('/\s+/', '+', $_address);
		
		$data = $this->getLatLng($_address);
		header('Content-Type: application/json');
		echo json_encode($data);
		$app->close();
	}
	
	public function pjgetcarddata()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$locid = $input->get('id');
		$locdata = $this->get_loc_data($locid);
		$lc = $locdata[0];
		if(!empty($lc->card_template))
		{
			$card = $lc->card_template;
		}
		else
		{
			$card = $input->get('card');
		}
		
		$v = $this->singlestoredata($locid);
		$card_data = $this->locationcarddata($card);
		$csfd = $this->get_custom_fields();
		$csdata = $this->get_custom_data($locid);
		$carr = array();
		foreach($csdata as $csd)
		{
			$this_fd = array();
			$nm = 'field_'.$csd->field_id;
			foreach($csfd as $cf) {
				if($cf->id == $csd->field_id) {
					$this_fd = $cf;
					$prm = $cf->params;
					$lk = json_decode($prm,true);
					if(!empty($lk['name']))
					{
						$nm = $lk['name'];
					}
				}
			}
			$carr[$nm]['value'] = $csd->field_value;
			$carr[$nm]['type'] = $this_fd->type;
			$carr[$nm]['detail_link'] = $this_fd->detail_link;
		}
		$html = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($v,$carr) {
			$key = $matches[1];
			// First check if it's a custom field in $carr
			if (isset($carr[$key])) {
				$ty = $carr[$key]['type'];
				$dl = $carr[$key]['detail_link'];
				if(!empty($carr[$key]['value']))
				{
					if($ty=='image')
					{
						if($dl)
						{
							return '<a href="'.Uri::root().'locations/'.$v['id'].'"><img src="'.Uri::root(). $carr[$key]['value'].'"/></a>';
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
							return '<a href="'.Uri::root().'locations/'.$v['id'].'">'.$carr[$key]['value'].'</a>';
						}
						else
						{
							return $carr[$key]['value'];
						}
						
					}
				}
				else
				{
					return "";
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
					return isset($v[$key]) ? $v[$key] : '';
				case 'country':
					// Return the stored value directly (now a country name)
					return isset($v[$key]) ? $v[$key] : '';
				case 'opening_times':
					return isset($v[$key]) ? nl2br($v[$key]) : '';
				case 'image':
					return isset($v[$key]) ? '<img src="'.Uri::root() . $v[$key].'"/>' : '';
				case 'catid':
					return isset($v[$key]) ? $this->getCategoryById($v[$key]) : '';
				default:
					return $matches[0]; // Return original placeholder if no match
			}
		}, $card_data->template);
		$data = array(
			'status' => 'success',
			'data' => $html,
		);
		echo json_encode($data);
		die;
	}

	public function generateXml()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$map_id = $input->get('id');
		$model 	= $this->getModel();
		$mapData = $model->getItem($map_id);
		$list_res = $mapData->list_results;
		$listr = json_decode($list_res,true);
		$locres = $this->locationresultdata($mapData->location_result);

		$loccard = $this->locationcarddata($mapData->location_card);

		$map_filter = $mapData->filter;
		$filter_data = $this->locationfilterdata($map_filter);
		$fdat = $filter_data->filter_data;
		$fdm = json_decode($fdat,true);
		$filterConditions = [];
		foreach ($fdm as $key => $s) {
			$fdp = $s['field'];
			if($input->exists($fdp))
			{
				$vm = $input->get($fdp);
				$filterConditions[$fdp] = $vm;
			}
		}
		$temp = $locres->template;
		$custm_dt = $this->get_custom_fields();
		$loc_card_temp = $loccard->template;
		$db = Factory::getDbo();
		
		// Get component parameters
		$params = ComponentHelper::getParams('com_store_locator');
		$micn = $mapData->marker_icon;

		$maricn =  Uri::root().$micn;
		// Get latitude and longitude
		if (is_numeric($input->get('lat')) && is_numeric($input->get('lng'))) {
			$center_lat = $input->get('lat');
			$center_lng = $input->get('lng');
		} else {
			// Use default address from component parameters
			$default_address = $params->get('defaultaddress', '');
			$default_address = preg_replace('/\s+/', '+', $default_address);
			
			// Get coordinates using Google Geocoding
			$coordinates = $this->getCoordinatesFromAddress($default_address);
			if ($coordinates) {
				$center_lat = $coordinates['lat'];
				$center_lng = $coordinates['lng'];
			} else {
				// Set default coordinates if geocoding fails
				$center_lat = 0;
				$center_lng = 0;
			}
		}

		// Get radius and distance unit
		$radius = is_numeric($input->get('radius')) ? $input->get('radius') : 25;
		$distance = 'miles';
		if($params->get('distance_unit')==2)
		{
			$distance = 'km';
		}		

		// Set mean radius based on distance unit
		$mean_radius = ($distance === 'km') ? 6371 : 3959;

		// Build the query
		$query = $db->getQuery(true);

		// Select fields including distance calculation
		$distanceCalc = "($mean_radius * acos(cos(radians($center_lat)) * cos(radians(latitude)) * " .
						"cos(radians(longitude) - radians($center_lng)) + sin(radians($center_lat)) * " .
						"sin(radians(latitude))))";

		$query->select([
			'a.*',
			'c.title AS category_name',
			$distanceCalc . ' AS distance'
		]);
		$query->from($db->quoteName('#__store_locator_locations', 'a'));
		
		// Join with categories
		$query->join(
			'LEFT',
			$db->quoteName('#__categories', 'c') . ' ON c.id = a.catid'
		);

		$query->join(
			'INNER',
			$db->quoteName('#__store_locator_custom_data', 'cd') . ' ON cd.location_id = a.id'
		);

		// Where conditions
		$query->where('a.state = 1');
		$query->where('FIND_IN_SET('.$map_id.', ' . $db->quoteName('a.map') . ') > 0');
		foreach($filterConditions as $key => $value)
		{
			preg_match('/field_(\d+)/', $key, $matches);
    
			if (!isset($matches[1])) {
				$query->where($db->quoteName($key).' = '. $db->quote($value));
			}		
			else
			{
				$query->where($db->quoteName('cd.field_id') . ' = ' . $db->quote($matches[1]))
          		->where($db->quoteName('cd.field_value') . ' LIKE ' . $db->quote('%' . $value . '%'));
			}
			
		}
		
		// Category filter
		$category_id = $input->get('category_id', '', 'STRING');
		if (!empty($category_id) && $this->validateIds($category_id)) {
			$query->where('a.catid IN (' . $category_id . ')');
		}

		// Filter handling (if you have additional filters)
		$filter_id = $input->get('filter_id', '', 'STRING');
		if (!empty($filter_id) && $this->validateIds($filter_id)) {
			// Add your filter conditions here
		}

		// Having clause for distance
		$query->having('distance < ' . (int)$radius);

		if(!empty($listr))
		{
			$orderConditions = [];
			foreach($listr as $lr)
			{
				$arrang_field = $lr['field'];
				$argodr = $lr['order'];
				$odr = $argodr == 1 ? 'ASC' : 'DESC';
				$orderConditions[] = $db->quoteName('a.' . $arrang_field) . ' ' . $odr;
			}
			$query->order(implode(', ', $orderConditions));
		}
		$query->group($db->quoteName('a.id'));
		// Execute query
		$db->setQuery($query);
		
		$results = $db->loadAssocList();
		$tm = array();
		$tk = array();
		ob_start();
		$replacements = array();
		$arr = array();
		if(count($results) > 0)
		{
			?>
			<ul class="list-group">
				<?php
				foreach($results as $k => $v)
				{
					$csdata = $this->get_custom_data($v['id']);
					$carr = array();
					foreach($csdata as $csd)
					{
						$this_fd = array();
						$nm = 'field_'.$csd->field_id;
						foreach($custm_dt as $cf) {
							if($cf->id == $csd->field_id) {
								$this_fd = $cf;
								$prm = $cf->params;
								$lk = json_decode($prm,true);
								if(!empty($lk['name']))
								{
									$nm = $lk['name'];
								}
							}
						}
						$carr[$nm]['value'] = $csd->field_value;
						$carr[$nm]['type'] = $this_fd->type;
						$carr[$nm]['detail_link'] = $this_fd->detail_link;
					}
					$address = array();
					$address[] = $v['country'];   // Use raw value, not getCountryName
					$address[] = $v['user_state'];
					$address[] = $v['city'];
					$address[] = $v['street'];
					$address[] = $v['zip_code'];
					$address = array_filter($address, 'strlen');
					$_address = join(", ", $address);
				
					$v["address"] = $_address;
					$v["img_tag"] = ''; 
					$v['marker_content'] = ''; 
					?>
					<li class="list-group-item pjSlResult stl-store-item" lang="<?php echo $k?>" data-id="<?php echo $v['id']; ?>" data-card="<?php echo $mapData->location_card; ?>">
						<div class="<?php echo $locres->css_class; ?>">
						<?php
						
						$tm[$k] = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($v,$carr) {
							$key = $matches[1];
							if (isset($carr[$key])) {
								$ty = $carr[$key]['type'];
								$dl = $carr[$key]['detail_link'];
								if(!empty($carr[$key]['value']))
								{
									if($ty=='image')
									{
										if($dl)
										{
											return '<a href="'.Uri::root().'locations/'.$v['id'].'"><img src="'.Uri::root(). $carr[$key]['value'].'"/></a>';
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
											return '<a href="'.Uri::root().'locations/'.$v['id'].'">'.$carr[$key]['value'].'</a>';
										}
										else
										{
											return $carr[$key]['value'];
										}
									}
								}
								else
								{
									return "";
								}
								
							}
							switch ($key) {
								case 'locationlistingtitle':
								case 'street':
								case 'city':
								case 'user_state':
								case 'zip_code':
								case 'phone':
								case 'email':
								case 'website':
									return isset($v[$key]) ? $v[$key] : '';
								case 'country':
									return isset($v[$key]) ? $v[$key] : '';
								case 'opening_times':
									return isset($v[$key]) ? nl2br($v[$key]) : '';
								case 'image':
									return isset($v[$key]) ? '<img src="'.Uri::root() . $v[$key].'"/>' : '';
								case 'catid':
									return isset($v[$key]) ? $this->getCategoryById($v[$key]) : '';
								default:
									return $matches[0];
							}
						}, $temp);
						?>						
						<?php  echo $tm[$k]; ?>
						</div>
					</li>
					<?php
					$tk[$k] = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($v, $carr) {
						$key = $matches[1];
						if (isset($carr[$key])) {
							$ty = $carr[$key]['type'];
							$dl = $carr[$key]['detail_link'];
							if($ty=='image')
							{
								if($dl)
								{
									return '<a href="'.Uri::root().'locations/'.$v['id'].'"><img src="'.Uri::root(). $carr[$key]['value'].'"/></a>';
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
									return '<a href="'.Uri::root().'locations/'.$v['id'].'">'.$carr[$key]['value'].'</a>';
								}
								else
								{
									return $carr[$key]['value'];
								}
							}
						}
						
						switch ($key) {
							case 'locationlistingtitle':
							case 'street':
							case 'city':
							case 'user_state':
							case 'zip_code':
							case 'phone':
							case 'email':
							case 'website':
								return isset($v[$key]) ? $v[$key] : '';
							case 'country':
								return isset($v[$key]) ? $v[$key] : '';
							case 'opening_times':
								return isset($v[$key]) ? nl2br($v[$key]) : '';
							case 'image':
								return isset($v[$key]) ? '<img src="'.Uri::root() . $v[$key].'"/>' : '';
							case 'catid':
								return isset($v[$key]) ? $this->getCategoryById($v[$key]) : '';
							default:
								return $matches[0];
						}
					}, $loc_card_temp);
					
					$marker_content_arr = array();

					$arr[$k] = array('name' => $v['locationlistingtitle'], 'lat' => $v['latitude'], 'lng' => $v['longitude'], 'distance' => $v['distance'], 'marker' => $maricn, 'marker_content' => $tk[$k]);
				}
				?>
			</ul>
			<div id="pagination"></div>
			<?php
		}else{
			?>
			<ul class="list-group">
				<li class="list-group-item pjSlResult">Not Found</li>
			</ul>
			<?php
		}
		$ob_store_list = ob_get_contents();
		ob_end_clean();
		$arr[count($results)] = array('store_list' => '');
		if (!empty($ob_store_list))
		{
			$arr[count($results)] = array('store_list' => $ob_store_list);
		}

		// Output response
		header('Content-Type: application/json');
		echo json_encode($arr);
		$app->close();
	}

	public function pjActionSendEmail() {
		return '';
	}

	// Helper method to validate comma-separated IDs
	private function validateIds($ids)
	{
		$id_array = explode(',', $ids);
		foreach ($id_array as $id) {
			if (!is_numeric($id)) {
				return false;
			}
		}
		return true;
	}

	public function pjActionLoadCss()
	{
		$theme = 'theme1';
		$fonts = 'theme1';
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$theme = $_GET['theme'];
			$fonts = $_GET['theme'];
		}
		$arr = array(
				array('file' => 'style.css', 'path' => Uri::root().'media/com_store_locator/css/'),
				array('file' => "$fonts.css", 'path' => Uri::root() . "media/com_store_locator/css/fonts"),
				array('file' => "$theme.css", 'path' => Uri::root() . "media/com_store_locator/css/themes"),
				array('file' => 'transitions.css', 'path' => Uri::root().'media/com_store_locator/css/')
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
				
			if ($string !== FALSE)
			{
				echo str_replace(
						array('../fonts/glyphicons', "pjWrapper"),
						array(
								Uri::root() . 'media/com_store_locator/css/fonts/glyphicons',
								"pjWrapperStoreLocator_" . $theme
						),
						$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLoadJs()
	{
		header("Content-type: text/javascript");
		
		$params = ComponentHelper::getParams('com_store_locator');

		$api_key_str = 'key=' . $params->get('googlemapsapikey') . '&';
		
		$arr = array(
			array('file' => '', 'path' => 'https://maps.google.com/maps/api/js?'.$api_key_str.'sensor=false&language=en')
		);
		$options = new \Joomla\Registry\Registry;
		$transport = new Curl($options);
		$http = new Http($options, $transport);
		foreach ($arr as $item)
		{
			$js_content = $http->get($item['path'] . $item['file']);
			echo $js_content->body . "\n";
		}
		exit;
	}

	public function mapthemedata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_map_themes'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function locationfilterdata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_frontend_filter'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function mapskindata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_map_skins'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}
	protected function locationresultdata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_location_results'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}
	public function locationcarddata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_card_template'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}
	public function locationdetailsdata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_location_details'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function singlestoredata($pk = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_locations'))
		->where($db->quoteName('id') . ' = ' . (int) $pk);
		$db->setQuery($query);
		return $db->loadAssoc();
	}

	public function locatorlocations()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_locations'));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private function get_filter_terms($field) 
	{ 
		$db = Factory::getDbo(); 
		$columns = $db->getTableColumns('#__store_locator_locations');
	
		// Check if the column exists in the main locations table
		if (isset($columns[$field])) {
			$query = $db->getQuery(true); 
			$query->select('DISTINCT ' . $field) 
				  ->from($db->quoteName('#__store_locator_locations')) 
				  ->where($db->quoteName('state') . ' = 1')
				  ->order($db->quoteName($field) . ' ASC'); 
	
			$db->setQuery($query); 
			
			try {
				$results = $db->loadColumn();
				return array_filter($results, 'strlen');
			} catch (RuntimeException $e) {
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error'); 
				return []; 
			}
		}
		else
		{
			$int = (int) filter_var($field, FILTER_SANITIZE_NUMBER_INT);
			$query = $db->getQuery(true);
			$query->select('*')
				  ->from($db->quoteName('#__store_locator_fields'))
				  ->where($db->quoteName('id') . ' = ' . $db->quote($int))
				  ->where($db->quoteName('state') . ' = 1')
				  ->order($db->quoteName('title') . ' ASC');
		
			$db->setQuery($query);
		
			$results = $db->loadAssocList();
			if(!empty($results))
			{
				$rs = $results[0];
				if(($rs['type']=='list') || ($rs['type']=='checkbox') || ($rs['type']=='radio'))
				{
					$pr = $rs['params'];
					$bm = json_decode($pr,true);
					$fopt = $bm['field_options'];
					return $fopt['value'];
				}
			}
			else
			{
				return "";
			}
		}

	} 

	public function pjActionGetLocations()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$map_id = $input->get('id');
		$model = $this->getModel();
		$mapData = $model->getItem($map_id);
		$map_width = $mapData->map_width;
		$res_width = $mapData->results_width;
		$map_order = $mapData->map_order;
		$location_order = $mapData->location_order;
		$radius_search = $mapData->radius_search;
		$map_filter = $mapData->filter;
		$filter_data = $this->locationfilterdata($map_filter);
		$fdat = $filter_data->filter_data;
		$params = ComponentHelper::getParams('com_store_locator');

		$def = $mapData->defaultaddress;
		$ip = $mapData->ip; //
		if($ip ==1){
			$ipaddress = getenv("REMOTE_ADDR");


			if(!empty($ipaddress))
			{
				$urlss = "http://ip-api.com/json/$ipaddress";
				$fdtqq = file_get_contents($urlss);
				if(!empty($fdtqq))
				{
					$dstjs = json_decode($fdtqq,true);
					$def = $dstjs['city'].', '.$dstjs['region'].', '.$dstjs['countryCode'].' - '.$dstjs['zip'];
				}
			}

		}else{
			$def = $mapData->ip_address;
		}

		$radunit = $mapData->distance_unit;
		$radius_data = $mapData->filter_radius;
		$radius_data = explode(',', $radius_data);
		$unit = 'miles';
		if($radunit==2)
		{
			$unit ='km';
		}
		$db = Factory::getDbo();

		// Get categories
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = '  . $db->quote('com_store_locator'))
			->order($db->quoteName('title') . ' ASC');
		$db->setQuery($query);
		$category_arr = $db->loadAssocList();

		$filter_arr = array();
		$loccard = $this->locationcarddata($map_id); 
		$open_post = $loccard->open_position;
		$close_position = $loccard->close_position;
		$templt = $loccard->template;
		
		ob_start();
		?>
		<div class="store-heading">
			<?php if($mapData->showmaptitle) { ?>
				<h3><?php echo $mapData->maptitle; ?></h3>
			<?php } ?>
		</div>
		<div class="panel panel-default pjSlMain">
			<header class="panel-heading clearfix pjSlHeader">
				<div class="pjSlForm pjSlFormFilters">
					<form action="" method="post" name="stl_seach_form" class="form-inline" onsubmit="return false;">
						<div class="form-group pjSlFilterAddress">
							<div class="input-group">
								<input type="text" name="address" value="<?php echo $def;?>" class="form-control" placeholder="Search by address, zip or name" />
								
								<a href="#" id="stl_current_location" class="input-group-addon" title="Current Location">
									<span class="glyphicon glyphicon-screenshot" aria-hidden="aria-hidden"></span>
								</a>
								
								<a href="#" class="input-group-addon pjSlSearchIcon">
									<span class="glyphicon glyphicon-search" aria-hidden="aria-hidden"></span>
								</a>
							</div><!-- /.input-group -->
						</div><!-- /.form-group pjSlFilterAddress -->

						<div class="form-group pjSlFilterDistance">
							<select name="radius" class="form-control">
								<?php foreach($radius_data as $radius) { 
									if(($radius==1) && ($unit=='miles'))
									{
										$unit = 'mile';
									}
									?>
									<option <?php if($radius_search==$radius) { ?>selected="selected" <?php } ?> value="<?php echo $radius; ?>">within <?php echo $radius; ?> <?php echo $unit; ?></option>
								<?php } ?>				
							</select>
						</div><!-- /.form-group pjSlFilterDistance -->
						<?php
						if(!empty($category_arr) || !empty($filter_arr))
						{ 
							?>
							<div class="form-group">
								<a href="#pjSlFormFiltersDropdown" role="button" data-toggle="collapse" class="btn btn-primary pjSlBtnFilterBy" aria-expanded="false" aria-controls="pjSlFormFiltersDropdown">Filter Results</a>
							</div><!-- /.form-group -->
						
							<div class="collapse pjSlFormFiltersDropdown" id="pjSlFormFiltersDropdown">
								<?php
								if(!empty($category_arr))
								{ 
									?>
									<input type="hidden" name="category_id" valuue="1" />
									<p class="pjSlFormFiltersDropdownTitle">Category: </p><!-- /.pjSlFormFiltersDropdownTitle -->

									<ul class="list-inline pjSlListFilters">
										<?php
										foreach ($category_arr as $k => $v)
										{ 
											?>
											<li>
												<div class="pjSlCheckbox pjSlCustomCheckbox">
													<input type="checkbox" id="pjSlCustomCheckbox-category-<?php echo $v['id'];?>" value="<?php echo $v['id'];?>" class="pjSlCategoryCheckbox" autocomplete="off" />
			
													<label for="pjSlCustomCheckbox-category-<?php echo $v['id'];?>">
														<span class="pjSlCustomCheckboxFake">
															<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
														</span>
			
														<?php echo $v['title']; ?>
													</label>
												</div><!-- /.pjSlCheckbox pjSlCustomCheckbox -->
											</li>
											<?php
										} 
										?>
									</ul><!-- /.list-inline pjSlListFilters -->
									<?php
								}
								if(!empty($fdat))
								{ 
									$farr = json_decode($fdat,true);
									?>
									<p class="pjSlFormFiltersDropdownTitle">Fields: </p><!-- /.pjSlFormFiltersDropdownTitle -->

									<div class="pjSlListFilters row">
										<?php
										foreach($farr as $k => $vi)
										{ 
											$filter_datarr = $this->get_filter_terms($vi['field']);
											?>
											<div class="col-md-<?php echo $vi['width']; ?> singlfilter" data-id="<?php echo $vi['field']; ?>">
												<div class="form-group">
													<label><?php echo $vi['heading']; ?></label>
													<?php if(empty($filter_datarr)) { ?>
														<input class="form-control" name="filter[<?php echo $vi['field']; ?>]" id="filter"/>
														<?php } else { ?>
													<select name="filter[<?php echo $vi['field']; ?>]" id="filter" class="form-control">
														<option value="">Select</option>
														<?php foreach ($filter_datarr as $fa) { ?>
															<option value="<?php echo $fa; ?>"><?php echo $fa; ?></option>
														<?php } ?>
													</select>
													<?php } ?>
												</div>
											</div>
											<?php
										} 
										?>
									</div><!-- /.list-inline pjSlListFilters -->
									<?php 
								} 
								?>

								<div class="pjSlFormFiltersDropdownActions">
									<button type="submit" class="btn btn-primary pjSlSearchIcon">Apply Filters</button>
									<a href="#" class="pjSlClearFilters">Clear Filters</a>
								</div><!-- /.pjSlFormFiltersDropdownActions -->
							</div><!-- /#pjSlFormFiltersDropdown.collapse pjSlFormFiltersDropdown -->
							<?php
						} 
						?>
					</form><!-- /.form-inline -->
				</div><!-- /.pjSlForm pjSlFormFilters -->
			</header><!-- /.panel-heading clearfix pjSlHeader -->
			
			<div class="panel-body pjSlBody">
				<div class="row">
					<?php
					if($location_order==1)
					{
						if(!empty($params->get('googlemapsapikey')))
						{ 
							?>
							<div class="col-md-<?php echo $res_width; ?> pjSlResults">
								<div id="stl_search_result">
									<div id="stl_search_addresses" class="pre-scrollable pjSlResultsInner"></div>
								</div>
								<div id="stl_search_directions" style="display: none">
									<div class="pre-scrollable pjSlResultsInner">
										<div class="pjSlGoogleApi">
											<div class="pjSlAccordionActions">
												<a href="#" class="btnBack stl-directions-close">Close</a>
	
												<a role="button" data-toggle="collapse" href="#sendByEmail" aria-expanded="false" aria-controls="sendByEmail">
													<span class="pjSlAccordionSign pjSlAccordionOpen">+</span>
													<span class="pjSlAccordionSign pjSlAccordionClose">-</span>
													
													<span>Email</span>
												</a>
											</div><!-- /.pjSlAccordionActions -->
	
											<div class="pjSlForm pjSlFormDirection pjSlFormEmail collapse" id="sendByEmail">
												<form action="" method="post" id="stl_send_email_form" name="stl_send_email_form" class="form-inline" onsubmit="return false;">
													<div class="row">
														<div class="col-sm-12">
															<input type="text" name="stl_email_text" placeholder="Email" class="form-control pjSlEmailField" />
															
															<textarea id="stl_directions_html" name="stl_directions_html" class="stl-direction-html" style="display: none;"></textarea>
															
															<button type="button" id="stl_send_email" name="stl_send_email"  class="btn btn-primary">Go</button>
														</div>
													</div>
												
												</form><!-- /.form-inline -->
											</div><!-- /#sendByEmail.pjSlForm pjSlFormDirection collapse -->
	
											<div id="stl_search_directions_panel"></div>
										</div><!-- /.pjSlGoogleApi -->
									</div><!-- /.pre-scrollable pjSlResultsInner -->
								</div>
							</div><!-- /.col-lg-5 col-md-5 col-sm-6 col-xs-12 pjSlResults -->
							
							<div class="col-md-<?php echo $map_width; ?> " id="">
								<?php if($open_post==1) { ?>
									<div id="info-window" class="info-window" style="display: none;">
										<?php if($close_position==1) { ?>
										<a href="#" class="cloz_info posleft">&times</a>
										<?php } 
										elseif($close_position==2) { ?>
										<a href="#" class="cloz_info posright">&times</a>
										<?php } ?>
										<div class="info-inner" id="info-content">
										</div>
									</div>
								<?php } ?>
								<div class="pjSlMap" id="stl_store_canvas"></div>
							</div><!-- /#pjSlMap.col-lg-7 col-md-7 col-sm-6 col-xs-12 pjSlMap -->
							<?php
						}
						
					}
					else
					{
						if(!empty($params->get('googlemapsapikey')))
						{ 
							?>
							<div class="col-md-<?php echo $map_width; ?>">
								<?php if($open_post==1) { ?>
									<div id="info-window" class="info-window shadow-lg" style="display: none;">
										<?php if($close_position==1) { ?>
										<a href="#" class="cloz_info posleft">&times</a>
										<?php } 
										elseif($close_position==2) { ?>
										<a href="#" class="cloz_info posright">&times</a>
										<?php } ?>
										<div class="info-inner" id="info-content">
										</div>
									</div>
								<?php } ?>
								<div class="pjSlMap" id="stl_store_canvas"></div>
							</div>

							<div class="col-md-<?php echo $res_width; ?> pjSlResults">
								<div id="stl_search_result">
									<div id="stl_search_addresses" class="pre-scrollable pjSlResultsInner"></div>
								</div>
								<div id="stl_search_directions" style="display: none">
									<div class="pre-scrollable pjSlResultsInner">
										<div class="pjSlGoogleApi">
											<div class="pjSlAccordionActions">
												<a href="#" class="btnBack stl-directions-close">Close</a>
	
												<a role="button" data-toggle="collapse" href="#sendByEmail" aria-expanded="false" aria-controls="sendByEmail">
													<span class="pjSlAccordionSign pjSlAccordionOpen">+</span>
													<span class="pjSlAccordionSign pjSlAccordionClose">-</span>
													
													<span>Email</span>
												</a>
											</div><!-- /.pjSlAccordionActions -->
	
											<div class="pjSlForm pjSlFormDirection pjSlFormEmail collapse" id="sendByEmail">
												<form action="" method="post" id="stl_send_email_form" name="stl_send_email_form" class="form-inline" onsubmit="return false;">
													<div class="row">
														<div class="col-sm-12">
															<input type="text" name="stl_email_text" placeholder="Email" class="form-control pjSlEmailField" />
															
															<textarea id="stl_directions_html" name="stl_directions_html" class="stl-direction-html" style="display: none;"></textarea>
															
															<button type="button" id="stl_send_email" name="stl_send_email"  class="btn btn-primary">Go</button>
														</div>
													</div>
												
												</form><!-- /.form-inline -->
											</div><!-- /#sendByEmail.pjSlForm pjSlFormDirection collapse -->
	
											<div id="stl_search_directions_panel"></div>
										</div><!-- /.pjSlGoogleApi -->
									</div><!-- /.pre-scrollable pjSlResultsInner -->
								</div>
							</div><!-- /.col-lg-5 col-md-5 col-sm-6 col-xs-12 pjSlResults -->
							
							
							<?php
						}
					}
					?>
					
				</div><!-- /.row -->
			</div><!-- /.panel-body pjSlBody -->
			
		</div><!-- /.panel panel-default pjSlMain -->
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		$app->close();
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
}