<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Site\View\Locatormap;
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

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */

	public function mapskindata($pk = null)
	{
		 $myItemObj = new \stdClass();
	 
		 $db    = Factory::getDbo();
		 $query = $db->getQuery(true);
		 $query->select('*')
		 ->from($db->quoteName('#__store_locator_map_skins'))
		 ->where($db->quoteName('id') . ' = ' . (int) $pk); // Use pk as the ID in the query
		 $db->setQuery($query);
		 $myItemObj = $db->loadObject();
	 
		 return $myItemObj;
	}

	public function mapthemedata($pk = null)
	{
		$myItemObj = new \stdClass();
	
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__store_locator_map_themes'))
		->where($db->quoteName('id') . ' = ' . (int) $pk); // Use pk as the ID in the query
		$db->setQuery($query);
		$myItemObj = $db->loadObject();
	
		return $myItemObj;
	}

	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->params = $app->getParams('com_store_locator');
		if(!empty($this->item->map_skin))
		{
			$this->mapskin  =  $this->mapskindata($this->item->map_skin);
		}
		if(!empty($this->item->map_theme))
		{
			$this->maptheme  =  $this->mapthemedata($this->item->map_theme);
		}
		// $this->locations = $this->locatorlocations();

		// echo "<pre>Locations ".print_r($this->locations, true);
		// echo "<pre>Map Skin ".print_r($this->mapskin, true);
		// echo "<pre>Location Result ".print_r($this->locationresult, true);
		// echo "<pre>Location Card ".print_r($this->locationcard, true);
		// echo "<pre>Location Details ".print_r($this->locationdetails, true);
		


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

		$this->_prepareDocument();

		parent::display($tpl);
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
