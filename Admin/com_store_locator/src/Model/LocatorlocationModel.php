<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Filter\OutputFilter;
use \Joomla\CMS\Event\Model;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Event\AbstractEvent;


/**
 * Locatorlocation model.
 *
 * @since  1.0.0
 */
class LocatorlocationModel extends AdminModel
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 *
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_STORE_LOCATOR';

	/**
	 * @var    string  Alias to manage history control
	 *
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_store_locator.locatorlocation';

	/**
	 * @var    null  Item data
	 *
	 * @since  1.0.0
	 */
	protected $item = null;

	
	public function import($file)
	{
		$db = Factory::getDbo();
		$input = Factory::getApplication()->input;
		$fieldMapping = $input->get('field_mapping', array(), 'array');

		// Get existing table columns
		$columns = $db->getTableColumns('#__store_locator_locations');

		// Open file
		if (($handle = fopen($file, "r")) !== FALSE) {
			// Get headers
			$headers = fgetcsv($handle);
			
			while (($data = fgetcsv($handle)) !== false) {
				$mappedData = array();
				foreach ($fieldMapping as $targetField => $sourceField) {
					// Check if the target column exists in the database table
					if (!array_key_exists($targetField, $columns)) {
						// Skip columns that don't exist in the table
						continue;
					}

					$sourceIndex = array_search($sourceField, $headers);
					if ($sourceIndex !== false) {
						$mappedData[$targetField] = $data[$sourceIndex];
					}
				}

				// Only insert if there's data to insert
				if (!empty($mappedData)) {
					// Insert mapped data into database
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__store_locator_locations'))
						->columns($db->quoteName(array_keys($mappedData)))
						->values(implode(',', array_map(array($db, 'quote'), $mappedData)));
					
					try {
						$db->setQuery($query);
						$db->execute();
					} catch (Exception $e) {
						// Log or handle the error
						Factory::getApplication()->enqueueMessage('Error importing row: ' . $e->getMessage(), 'error');
					}
				}
			}
			
			fclose($handle);
			return true;
		}
		return false;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table    A database object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Locatorlocation', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Get the form.
		$form = $this->loadForm(
								'com_store_locator.locatorlocation', 
								'locatorlocation',
								array(
									'control' => 'jform',
									'load_data' => $loadData 
								)
							);

		

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_store_locator.edit.locatorlocation.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
			
		}

		return $data;
	}
	/**
     * Export data based on provided options
     *
     * @param   array  $options  Export options
     * @return  mixed  Exported data in specified format or false on failure
     */
    public function getexportData($options = array()) {
        // Default options
        $defaultOptions = array(
            'format' => 'json',           // json, csv, xml
            'range' => 'all',             // all, selected, date
            'date_start' => null,
            'date_end' => null,
            'selected_ids' => array(),
            'include_metadata' => true,
            'include_relations' => true
        );
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            // Get data based on range
            $data = $this->getData($options);
            
            // Add metadata if requested
            if ($options['include_metadata']) {
              //  $data = $this->addMetadata($data);
            }
            
            // Add related data if requested
            if ($options['include_relations']) {
              //  $data = $this->addRelatedData($data);
            }
            
            // Format the data
            return $this->formatData($data, $options['format']);
            
        } catch (Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Get data based on specified range
     *
     * @param   array  $options  Export options
     * @return  array  Data to export
     */
	protected function getData($options) {
		$db = $this->getDbo();
		
		// Configurable export options
		$exportOptions = [
			'excludeColumns' => [
				'id',
				'checked_out', 
				'checked_out_time', 
				'created', 
				'created_by', 
				'modified', 
				'modified_by'
			],
			'includeCustomFields' => true,
			'customFieldPrefix' => 'field_'
		];
		
		// Merge with passed options
		$exportOptions = array_merge($exportOptions, $options);
		
		// Get main location data
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__store_locator_locations'))
			->where($db->quoteName('state') . ' = ' . 1);
		
		$db->setQuery($query);
		$locations = $db->loadObjectList();
		
		// Process locations
		$results = [];
		foreach ($locations as $location) {
			// Convert to array
			$locationData = (array)$location;
			
			// Remove excluded columns
			$locationData = array_diff_key(
				$locationData, 
				array_flip($exportOptions['excludeColumns'])
			);
			
			// Optionally add custom fields
			if ($exportOptions['includeCustomFields']) {
				// Get custom fields for this location
				$customQuery = $db->getQuery(true)
					->select(['f.id', 'f.params', 'cd.field_value'])
					->from($db->quoteName('#__store_locator_custom_data', 'cd'))
					->join('LEFT', $db->quoteName('#__store_locator_fields', 'f') . ' ON f.id = cd.field_id')
					->where($db->quoteName('cd.location_id') . ' = ' . $location->id);
				
				$db->setQuery($customQuery);
				$customFields = $db->loadObjectList();
				
				// Add custom fields to location data
				foreach ($customFields as $customField) {
					$prms = $customField->params;
					$prk = json_decode($prms,true);
					$fid = $exportOptions['customFieldPrefix'] . $customField->id;
					if(!empty($prk['name']))
					{
						$fid = $prk['name'];
					}
					$locationData[$fid] = $customField->field_value;
				}
			}
			
			// Reorder the array to match the database columns
			$locationData = array_merge(
				array_intersect_key($locationData, array_flip(array_keys((array)$location))),
				array_diff_key($locationData, array_flip(array_keys((array)$location)))
			);
			
			$results[] = $locationData;
		}
		
		return $results;
	}
    
    /**
     * Add metadata to the export data
     *
     * @param   array  $data  Main export data
     * @return  array  Data with metadata
     */
    protected function addMetadata($data) {
        $metadata = array(
            'version' => JVERSION,
            'timestamp' => (new Date())->toSql(),
            'component' => 'com_store_locator',
            'component_version' => $this->getComponentVersion(),
            'export_date' => date('Y-m-d H:i:s'),
            'total_records' => count($data)
        );
        
        return array(
            'metadata' => $metadata,
            'data' => $data
        );
    }
    
    /**
     * Add related data to the export
     *
     * @param   array  $data  Main export data
     * @return  array  Data with relations
     */
    protected function addRelatedData($data) {
        $db = $this->getDbo();
        
        // If data is already wrapped with metadata
        $items = isset($data['data']) ? $data['data'] : $data;
        
        // Get all relevant IDs
        $itemIds = array_column($items, 'catid');
        
        if (!empty($itemIds)) {
            // Example: Get related categories
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__categories'))
                ->where($db->quoteName('id') . ' IN (' . implode(',', $itemIds) . ')');
            
            $db->setQuery($query);
            $categories = $db->loadObjectList();
            
      
            
            $relations = array(
                'categories' => $categories,
            );
            
            if (isset($data['metadata'])) {
                $data['relations'] = $relations;
            } else {
                $data = array(
                    'data' => $items,
                    'relations' => $relations
                );
            }
        }
        
        return $data;
    }
    
    /**
     * Format data according to specified format
     *
     * @param   array   $data    Data to format
     * @param   string  $format  Output format
     * @return  mixed   Formatted data
     */
    protected function formatData($data, $format) {
        switch ($format) {
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT);
                
            case 'xml':
                return $this->arrayToXml($data);
                
            case 'csv':
                return $this->arrayToCsv($data);
                
            default:
                throw new Exception(JText::_('COM_YOURCOMPONENT_EXPORT_INVALID_FORMAT'));
        }
    }
    
    /**
     * Convert array to XML
     *
     * @param   array   $data  Data to convert
     * @return  string  XML string
     */
    protected function arrayToXml($data) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><export></export>');
        
        $this->arrayToXmlRecursive($data, $xml);
        
        return $xml->asXML();
    }
    
    /**
     * Recursive helper for XML conversion
     *
     * @param array            $data  Data to convert
     * @param SimpleXMLElement $xml   XML object
     */
    protected function arrayToXmlRecursive($data, &$xml) {
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xml->addChild($key);
                $this->arrayToXmlRecursive((array)$value, $subnode);
            } else {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
    }
    
    /**
     * Convert array to CSV
     *
     * @param   array   $data  Data to convert
     * @return  string  CSV string
     */
    protected function arrayToCsv($data) {
        $output = fopen('php://temp', 'r+');
        
        // If data has metadata, we'll create separate sections
        if (isset($data['metadata'])) {
            // Write metadata
            fputcsv($output, array('Metadata'));
            foreach ($data['metadata'] as $key => $value) {
                fputcsv($output, array($key, $value));
            }
            fputcsv($output, array()); // Empty line
            
            // Write main data
            $items = $data['data'];
        } else {
            $items = $data;
        }
        
        // Write headers
        if (!empty($items)) {
            // $headers = array_keys((array)$items[0]);
			$maxKeys = max(array_map('count', $items));
			$ky = 0;
			foreach($items as $s=>$i)
			{
				$ci = count($i);
				if($ci==$maxKeys)
				{
					$ky = $s;
				}
			}
			// // Filter the arrays with the maximum keys
			// $maxItems = array_filter($items, function($item) use ($maxKeys) {
			// 	return count($item) === $maxKeys;
			// });

			// // Get the headers from the first array with the maximum keys
			// $headers = array_keys((array)$maxItems[0]);
			$headers = array_keys($items[$ky]);
            fputcsv($output, $headers);
            
            // Write data rows
            foreach ($items as $item) {
                fputcsv($output, (array)$item);
            }
        }
        
        // Get the contents
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    /**
     * Get component version
     *
     * @return string Component version
     */
    protected function getComponentVersion() {
        $xml = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_store_locator/store_locator.xml');
        return (string)$xml->version;
    }

	public function getstorecustom($cid)
	{
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

		
	public static function getfieldgroups()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_field_groups'))
			->where($db->quoteName('state') . ' = ' . 1);
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return $result;
	}

	public static function getfields()
	{
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

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		
			if ($item = parent::getItem($pk))
			{
				if (isset($item->params))
				{
					$item->params = json_encode($item->params);
				}
				
				// Do any procesing on fields here if needed
			}

			return $item;
		
	}

	/**
	 * Method to duplicate an Locatorlocation
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$app = Factory::getApplication();
		$user = $app->getIdentity();
        $dispatcher = $this->getDispatcher();

		// Access checks.
		if (!$user->authorise('core.create', 'com_store_locator'))
		{
			throw new \Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		PluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			
				if ($table->load($pk, true))
				{
					// Reset the id to create a new record.
					$table->id = 0;

					if (!$table->check())
					{
						throw new \Exception($table->getError());
					}
					

					// Create the before save event.
					$beforeSaveEvent = AbstractEvent::create(
						$this->event_before_save,
						[
							'context' => $context,
							'subject' => $table,
							'isNew'   => true,
							'data'    => $table,
						]
					);

					// Trigger the before save event.
					$dispatchResult = Factory::getApplication()->getDispatcher()->dispatch($this->event_before_save, $beforeSaveEvent);

					// Check if dispatch result is an array and handle accordingly
					$result = isset($dispatchResult['result']) ? $dispatchResult['result'] : [];

					// Proceed with your logic
					if (in_array(false, $result, true) || !$table->store()) {
						throw new \Exception($table->getError());
					}

					// Trigger the after save event.
					Factory::getApplication()->getDispatcher()->dispatch(
						$this->event_after_save,
						AbstractEvent::create(
							$this->event_after_save,
							[
								'context'    => $context,
								'subject'    => $table,
								'isNew'      => true,
								'data'       => $table,
							]
						)
					);			
				}
				else
				{
					throw new \Exception($table->getError());
				}
			
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	private function update_field_dt($location_id,$field_id,$value) {
		$db = Factory::getDbo();
		$customData = new \stdClass();
		$customData->location_id = $location_id;
		$customData->field_id = $field_id;
		$customData->field_value = $value;
		$conditions = array(
			$db->quoteName('field_id') . ' = ' . $field_id,
			$db->quoteName('location_id') . ' = ' . $location_id
		);
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName('#__store_locator_custom_data'))
			->where($conditions);
		
		$db->setQuery($query);
		$exists = $db->loadResult();

		if ($exists) {
			// $whereClause = implode(' AND ', $conditions);
			$conditions2 = array('field_id', 'location_id');
				$result = $db->updateObject('#__store_locator_custom_data', $customData, $conditions2);
		} else {
			// Insert new record
			$result = $db->insertObject('#__store_locator_custom_data', $customData);
		}

	}

	private function save_custom_data($location_id,$input)
	{
		$custom_data = $input->get('custom_data', '', 'array');
		// echo "<pre>".print_r($custom_data, true);
		// echo "<pre>".print_r($input, true);
		// die;
		$custom_fds = $this->getfields();
		foreach($custom_fds as $cs) {
			$pr = $cs->params;
			$pd = json_decode($pr,true);
			if(!empty($pd['name']))
			{
				if($input->exists($pd['name']))
				{
					$vl = $input->get($pd['name'],'', 'string');
					// echo $vl;
					
					$this->update_field_dt($location_id,$cs->id,$vl);
				}
				else
				{
					$vl = $custom_data[$cs->id];
					$this->update_field_dt($location_id,$cs->id,$vl);
				}
			}
		}
		// die;
		/*
		$db = Factory::getDbo();
		foreach($custom_data as $field_id=>$value)
		{
			$customData = new \stdClass();
			$customData->location_id = $location_id;
			$customData->field_id = $field_id;
			$customData->field_value = $value;
			$conditions = array(
				$db->quoteName('field_id') . ' = ' . $field_id,
				$db->quoteName('location_id') . ' = ' . $location_id
			);
			$query = $db->getQuery(true)
				->select('id')
				->from($db->quoteName('#__store_locator_custom_data'))
				->where($conditions);
			
			$db->setQuery($query);
			$exists = $db->loadResult();
	
			if ($exists) {
				// $whereClause = implode(' AND ', $conditions);
				$conditions2 = array('field_id', 'location_id');
				 $result = $db->updateObject('#__store_locator_custom_data', $customData, $conditions2);
			} else {
				// Insert new record
				$result = $db->insertObject('#__store_locator_custom_data', $customData);
			}
		}
		*/

	}

	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$schedule = $input->get('schedule', '', 'array');
		// echo "<pre>". print_r($data, true);
		// die;
		$catids = $data['catid'];
		if(!empty($catids))
		{
			$data['catid'] = implode(',', $catids);
		}
		

		$maps = $data['map'];
		if(!empty($maps))
		{
			$data['map'] = implode(',', $maps);
		}
		
		// $data['catid'] = implode(',', $catids);
		// echo "<pre>".print_r($schedule,true);
		// die;
		// $jfm = $input->get('jform', '', 'array');
		// $catids = implode(",",$jfm['catid']);
		// $data['catid'] = $catids;
        // Initialize variables
        $table = $this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Load the row if saving an existing record
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }
		

        // Bind the data
        if (!$table->bind($data))
        {
            $app->enqueueMessage($table->getError(), 'error');
            return false;
        }
		
        // Prepare the row for saving
        $this->prepareTable($table);

        // Store the data
        if (!$table->store())
        {
            $app->enqueueMessage($table->getError(), 'error');
            return false;
        }

        // Clean the cache
        $this->cleanCache();
		
		
		// $custom_data = $input->get('custom_data', '', 'array');
		
		// echo "<pre>".print_r($schedule, true);
		// die;
		$this->save_custom_data($table->id, $input);
		// echo "<pre>".print_r($custom_data, true);
		// die;
		// return parent::save($data);
		$tsk = $input->get('task', '', 'string');
		if($tsk=='apply')
		{
			$app->redirect(Route::_('index.php?option=com_store_locator&view=locatorlocation&layout=edit&id='.$pk, false));
            return;
		}

        return true;
	}

	public function getadminentry($id) {

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_admin_location_entries'))
			->where($db->quoteName('id') . ' = ' . $db->quote($id));
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return $result;
		// return $id;

	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  Table Object
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = $this->getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__store_locator_locations');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
