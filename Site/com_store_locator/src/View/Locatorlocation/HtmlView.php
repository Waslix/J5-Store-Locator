<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Site\View\Locatorlocation;
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * View class for a list of Store_locator.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;


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
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->params = $app->getParams('com_store_locator');

		if (!empty($this->item))
		{
			$csfd = $this->get_custom_fields();
			$csdata = $this->get_custom_data($this->item->id);
			$carr = array();
			foreach($csdata as $csd)
			{
				$this_fd = array();
				foreach($csfd as $cf) {
					if($cf->id == $csd->field_id) {
						$this_fd = $cf;
					}
				}
				$carr['field_'.$csd->field_id]['value'] = $csd->field_value;
				$carr['field_'.$csd->field_id]['type'] = $this_fd->type;
				$carr['field_'.$csd->field_id]['detail_link'] = $this_fd->detail_link;
			}
			$this->carr = $carr;
		}


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		

		if ($this->_layout == 'edit')
		{
			$authorised = $user->authorise('core.create', 'com_store_locator');

			if ($authorised !== true)
			{
				throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		$this->details = $this->locationdetails($this->item->details_tempate);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	public function locationdetails($lb)
	{
		$myItemObj = new \stdClass();
	
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_location_details'))
		->where($db->quoteName('id') . ' = ' . $db->quote($lb));
		$db->setQuery($query);
		$myItemObj = $db->loadAssocList();
	
		return $myItemObj[0];
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_STORE_LOCATOR_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		
	}
}
