<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Storelocator\Component\Store_locator\Administrator\View\Locationmenulistsingle;
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
use Joomla\CMS\Uri\Uri;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of locatormaps.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	public function display($tpl = null) {
        $db = Factory::getDbo();
        $columns = $db->getTableColumns('#__store_locator_locations');
        $this->addToolbar();
        $app = Factory::getApplication();
		$input = $app->input;
        $loc_id = $input->get('id', '', 'int');
        // Get export options (if any)
        // $this->exportOptions = $this->get('ExportOptions');
        
        // Display the template
        $this->id = $loc_id;
        $this->locationlists = $this->getlocationlists($loc_id);
        $this->cols = $columns;
        parent::display($tpl);
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

	private function getlocationlists($id) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
        $columns = $db->getTableColumns('#__store_locator_locations');
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_location_lists'))
            ->where($db->quoteName('id') . ' = ' . $id);
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadObjectList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
        $app = Factory::getApplication();
		$input = $app->input;
        $paging = $result[0]->paging;
        $page = $input->get('page', 1, 'int');
        $offset = ($page - 1) * $paging;    
        $dtyp = $result[0]->display;
        $template = json_decode($result[0]->template_data,true);
        $criteria = json_decode($result[0]->criteria,true);
        $arrangeby = json_decode($result[0]->arrange_by,true);
        $search_from = json_decode($result[0]->search_from,true);
        $search_data = json_decode($result[0]->search_data,true);
        $search_pos = $result[0]->search_pos;
        $srexp = $result[0]->search_expanded;
        $term = '';
        if ($input->exists('search_ky')) {
            $term = $input->get('search_ky', '', 'string');
        }
       
        $sdata = array();
        $search_filter_data = array();
        foreach($search_data as $s)
        {
            $po = $s['field'];
            if(isset($columns[$po]))
            {
                $query = $db->getQuery(true);
                $query->select('DISTINCT '.$po)
                    ->from($db->quoteName('#__store_locator_locations'))
                    ->where($db->quoteName('state') . ' = ' . $db->quote(1));
                $db->setQuery($query);
                $result = $db->loadAssocList();
                $ops = array();
                foreach($result as $rs)
                {
                    if(!empty($rs[$po]))
                    {
                        $ops[] = $rs[$po];
                    }
                }
                
            }
            else
            {
                $int = (int) filter_var($po, FILTER_SANITIZE_NUMBER_INT);
                $query = $db->getQuery(true);
                $query->select('DISTINCT field_value')
                    ->from($db->quoteName('#__store_locator_custom_data'))
                    ->where($db->quoteName('field_id') . ' = ' . $db->quote($int));
                $db->setQuery($query);
                $result = $db->loadAssocList();
                $ops = array();
                foreach($result as $lk)
                {   
                    if(!empty($lk['field_value']))
                    {
                        $ops[] = $lk['field_value'];
                    }
                    
                }
            }
            $dat = array(
                'field' => $po,
                'options' => $ops,
                'heading' => $s['heading'],
                'width' => $s['width'],
                'class' => $s['class'],
                'type' => $s['type'],
            );
            $sdata[] = $dat;
            if($input->exists($s['field']))
            {
                $dd = $s['field'];
                $rs = $input->get($s['field'], '', 'array');
                foreach($rs as $r)
                {
                    $search_filter_data[$dd][] = $ops[$r];
                }
            }
        }
        
        $ca_locations = $this->get_locator_locations($criteria,$arrangeby,$term,$search_from,$search_filter_data,$paging,$offset);
        $ca_locations_all = $this->get_locator_locations_all($criteria,$arrangeby,$term,$search_from,$search_filter_data);
        // echo "<pre>".print_r($ca_locations, true)."</pre>";
        // die;
        $csfd = $this->get_all_custom_fields();
        $locations = array();
        foreach($ca_locations as $location){
            $lcd = $location->id;
            $csdata = $this->get_custom_data($lcd);
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
            $data = array(
                'location_data' => $location,
                'carr' => $carr
            );
            $locations[] = $data;
        }

        $total_locations = count($ca_locations_all);
        $pagination = [
            'total' => $total_locations,
            'limit' => $paging,
            'page' => $page,
            'total_pages' => ceil($total_locations / $paging)
        ];
        // echo "<pre>".print_r($ca_locations, true);
        // die;
        // echo "<pre>".print_r($template, true)."</pre>";
        // echo "<pre>".print_r($criteria, true)."</pre>";
        // echo "<pre>".print_r($arrangeby, true)."</pre>";
        // die;
        $data = array(
            'display' => $dtyp,
            'template' => $template,
            'locations' => $locations,
            'criteria' => $criteria,
            'arrangeby' => $arrangeby,
            'search_from' => $search_from,
            'search_data' => $sdata,
            'search_pos' => $search_pos,
            'search_expanded' => $srexp,
            'term' => $term,
            'filter_dt' => $search_filter_data,
            'pagination' => $pagination
        );
		return $data;
	}

    private function get_all_custom_fields() {
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

    private function get_custom_fields($int) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		// Write your custom SQL query here
		$query->select('*')
			->from($db->quoteName('#__store_locator_fields'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($int));
		
		$db->setQuery($query);
		
		try {
			$result = $db->loadAssocList();
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return $result;
	}

     private function get_locator_locations_all($criteria,$arrangeby,$trm,$search_from,$filter_data) {
        $db = Factory::getDbo();
        $columns = $db->getTableColumns('#__store_locator_locations');
		$query = $db->getQuery(true);
        $query->select('DISTINCT l.*')
            ->from($db->quoteName('#__store_locator_locations', 'l'))
            ->join('INNER', $db->quoteName('#__store_locator_custom_data', 'c') . ' ON l.id = c.location_id')
            ->join('INNER', $db->quoteName('#__store_locator_custom_data', 'c2') . ' ON l.id = c2.location_id');

        foreach ($criteria as $cr) {
            $fdp = $cr['field'];
            $fdvl = $cr['value'];
            
            // Check if the field exists in the locations table
            if (isset($columns[$fdp])) {
                $query->where($db->quoteName('l.' . $fdp) . ' = ' . $db->quote($fdvl));
            } else {
                $int = (int) filter_var($fdp, FILTER_SANITIZE_NUMBER_INT);
                // Assuming you want to filter by field_id in the custom data table
                $query->where($db->quoteName('c.field_id') . ' = ' . $db->quote($int));
                $query->where($db->quoteName('c.field_value') . ' = ' . $db->quote($fdvl));
            }
        }
        
        $searchConditions = [];
        
        if(!empty($trm))
        {
            foreach ($search_from as $sf) {
                if (isset($columns[$sf])) {
                    $searchConditions[] = $db->quoteName('l.' . $sf) . ' LIKE ' . $db->quote('%' . $trm . '%');
                } else {
                    $int = (int) filter_var($sf, FILTER_SANITIZE_NUMBER_INT);
                    // Assuming you want to filter by field_id in the custom data table
                    $searchConditions[] = '(' . $db->quoteName('c.field_id') . ' = ' . $db->quote($int) . ' AND ' . $db->quoteName('c.field_value') . ' LIKE ' . $db->quote('%' . $trm . '%') . ')';
                }
            }
                
        }
        else
        {
            $searchConditions[] = $db->quoteName('l.state') . ' = ' . $db->quote(1);
        }
        if (!empty($searchConditions)) {
            $query->where('(' . implode(' OR ', $searchConditions) . ')');
        }
        
        
        foreach($filter_data as $fd=>$dsr)
        {
            if (isset($columns[$fd])) {
                $query->where($db->quoteName('l.' . $fd) . ' IN (' . implode(',', $db->quote($dsr)) . ')');
            }
            else
            {
                $int = (int) filter_var($fd, FILTER_SANITIZE_NUMBER_INT);
                // Assuming you want to filter by field_id in the custom data table
                $query->where($db->quoteName('c2.field_id') . ' = ' . $db->quote($int));
                $query->where($db->quoteName('c2.field_value') . ' IN (' . implode(',', $db->quote($dsr)) . ')');
            }
        }
        // echo $query->__toString(); die;

        // echo "<pre>".print_r($query, true);
        // die;
        // foreach($arrangeby as $arg)
        // {
        //     $arrang_field = $arg['field'];
        //     $argodr = $arg['order'];
        //     if($argodr==1)
        //     {
        //         $odr = 'ASC';
        //     }
        //     else
        //     {
        //         $odr = 'DESC';
        //     }
        //     $query->order($db->quoteName('l.' . $arrang_field) . ' ' .($odr));
        // }
    
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
            // ->where($db->quoteName('id') . ' = ' . $id);
		
		// $db->setQuery($query);
    }

    private function get_locator_locations($criteria, $arrangeby, $trm, $search_from, $filter_data, $limit = 10, $offset = 0) {
        $db = Factory::getDbo();
        $columns = $db->getTableColumns('#__store_locator_locations');
        $query = $db->getQuery(true);
    
        $query->select('DISTINCT l.*')
            ->from($db->quoteName('#__store_locator_locations', 'l'))
            ->join('INNER', $db->quoteName('#__store_locator_custom_data', 'c') . ' ON l.id = c.location_id')
            ->join('INNER', $db->quoteName('#__store_locator_custom_data', 'c2') . ' ON l.id = c2.location_id');
    
        
        foreach ($criteria as $cr) {
            $fdp = $cr['field'];
            $fdvl = $cr['value'];
            
            if (isset($columns[$fdp])) {
                // echo $fdp;
                $query->where($db->quoteName('l.' . $fdp) . ' = ' . $db->quote($fdvl));
            } else {
                $int = (int) filter_var($fdp, FILTER_SANITIZE_NUMBER_INT);
                $query->where($db->quoteName('c.field_id') . ' = ' . $db->quote($int));
                $query->where($db->quoteName('c.field_value') . ' = ' . $db->quote($fdvl));
            }
        }
        
        $searchConditions = [];
        
        if(!empty($trm)) {
            
            foreach ($search_from as $sf) {
                if (isset($columns[$sf])) {
                    $searchConditions[] = $db->quoteName('l.' . $sf) . ' LIKE ' . $db->quote('%' . $trm . '%');
                } else {
                    $int = (int) filter_var($sf, FILTER_SANITIZE_NUMBER_INT);
                    $searchConditions[] = '(' . $db->quoteName('c.field_id') . ' = ' . $db->quote($int) . ' AND ' . $db->quoteName('c.field_value') . ' LIKE ' . $db->quote('%' . $trm . '%') . ')';
                }
            }
    
           
        }
        else
        {
            $searchConditions[] = $db->quoteName('l.state') . ' = ' . $db->quote(1);
        }
        if (!empty($searchConditions)) {
            $query->where('(' . implode(' OR ', $searchConditions) . ')');
        }
     
        
        // Apply filter data
        foreach($filter_data as $fd => $dsr) {
            if (isset($columns[$fd])) {
                $query->where($db->quoteName('l.' . $fd) . ' IN (' . implode(',', $db->quote($dsr)) . ')');
            } else {
                $int = (int) filter_var($fd, FILTER_SANITIZE_NUMBER_INT);
                $query->where($db->quoteName('c2.field_id') . ' = ' . $db->quote($int));
                $query->where($db->quoteName('c2.field_value') . ' IN (' . implode(',', $db->quote($dsr)) . ')');
            }
        }
    
        // Apply ordering
        foreach($arrangeby as $arg) {
            $arrang_field = $arg['field'];
            $argodr = $arg['order'];
            $odr = $argodr == 1 ? 'ASC' : 'DESC';
            if (isset($columns[$arrang_field])) {
                $query->order($db->quoteName('l.' . $arrang_field) . ' ' . $odr);
            }
            else
            {
                $int = (int) filter_var($arrang_field, FILTER_SANITIZE_NUMBER_INT);
                $query->order($db->quoteName('c2.field_id') . ' = ' . $db->quote($int));
                $query->order($db->quoteName('c2.field_value') . ' ' . $odr);
            }
            
        }
    
        // Apply pagination
        $query->setLimit($limit, $offset);
    
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }
    
    protected function addToolbar() {
        $baseUrl = Uri::base();
        ToolbarHelper::title('Location Menu Lists');
        
        // Add toolbar buttons
     //    ToolbarHelper::custom('export.download', 'download', '', 'Download Sample', false);
        ToolbarHelper::link( $baseUrl.'index.php?option=com_store_locator&view=locationmenulist&layout=edit',  'Add New', 'add', false);
        // ToolbarHelper::cancel('export.cancel', 'JTOOLBAR_CLOSE');
        
        // Add help button if you have documentation
        // ToolbarHelper::help('JHELP_COMPONENTS_YOURCOMPONENT_EXPORT');
    }

}
