<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\View\Locationimport;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Storelocator\Component\Store_locator\Administrator\Helper\Store_locatorHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\Factory;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of locatormaps.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	public function display($tpl = null) {
        // Add title and toolbar
        $this->addToolbar();
        
        // Get export options (if any)
        // $this->exportOptions = $this->get('ExportOptions');
        
        // Display the template
        $this->availableFields = $this->getAvailableFields();
        parent::display($tpl);
    }

    public function getAvailableFields() {
        $db = Factory::getDbo();
		$query = "SHOW COLUMNS FROM " . $db->quoteName('#__store_locator_locations');
		$db->setQuery($query);
		$allColumns = $db->loadColumn();
		$asd = $this->get_custom_fields();
          $csf = array();
          $cm = array();
          foreach ($asd as $p) {
               $csf[] = 'field_'.$p->id; 
               $cm['field_'.$p->id] = $p->title;
          }
          $allColumns = array_merge($allColumns, $csf);
		$excludeColumns = ['id', 'checked_out', 'checked_out_time'];
		
		// Filter out excluded columns
		$selectedColumns = array_filter($allColumns, function($column) use ($excludeColumns) {
			return !in_array($column, $excludeColumns);
		});
	
        $sel = array();
        foreach($selectedColumns as $sl) {
            $lbl = $sl;
               if($sl=='state')
               {
                    $lbl = 'Status';
               }
               elseif($sl=='ordering')
               {
                    $lbl = 'Order';
               }
               elseif($sl=='created_by')
               {
                    $lbl = 'Created By';
               }
               elseif($sl=='modified_by')
               {
                    $lbl = 'Modified By';
               }
               elseif($sl=='locationlistingtitle')
               {
                    $lbl = 'Listing Title';
               }
               elseif($sl=='user')
               {
                    $lbl = 'User Id';
               }
               elseif($sl=='catid')
               {
                    $lbl = 'Category ID';
               }
               elseif($sl=='email')
               {
                    $lbl = 'Email';
               }
               elseif($sl=='website')
               {
                    $lbl = 'Website';
               }
               elseif($sl=='phone')
               {
                    $lbl = 'Phone';
               }
               elseif($sl=='image')
               {
                    $lbl = 'Image';
               }
               elseif($sl=='street')
               {
                    $lbl = 'Street';
               }
               elseif($sl=='city')
               {
                    $lbl = 'City';
               }
               elseif($sl=='user_state')
               {
                    $lbl = 'State';
               }
               elseif($sl=='zip_code')
               {
                    $lbl = 'Zip Code';
               }
               elseif($sl=='country')
               {
                    $lbl = 'Country';
               }
               elseif($sl=='latitude')
               {
                    $lbl = 'Latitude';
               }
               elseif($sl=='longitude')
               {
                    $lbl = 'Longitude';
               }
               elseif($sl=='admin_entry')
               {
                    $lbl = 'Admin Entry Template';
               }
               if(isset($cm[$sl]))
               {
                    $lbl = $cm[$sl];
               }
               $sel[$sl] = $lbl;
        }
        return $sel;
        // return array(
        //     'title' => 'Title',
        //     'description' => 'Description',
        //     'category' => 'Category',
        //     'published' => 'Published Status',
        //     'created_date' => 'Created Date'
        // );
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
    
    protected function addToolbar() {
        ToolbarHelper::title('Import Locations', 'download');
        
        // Add toolbar buttons
     //    ToolbarHelper::custom('export.download', 'download', '', 'Download Sample', false);
        ToolbarHelper::link( 'https://goggle.com',  'Download Sample', 'download', false);
        ToolbarHelper::cancel('export.cancel', 'JTOOLBAR_CLOSE');
        
        // Add help button if you have documentation
        ToolbarHelper::help('JHELP_COMPONENTS_YOURCOMPONENT_EXPORT');
    }

}
