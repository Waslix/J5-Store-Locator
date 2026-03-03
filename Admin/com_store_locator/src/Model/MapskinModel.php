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
use \Joomla\CMS\Event\AbstractEvent;
use \Joomla\CMS\Filesystem\File;
use \Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;


/**
 * Locatorlocation model.
 *
 * @since  1.0.0
 */
class MapskinModel extends AdminModel
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
	public $typeAlias = 'com_store_locator.mapskin';

	/**
	 * @var    null  Item data
	 *
	 * @since  1.0.0
	 */
	protected $item = null;

	
	

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
	public function getTable($type = 'Mapskin', $prefix = 'Administrator', $config = array())
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
								'com_store_locator.mapskin', 
								'mapskin',
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
		$data = Factory::getApplication()->getUserState('com_store_locator.edit.mapskin.data', array());

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

	public function save($data)
	{
		
		$app = Factory::getApplication();
        $files = $app->input->files->get('jform', array(), 'array');
        $file = $files['skin_data'];

        // Check if a new file was uploaded
        if (!empty($file['name'])) {
            // Set up upload directory
            $uploadPath = JPATH_ROOT . '/images/com_store_locator/uploads';

            // Create upload directory if it doesn't exist
            if (!Folder::exists($uploadPath)) {
                Folder::create($uploadPath);
            }

            // Clean the filename and make it safe
            $filename = File::makeSafe($file['name']);
            
            // Add timestamp to filename to make it unique
            $filename = time() . '_' . $filename;
            
            // Full path for the file
            $dest = Path::clean($uploadPath . '/' . $filename);

            // Try to upload the file
            if (!File::upload($file['tmp_name'], $dest)) {
                $app->enqueueMessage('Error uploading file', 'error');
                return false;
            }

            // Store the filename in the data array
            $data['skin_data'] = $filename;

            // If updating, remove old file
            if (!empty($data['id'])) {
                $oldItem = $this->getItem($data['id']);
                if (!empty($oldItem->stored_filename)) {
                    $oldFile = $uploadPath . '/' . $oldItem->stored_filename;
                    if (File::exists($oldFile)) {
                        File::delete($oldFile);
                    }
                }
            }
        }
		// echo "<pre>".print_r($data, true);
		// die;
		return parent::save($data);
	}

	public function delete(&$pks)
    {
        $result = parent::delete($pks);

        if ($result) {
            foreach ($pks as $pk) {
                $item = $this->getItem($pk);
                if (!empty($item->skin_data)) {
                    $file = JPATH_ROOT . '/images/com_store_locator/uploads/' . $item->stored_filename;
                    if (File::exists($file)) {
                        File::delete($file);
                    }
                }
            }
        }

        return $result;
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
