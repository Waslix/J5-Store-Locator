<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\View\Locationexport;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Storelocator\Component\Store_locator\Administrator\Helper\Store_locatorHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \Joomla\CMS\Form\Form;
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
        parent::display($tpl);
    }
    
    protected function addToolbar() {
        ToolbarHelper::title('Export Locations', 'download');
        
        // Add toolbar buttons
        // ToolbarHelper::custom('export.download', 'download', '', 'Export Locations', false);
        ToolbarHelper::back( 'JTOOLBAR_BACK',  'javascript:history.back()');
        ToolbarHelper::cancel('export.cancel', 'JTOOLBAR_CLOSE');
        
        // Add help button if you have documentation
        ToolbarHelper::help('JHELP_COMPONENTS_YOURCOMPONENT_EXPORT');
    }

}
