<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\View\Locatormap;
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Factory;
use \Storelocator\Component\Store_locator\Administrator\Helper\Store_locatorHelper;
use \Joomla\CMS\Language\Text;

/**
 * View class for a single Locatorlocation.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// $locations = $this->getlocations($this->item->id);
		// $this->locations = $locations;
		// $this->adminentries = $this->getadminentries();
		// echo "<pre>".print_r($locations, true);
		// die;
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}
				$this->addToolbar();
		
		parent::display($tpl);
	}

	public function getlocations($pk = null)
	{
		$myItemObj = new \stdClass();
		$stt = 1;
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_locations'))
		->where('FIND_IN_SET('.$pk.', ' . $db->quoteName('map') . ') > 0')
		->where($db->quoteName('state') . ' = ' . (int) $stt);
		$db->setQuery($query);
		$myItemObj = $db->loadObjectList();
	
		return $myItemObj;
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
	

	public function getadminentries()
	{
		$myItemObj = new \stdClass();
	
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_admin_location_entries'));
		$db->setQuery($query);
		$myItemObj = $db->loadObjectList();
	
		return $myItemObj;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getApplication()->getIdentity();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = Store_locatorHelper::getActions();

		ToolbarHelper::title(Text::_('COM_STORE_LOCATOR_TITLE_LOCATION_MAPS'), "generic");

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolbarHelper::apply('locatormap.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('locatormap.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			ToolbarHelper::custom('locatormap.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			ToolbarHelper::custom('locatormap.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('locatormap.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('locatormap.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
