<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use \Storelocator\Component\Store_locator\Administrator\Model\LocatorlocationsModel;
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);

$all_lists = $this->locationlists;
// echo "<pre>".print_r($all_lists, true);
// die;
$cols = $this->cols;
// echo "<pre>".print_r($cols, true);
$lc = $all_lists['locations'];
$tem = $all_lists['template'];
// echo "<pre>".print_r($tem, true);
$srpos = $all_lists['search_pos'];

$criteria = $all_lists['criteria'];
$arrangeby = $all_lists['arrangeby'];
$search_from = $all_lists['search_from'];
$search_data = $all_lists['search_data'];
$search_expanded = $all_lists['search_expanded'];
$dtype = $all_lists['display'];
// echo $dtype;
// die;
$fldtp = $all_lists['filter_dt'];
$pagination = $all_lists['pagination'];
$id = $this->id;
// echo "<pre>".print_r($fldtp, true);
if(isset($srpos))
{
    if($srpos==1)
    {
        ?>
        <div class="search-box">
            <div class="search-inner">
                <form method="POST">
                    <h3>Search & Filtering</h3>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="icon-search"></i></span>
                        </div>
                        <input value="<?php echo $all_lists['term']; ?>" name="search_ky" type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
                        <button class="btn btn-primary" type="submit" name="submit_data">Search</button>
                    </div>
                    <div class="adv_filter">
                        <a href="#" class="adv_filters">Advanced Filters</a>
                    </div>
                    <div class="<?php if($search_expanded==1) { ?>expanded <?php } ?>search_filters">
                        <form method="POST">
                        <div class="row">
                        <?php 
                        foreach($search_data as $kd=>$s) { 
                            $head = $s['heading'];
                            $wd = $s['width'];
                            $cls = $s['class'];
                            $typ = $s['type'];
                            $fld = $s['field'];
                            $fopts = $s['options'];
                            $selct = array();
                            if(isset($fldtp[$fld]))
                            {
                                $selct = $fldtp[$fld];
                            }
                            ?>
                            <div class="col-md-<?php echo $wd; ?>">
                                <h4><?php echo $head; ?></h4>
                                <div class="foptions">
                                <?php
                                foreach($fopts as $s=>$fo) { 
                                    $mkdd = $fo;
                                    if($fld=='catid')
                                    {
                                        $mkdd = LocatorlocationsModel::getCategoryById($fo);
                                    }
                                    ?>
                                    <label for="ip_<?php echo $kd; ?>_<?php echo $s; ?>"><input <?php if(in_array($fo,$selct)) { ?>checked="checked" <?php } ?> name="<?php echo $fld; ?>[]" type="checkbox" id="ip_<?php echo $kd; ?>_<?php echo $s; ?>" value="<?php echo $s; ?>"/><?php echo $mkdd; ?></label>
                                <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                            <div class="col-md-12 text-start mt-3">
                                <button type="submit" name="filter_data" class="btn btn-secondary">Filter Results</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}
?>

<div class="location-list-area">
    <?php 
        if($srpos==2)
        {
            ?>
            <div class="search-box">
                <div class="search-inner">
                    <form method="POST">
                        <h3>Search & Filtering</h3>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-search"></i></span>
                            </div>
                            <input value="<?php echo $all_lists['term']; ?>" name="search_ky" type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
                            <button class="btn btn-primary" type="submit" name="submit_data">Search</button>
                        </div>
                        <div class="adv_filter">
                            <a href="#" class="adv_filters">Advanced Filters</a>
                        </div>
                        <div class="<?php if($search_expanded==1) { ?>expanded <?php } ?>search_filters">
                            <form method="POST">
                            <div class="row">
                            <?php foreach($search_data as $kd=>$s) { 
                                $head = $s['heading'];
                                $wd = $s['width'];
                                $cls = $s['class'];
                                $typ = $s['type'];
                                $fld = $s['field'];
                                $fopts = $s['options'];
                                $selct = array();
                                if(isset($fldtp[$fld]))
                                {
                                    $selct = $fldtp[$fld];
                                }
                                ?>
                                <div class="snglfl col-md-<?php echo $wd; ?>">
                                    <h4><?php echo $head; ?></h4>
                                    <div class="foptions">
                                    <?php foreach($fopts as $s=>$fo) { ?>
                                        <label for="ip_<?php echo $kd; ?>_<?php echo $s; ?>"><input <?php if(in_array($fo,$selct)) { ?>checked="checked" <?php } ?> name="<?php echo $fld; ?>[]" type="checkbox" id="ip_<?php echo $kd; ?>_<?php echo $s; ?>" value="<?php echo $s; ?>"/><?php echo $fo; ?></label>
                                    <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                                <div class="col-md-12 text-start mt-3">
                                    <button type="submit" name="filter_data" class="btn btn-secondary">Filter Results</button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </form>
                </div>
            </div>
            <style>
            .location-list-area {
                display: flex;
            }
            .all-lists.row
            {
                gap:20px !important;
            }
            .snglfl
            {
                min-width:100%;
            }
            .location-list-area .search-box {
                min-width: 20%;
                margin-right: 20px;
				border: 1px solid #cecece;
            }
            .all-lists.row {
                width: 100%;
                display: flex;
                flex-direction: row;
                align-content: flex-start;
                justify-content: flex-start;
                align-items: flex-start;
            }
            </style>
            <?php
        }
        if($dtype==1)
        {
            // echo "<pre>".print_r($lc, true);
            ?>
            <div class="list-table">
                <table class="lstable">
                    <thead>
                        <tr>
                            <?php foreach($tem as $t) { ?>
                                <th>
                                    <?php echo $t['heading']; ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($lc as $l)
                            {
                            ?>
                            <tr data-target="/administrator/index.php?option=com_store_locator&view=locatorlocation&layout=edit&id=<?php echo $l['location_data']->id; ?>">
                                <?php foreach($tem as $r) { 
                                     $lcdata = $l['location_data'];
                                     $csd = $l['carr'];
                                     $fdss = $r['fields'];
                                    ?>
                                    <td>
                                    <?php
                                    foreach($fdss as $fi)
                                    {
                                        $ft = $fi['field'];
                                        $typ = $fi['type'];
                                        if($typ==4) { ?>
                                            <h3><?php echo $hd; ?></h3>
                                        <?php } ?>
                                        <p>
                                        <?php if($typ==1) { ?>
                                            <strong><?php echo $hd; ?></strong>
                                        <?php } 
                                        if(isset($cols[$ft]))
                                        {
                                            // echo "<pre>".print_r($lcdata,true);
                                            // echo $ft;
                                            if($ft=='catid')
                                            {
                                                $catid = $lcdata->{$ft};
                                                $mdt = LocatorlocationsModel::getCategoryById($catid);
                                                echo $mdt;
                                                // $mdt = $model->getCategoryById($catid);
                                            }
                                            else
                                            {
                                                echo $lcdata->{$ft};
                                            }
                                            
                                        }
                                        else
                                        {
                                            $int = (int) filter_var($ft, FILTER_SANITIZE_NUMBER_INT);
                                            if(isset($csd[$ft]))
                                            {
                                                // echo "<pre>".print_r($csd['field_'.$ft], true);
                                                // echo $csd[$ft]['value'];
                                                $ty = $csd[$ft]['type'];
                                                $dl = $csd[$ft]['detail_link'];
                                                if($ty=='image')
                                                {
                                                    if($dl)
                                                    {
                                                        echo '<a href="#"><img src="'.Uri::root(). $csd[$ft]['value'].'"/></a>';
                                                    }
                                                    else
                                                    {
                                                        echo '<img style="max-width:100px;" alt="'.$r['heading'].'" src="'.Uri::root(). $csd[$ft]['value'].'"/>';
                                                    }
                                                    
                                                }
                                                else
                                                {
                                                    if($dl)
                                                    {
                                                        echo '<a href="#">'.$csd[$ft]['value'].'</a>';
                                                    }
                                                    else
                                                    {
                                                        echo $csd[$ft]['value'];
                                                    }
                                                    
                                                }
                                            }
                                            // foreach($csd as $cs)
                                            // {
                                            //     if($cs->field_id==$int)
                                            //     {
                                            //         echo $cs->field_value;
                                            //     }
                                            // }
                                        }
                                    }
                                    ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php 
                        } ?>
                    </tbody>
                </table>
            </div>
            <style>
            .list-table, .list-table table {
                width: 100%;
				margin: 1px 8px;
            }

            .list-table th, .list-table td {
                padding: 10px 0;
                border-bottom: 1px solid #cecece;
            }

            .list-table p {
                margin-bottom: 0;
            }
            </style>
            <?php
        }
        else
        {
    ?>
    <div class="all-lists row">
        <?php
        if(!empty($lc))
        {
            foreach($lc as $l)
            {
                // echo "<pre>".print_r($l, true);
                ?>
                <div data-target="/administrator/index.php?option=com_store_locator&view=locatorlocation&layout=edit&id=<?php echo $l['location_data']->id; ?>" data-id="<?php echo $l['location_data']->id; ?>" class="single-list col-md-4">
     
                        <div class="row">
                        <?php
                        foreach($tem as $oi)
                        {
                            $hd = $oi['heading'];
                            $wd = $oi['width'];
                            // $ft = $oi['field'];
                            // $typ = $oi['type'];
                            $lcdata = $l['location_data'];
                            $csd = $l['carr'];
                            $fdss = $oi['fields'];
                            // echo $ft;
                            // echo "<pre>".print_r($csd, true);
                            ?>
                               
                                    <div class="col-md-<?php echo $wd; ?>">
                                        <?php 
                                        foreach($fdss as $fi)
                                        {
                                            $ft = $fi['field'];
                                            $typ = $fi['type'];
                                            if($typ==4) { ?>
                                                <h3><?php echo $hd; ?></h3>
                                            <?php } ?>
                                            <p>
                                            <?php if($typ==1) { ?>
                                                <strong><?php echo $hd; ?></strong>
                                            <?php } 
                                            if(isset($cols[$ft]))
                                            {
                                                // echo "<pre>".print_r($lcdata,true);
                                                // echo $ft;
                                                echo $lcdata->{$ft};
                                            }
                                            else
                                            {
                                                $int = (int) filter_var($ft, FILTER_SANITIZE_NUMBER_INT);
                                                if(isset($csd[$ft]))
                                                {
                                                    // echo "<pre>".print_r($csd['field_'.$ft], true);
                                                    // echo $csd[$ft]['value'];
                                                    $ty = $csd[$ft]['type'];
                                                    $dl = $csd[$ft]['detail_link'];
                                                    if($ty=='image')
                                                    {
                                                        if($dl)
                                                        {
                                                            echo '<a href="#"><img src="'.Uri::root(). $csd[$ft]['value'].'"/></a>';
                                                        }
                                                        else
                                                        {
                                                            echo '<img style="max-width:100px;" src="'.Uri::root(). $csd[$ft]['value'].'"/>';
                                                        }
                                                        
                                                    }
                                                    else
                                                    {
                                                        if($dl)
                                                        {
                                                            echo '<a href="#">'.$csd[$ft]['value'].'</a>';
                                                        }
                                                        else
                                                        {
                                                            echo $csd[$ft]['value'];
                                                        }
                                                        
                                                    }
                                                }
                                                // foreach($csd as $cs)
                                                // {
                                                //     if($cs->field_id==$int)
                                                //     {
                                                //         echo $cs->field_value;
                                                //     }
                                                // }
                                            }
                                        }
                                       
                                        ?></p>
                                    </div>
                               
                            <?php
                        }
                    ?>
          
                     </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php 
        }
        if($srpos==3)
        {
            ?>
            <div class="search-box">
                <div class="search-inner">
                    <form method="POST">
                        <h3>Search & Filtering</h3>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="icon-search"></i></span>
                            </div>
                            <input value="<?php echo $all_lists['term']; ?>" name="search_ky" type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1">
                            <button class="btn btn-primary" type="submit" name="submit_data">Search</button>
                        </div>
                        <div class="adv_filter">
                            <a href="#" class="adv_filters">Advanced Filters</a>
                        </div>
                        <div class="<?php if($search_expanded==1) { ?>expanded <?php } ?>search_filters">
                            <form method="POST">
                            <div class="row">
                            <?php foreach($search_data as $kd=>$s) { 
                                $head = $s['heading'];
                                $wd = $s['width'];
                                $cls = $s['class'];
                                $typ = $s['type'];
                                $fld = $s['field'];
                                $fopts = $s['options'];
                                $selct = array();
                                if(isset($fldtp[$fld]))
                                {
                                    $selct = $fldtp[$fld];
                                }
                                ?>
                                <div class="snglfl col-md-<?php echo $wd; ?>">
                                    <h4><?php echo $head; ?></h4>
                                    <div class="foptions">
                                    <?php foreach($fopts as $s=>$fo) { 
                                        // $smdt = LocatorlocationsModel::getCategoryById($catid);
                                        ?>
                                        <label for="ip_<?php echo $kd; ?>_<?php echo $s; ?>"><input <?php if(in_array($fo,$selct)) { ?>checked="checked" <?php } ?> name="<?php echo $fld; ?>[]" type="checkbox" id="ip_<?php echo $kd; ?>_<?php echo $s; ?>" value="<?php echo $s; ?>"/><?php echo $fo; ?></label>
                                    <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                                <div class="col-md-12 text-start mt-3">
                                    <button type="submit" name="filter_data" class="btn btn-secondary">Filter Results</button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </form>
                </div>
            </div>
            <style>
            .location-list-area {
                display: flex;
            }
            .all-lists.row
            {
                gap:20px !important;
            }
            .snglfl
            {
                min-width:100%;
            }
            .location-list-area .search-box {
                min-width: 20%;
                margin: 5px 5px 5px 25px;
				border: 1px solid #cecece;
				
            }
            .all-lists.row {
                width: 100%;
                display: flex;
                flex-direction: row;
                align-content: flex-start;
                justify-content: flex-start;
                align-items: flex-start;
            }
            </style>
            <?php
        }
    ?>
    </div>
    <div class="pagination">
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination-nav">
                <?php if ($pagination['page'] > 1): ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_store_locator&view=locationmenulistsingle&id=' . $id . '&page=' . ($pagination['page'] - 1)); ?>" class="prev">
                        &laquo; Previous
                    </a>
                <?php endif; ?>

                <?php 
                // Show page numbers
                $start = max(1, $pagination['page'] - 2);
                $end = min($pagination['total_pages'], $pagination['page'] + 2);
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $pagination['page']): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_store_locator&view=locationmenulistsingle&id=' . $id . '&page=' . $i); ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_store_locator&view=locationmenulistsingle&id=' . $id . '&page=' . ($pagination['page'] + 1)); ?>" class="next">
                        Next &raquo;
                    </a>
                <?php endif; ?>
            </div>

            <div class="pagination-info">
                Showing <?php 
                echo (($pagination['page'] - 1) * $pagination['limit'] + 1) . 
                    ' to ' . 
                    min($pagination['page'] * $pagination['limit'], $pagination['total']); 
                ?> of <?php echo $pagination['total']; ?> entries
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
 table.list_area {
    width: 100%;
    background: #fff;
    padding: 10px;
}
table.list_area th, table.list_area td {
    padding: 10px;
    border: 1px solid #ccc;
}

table.list_area th {
    color: #fff;
    background: var(--sidebar-toggle-bg);
}
.search-inner .input-group-text {
    padding: 12px 18px;
    background: #fff;
    border-right: none;
    border-color: #ccc;
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
}

.search-inner .icon-search {
    color: #222;
}
.foptions {
    max-height: 243px;
    overflow: auto;
}

.foptions label {
    width: 100%;
    display: block;
    margin-bottom: 8px;
}

.foptions input {
    margin-right: 10px;
}
.search-box {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.search-inner input {
    border-left: none !important;
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
}
:root[data-color-scheme="dark"] .search-box {
    background: #0d0d1b;
}
.location-list-area {
    background: #ffffff;
    border-radius: 7px;
    padding: 20px;
}

.single-list.col-md-4 {
    background: #fff;
    width: 32%;
    padding: 18px;
    border-radius: 10px;
    box-shadow: #00000045 0px 0px 8px;
}
.search_filters {
    height: 0;
    margin-top: 20px;
    overflow: hidden;
    transition: all 500ms linear;
}
.search_filters.expanded {
    height: 100%;
    overflow-y: auto;
}
.all-lists.row {
    gap: 30px;
    margin: 0 !important;
}
:root[data-color-scheme="dark"] .location-list-area {
    background: #040415;
}

:root[data-color-scheme="dark"] .single-list.col-md-4 {
    background: #242424;
}
:root[data-color-scheme="dark"] {
  --pagination-bg: #20262d;
  --pagination-color: #f1f1f1;
  --pagination-link-bg: #040415;
  --pagination-link-border: #040415;
  --pagination-link-hover-bg: #007db0;
  --pagination-link-hover-color: #fff;
  --pagination-link-hover-border: #007db0;
  --pagination-current-bg: #007db0;
  --pagination-current-color: #fff;
  --pagination-info-color: #ccc;
}

:root[data-color-scheme="light"] {
  --pagination-bg: #f9f9f9;
  --pagination-color: #333;
  --pagination-link-bg: #fff;
  --pagination-link-border: #ccc;
  --pagination-link-hover-bg: #007db0;
  --pagination-link-hover-color: #fff;
  --pagination-link-hover-border: #007db0;
  --pagination-current-bg: #007db0;
  --pagination-current-color: #fff;
  --pagination-info-color: #666;
}

.pagination {
  background-color: var(--pagination-bg);
  padding: 10px 0;
  border-radius: 5px;
  color: var(--pagination-color);
  align-items: center;
  margin: 1rem 1px;
}
.pagination {
    justify-content: center;
}
.pagination-nav a {
  color: var(--pagination-color);
  text-decoration: none;
  padding: 8px 12px;
  margin: 0 5px;
  border: 1px solid var(--pagination-link-border);
  border-radius: 4px;
  transition: background-color 0.3s, color 0.3s;
  display: inline-block;
  background: var(--pagination-link-bg);
}

.pagination-nav a:hover {
  border-color: var(--pagination-link-hover-border);
  background-color: var(--pagination-link-hover-bg);
  color: var(--pagination-link-hover-color);
}

.pagination-nav .prev {
  background-color: var(--pagination-link-bg);
}

.pagination-nav .current {
  padding: 8px 12px;
  margin: 0 5px;
  background-color: var(--pagination-current-bg);
  color: var(--pagination-current-color);
  border-radius: 4px;
  border: 1px solid var(--pagination-current-bg);
}

.pagination-info {
  margin-left: 16px;
  font-size: 14px;
  color: var(--pagination-info-color);
}

.pagination-nav {
  margin-left: 10px;
}
:root[data-color-scheme="dark"] .single-list.col-md-4 {
    background: #007db0 !important;
}
.single-list
{
    cursor: pointer;
}
.snglfl {
    margin-bottom: 20px;
}
table.lstable tbody tr {
    cursor: pointer;
}
table.lstable tbody tr:hover td {
    background-color: #f2f2f2 !important;
}

:root[data-color-scheme="dark"] table.lstable tbody tr:hover td {
    background-color: #000 !important;
}
</style>
<script>
    jQuery(document).on('click', 'a.adv_filters', function(){
        jQuery('.search_filters').toggleClass('expanded');
        return false;
    });
    jQuery('.single-list').click(function(){
        var trg = jQuery(this).data('target');
        window.open(trg, '_blank');
        return false;
    });
    jQuery(document).on('click', 'table.lstable tbody tr', function(){
        var trg = jQuery(this).data('target');
        window.open(trg, '_blank');
        return false;
    });
</script>