<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;

/** @var \Joomla\Component\Banners\Administrator\View\Client\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
    HTMLHelper::_('jquery.framework');
    HTMLHelper::_('script', 'https://code.jquery.com/ui/1.14.0/jquery-ui.js', array('version' => 'auto', 'relative' => true));

$customData = $this->get('mapthemes');
$mapskins = $this->get('mapskins');
$filterfields = $this->get('filterfields');
$document = Factory::getDocument();
$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
$document->addStyleSheet(Uri::root() . "media/com_store_locator/css/custom.css");
$wa->addInlineStyle('
.cstmmap {
    position: relative;
    overflow: hidden;
}

.map_con, .res_con {
    height: 100%;
    overflow: auto;
}

.resize-handle {
    position: absolute;
    right: -5px;
    top: 0;
    bottom: 0;
    width: 10px;
    cursor: col-resize;
    background: rgba(0, 0, 0, 0.1);
    transition: background 0.2s;
    z-index: 100;
}

.resize-handle:hover {
    background: rgba(0, 0, 0, 0.2);
}

.map_con.resizing {
    user-select: none;
    pointer-events: none;
}

.drag-handle {
    position: absolute;
    top: 5px;
    right: 20px;
    padding: 5px;
    cursor: move;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 101;
}

.drag-handle:hover {
    background: rgba(255, 255, 255, 1);
}

.ui-sortable-placeholder {
    visibility: visible !important;
    background: rgba(0,0,0,0.1);
    border: 2px dashed rgba(0,0,0,0.2);
}

/* Ensure minimum widths */
.map_con {
    min-width: 30%;
}

.res_con {
    min-width: 20%;
}

/* Transition for smooth reordering */
.row > div {
    transition: all 0.3s ease;
}
input#jform_marker_icon {
    margin: 0 !important;
}
#general input, #general .form-select{
    margin: 5px 0;
    width: 200px !important;
}
');

$wa->addInlineScript('
jQuery(document).ready(function($) {
    const container = $(".cstmmap");
    const mapContainer = $(".map_con");
    const resultsContainer = $(".res_con");
    
    // Add handles
    mapContainer.append("<div class=\"drag-handle\"><i class=\"icon-move\"></i></div>");
    resultsContainer.append("<div class=\"drag-handle\"><i class=\"icon-move\"></i></div>");
    
   
    // Reordering functionality
    $(".js-draggable").sortable({
        items: "> div",
        handle: ".drag-handle",
        placeholder: "ui-sortable-placeholder",
        tolerance: "pointer",
        start: function(e, ui) {
            ui.placeholder.height(ui.item.height());
        },
        stop: function(e, ui) {
            updateOrderFields();
        }
    });
    function updateOrderFields() {
        var order = [];
        $(".js-draggable > div").each(function() {
            order.push($(this).attr("id"));
        });
        
        // Update location_order field
        $("#jform_location_order").val(order.indexOf("results-container") + 1);
        
        // Update map_order field
        $("#jform_map_order").val(order.indexOf("map-container") + 1);
    }
    
    // Initial update of fields
    updateOrderFields();
    
});
');
// echo "<pre>".print_r($mapskins,true);
?>
<script>
 jQuery(document).ready(function($){
  $(".jform_ip_b_1").click(function(){
   $("#ip_address").addClass("show");
    $("#ip_address").removeClass("hide");
  });

   $(".jform_ip_a_1").click(function(){
   $("#ip_address").addClass("hide");
   $("#ip_address").removeClass("show");
  });
 })   

</script>

<form action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="client-form" aria-label="<?php echo Text::_('COM_BANNERS_CLIENT_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>



<div class="row main-card px-4 py-4 border rounded">
 <div class="col-2"><?php echo $this->form->renderField('maptitle'); ?> </div>
  <div class="col-4"><?php //echo $this->form->renderField('ip'); ?>


    <fieldset id="jform_ip">
            <div class="btn-group radio arrang">
                <input class="arrng_order btn-check" type="radio" id="jform_ip_a_1" name="jform[ip]" <?php if($this->item->ip ==1) echo "checked"; ?> value="1">
                <label for="jform_ip_a_1" class="btn btn-outline-secondary jform_ip_a_1">
                    <?php echo Text::_("COM_STORE_LOCATOR_BY_IP");  ?> </label>
                <input class="arrng_order btn-check" type="radio" id="jform_ip_b_1" name="jform[ip]" <?php if($this->item->ip ==2) echo "checked"; ?> value="2">
                <label for="jform_ip_b_1" class="btn btn-outline-secondary jform_ip_b_1"><?php echo Text::_("COM_STORE_LOCATOR_BY_ADDRESS");  ?></label>
            </div>
            <div class="address_box">
                 <?php //echo $this->form->renderField('ip_address'); ?>
                 <input type="text" name="jform[ip_address]" id="ip_address" value="<?php  echo $this->item->ip_address; ?>" class="<?php if($this->item->ip ==2) echo "show"; else{ echo "hide"; } ?>" >
            </div>
        </fieldset>
       
</div>


  <div class="col-2"><?php echo $this->form->renderField('radius_search'); ?></div>
  <div class="col-2"><?php echo $this->form->renderField('max_results'); ?></div>
  <div class="col-2"> <?php echo LayoutHelper::render('joomla.edit.global', $this); ?> </div>
 
 
 
 
 

 </div>
<br/>



 <div class="row">
 <div class="col-lg-9"> 
	<div class="main-card px-4 py-4 border rounded">
	   <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_STORE_LOCATOR_TAB_GENERAL', true)); ?>
 
<div class="row">

            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('defaultaddress'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('defaultaddress'); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('defaultzoom'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('defaultzoom'); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('filter_radius'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('filter_radius'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('defaultradius'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('defaultradius'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('distance_unit'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('distance_unit'); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $this->form->getLabel('marker_icon'); ?>
                 </div>
                 <div class="col-md-10">
                    <?php echo $this->form->getInput('marker_icon'); ?>
                </div>
            </div>
              
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'arrange_by', 'Arrange By Fields'); ?>
        <div class="row">
            <div class="cslistres">
                <?php echo $this->form->renderField('list_results'); ?>
            </div>
            <?php
            $fdtk = array();
            if(isset($this->item->list_results))
            {
                $fdkss = $this->item->list_results;
                $fdkss = json_decode($fdkss,true);
                // $fdtk = $fdkss[0];
            }
            ?>
            <h2 class="m-0">Arrange By</h2>
            <small><span style="color: #a8a8a8;">Listings will be arranged according to these fields</span></small><br/>
                    
                    
                        <div class="col-12 main-card px-4 py-4 mt-4 border rounded">
            
                
                        <div class="arrange-by-field">
                            <?php echo $this->form->renderField('arrange_by'); ?>
                        </div>
                        <div class="bg-body-secondary field-arrange p-3">
                            
                            <?php
                            if(!empty($fdkss))
                            {
                                ?>
                                <a data-index="<?php echo count($fdkss); ?>" href="#" class="btn btn-info add_criteria_arrb"><span class="icon-save-new" aria-hidden="true"></span>Add Field</a>
                                <?php
                                foreach($fdkss as $o=>$fo)
                                {
                                    $om = $o+1;
                                    $fdi = $fo['field'];
                                    $fod = $fo['order'];
                                    ?>
                                    <div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3  single_box_arr">
                                        <select name="arrange_field[]" class="form-select farr fsel">
                                            <option value="">Select Field</option>
                                            <?php foreach($filterfields as $f=>$field) {
                                                ?>
                                                <option <?php if($fdi==$f) { ?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
                                                <?php
                                            } ?>
                                        </select>
                                        <fieldset id="jform_display">
                                            <div class="btn-group radio arrang">
                                                <input <?php if($fod==1) { ?>checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_a_<?php echo $om; ?>" name="arrange_type[<?php echo $om; ?>]" value="1">
                                                <label for="jform_display_a_<?php echo $om; ?>" class="btn btn-outline-secondary">Ascending</label>
                                                <input <?php if($fod==2) { ?>checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_b_<?php echo $om; ?>" name="arrange_type[<?php echo $om; ?>]" value="2">
                                                <label for="jform_display_b_<?php echo $om; ?>" class="btn btn-outline-secondary">Descending</label>
                                            </div>
                                        </fieldset>
                                        <a data-index="1"  href="#" class="btn btn-info add_criteria_arrb"><span class="icon-save-new" aria-hidden="true"></span>Add</a>
                                        <a class="ck" href="#">-</a>
                                    </div>
                                    <?php
                                }
                            }
                            else
                            { 
                            ?>
                            <div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3  single_box_arr">
                                <select name="arrange_field[]" class="form-select farr fsel">
                                    <option value="">Select Field</option>
                                    <?php foreach($filterfields as $f=>$field) {
                                        ?>
                                        <option value="<?php echo $f; ?>"><?php echo $field; ?></option>
                                        <?php
                                    } ?>
                                </select>
                                <fieldset id="jform_display">
                                    <div class="btn-group radio arrang">
                                        <input class="arrng_order btn-check" type="radio" id="jform_display_a_1" name="arrange_type[1]" value="1">
                                        <label for="jform_display_a_1" class="btn btn-outline-secondary">Ascending</label>
                                        <input class="arrng_order btn-check" type="radio" id="jform_display_b_1" name="arrange_type[1]" value="2">
                                        <label for="jform_display_b_1" class="btn btn-outline-secondary">Descending</label>
                                    </div>
                                </fieldset>
                                <a data-index="1"  href="#" class="btn btn-info add_criteria_arrb"><span class="icon-save-new" aria-hidden="true"></span>Add</a>
                                <a class="ck" href="#">-</a>
                            </div>
                            <?php  } ?>
                        </div>
            
            </div>
           <?php /* <div class="col-md-12">
                <div class="form-group">
                    <label for="list_results">List Results By : </label>
                    <div class="row mt-2">
                        <div class="col-md-5">
                            <select class="form-select selfield" style="max-width:240px;">
                                <option value="">Select Field</option>
                                <?php foreach($filterfields as $f=>$ff) { ?>
                                    <option <?php if($fdtk['field']==$f) { ?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $ff; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <fieldset id="jform_display">
                                <div class="btn-group radio arrang">
                                    <input <?php if($fdtk['order']==1) { ?>checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_a_<?php echo $fdi; ?>" name="arrange_type_<?php echo $fdi; ?>[]" value="1">
                                    <label for="jform_display_a_<?php echo $fdi; ?>" class="btn btn-outline-secondary">Ascending</label>
                                    <input <?php if($fdtk['order']==2) { ?>checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_b_<?php echo $fdi; ?>" name="arrange_type_<?php echo $fdi; ?>[]" value="2">
                                    <label for="jform_display_b_<?php echo $fdi; ?>" class="btn btn-outline-secondary">Descending</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div> */ ?>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'design', Text::_('COM_STORE_LOCATOR_TAB_DESIGN')); ?>
        
		
		
		
		<div class="px-4 py-2 bg-light rounded ">
	
		
		<?php
                	
                    $mwd = 9;
                    if(isset($this->item->map_width))
                    {
                        $mwd = $this->item->map_width;
                    }

                    $msd = 3;
                    if(isset($this->item->results_width))
                    {
                        $msd = $this->item->results_width;
                    }
                    ?>
                    <div class="container-fluid p-0 cstmmap ">
                        <!-- Top row with 9-3 split -->
                        <div class="row g-0 js-draggable">
                            <div class="col-md-<?php echo $mwd; ?> position-relative map_con" id="map-container">  Map Container </div>
                            <div class="col-md-<?php echo $msd; ?> res_con" id="results-container"> Location Results </div>
                        </div>
                        
                   
                    </div>

<div class="row">
<div class="col-md-3"> <?php echo $this->form->renderField('map_width'); ?></div>
<div class="col-md-3"> <?php echo $this->form->renderField('map_height'); ?></div>
<div class="col-md-6"> <?php echo $this->form->renderField('results_width'); ?>	 </div>

<?php echo $this->form->renderField('location_order'); ?>
 <?php echo $this->form->renderField('map_order'); ?>
</div>
</div>
		
		<br/><br/>
		
		
		
		<div class="row">
           
			
			
			
				

              

             
                <?php
                	echo '<div class="mpthm">'.$this->form->renderField('map_theme').'</div>';
                	echo '<div class="mpskn">'.$this->form->renderField('map_skin').'</div>';
                ?>
         
        </div>
		
			
		
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'filter', Text::_('COM_STORE_LOCATOR_TAB_FILTER')); ?>
		
		      <div class="container-fluid mb-4">
                        <div class="mb-4">
                            <h2 class="h4 mb-2">Map Theme:</h2>
                         	 <small><span style="color: #999999;">the layout theme for your map controls the overall styling, link color, etc</span></small>
							
							<br/>	<br/>
							
							 <div class="row">
                            <!-- Theme 1 -->
                             <?php foreach($customData as $m) { ?>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="card h-100 border rounded-3 map-theme" data-id="<?php echo $m->id; ?>">
                                    <div class="card-body text-center">
                                        <div class="theme-preview mb-3">
                                            <img src="<?php echo Uri::root().$m->theme_icon; ?>" alt="Theme 1 Preview" class="img-fluid mb-0">
                                        </div>
                                        <div class="text-center;"><?php echo $m->theme_title; ?> </div>
                                         <div class="card-body text-center">  <a target="_blank" href="<?php echo Uri::root(); ?>administrator/index.php?option=com_store_locator&view=maptheme&layout=edit&id=<?php echo $m->id; ?>" class="btn btn-danger btn-sm text-white px-4">edit</a> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- Repeat for Themes 3-10 -->
							
							
                        </div>
							
							
                        </div>
                        
                       
                    </div>
		
      
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'templates', Text::_('COM_STORE_LOCATOR_TAB_TEMPLATES')); ?>
       
			       <div class="container-fluid mb-4 mt-2">
                        <div class="mb-4">
                            <h2 class="h4 mb-2">Map Skin:</h2>
                            
							<small><span style="color: #999999;">the map skin controls the visual appearance of your map</span></small>
                        </div>
                        
                        <div class="row">
                            <!-- Theme 1 -->
                             <?php foreach($mapskins as $m) { ?>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="card h-100 border rounded-3 map-skin" data-id="<?php echo $m->id; ?>">
                                    <div class="card-body text-center">
                                        <div class="theme-preview mb-3">
                                            <img src="<?php echo Uri::root().$m->skin_icon; ?>" alt="Theme 1 Preview" class="img-fluid mb-3">
                                        </div>
                                         <div class="text-center;"><?php echo $m->skin_title; ?></div>
                                            <div class="card-body text-center">  <a target="_blank" href="<?php echo Uri::root(); ?>administrator/index.php?option=com_store_locator&view=mapskin&layout=edit&id=<?php echo $m->id; ?>" class="btn btn-danger btn-sm text-white px-4">edit</a> 
                                        </div>
                                    </div>
									
                                </div>
								
								
                            </div>
                            <?php } ?>
                            <!-- Repeat for Themes 3-10 -->
                        </div>
                    </div>
	
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	
		
    </div>
   
	
	
    </div>                  
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
	
	
<div class="col-lg-3"> <div class="main-card px-4 py-4 border rounded">	 

<div class="accordion" id="LocationMap">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
         <span style="color: #1b7fcf;"><span class="icon-cog" aria-hidden="true"></span>&nbsp; Location Template Layout Options</span>
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#LocationMap">
      <div class="accordion-body">
       
	   <small><span style="color: #999999;">select the default location templates for this map</span></small>
	   <br/><br/>    
	  
	   <?php echo $this->form->renderField('location_result'); ?> 
	   <?php echo $this->form->renderField('location_card'); ?>
	    <?php echo $this->form->renderField('location_details'); ?>
	     
      </div>
    </div>
  </div>
  
  
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
       <span style="color: #1b7fcf;"><span class="icon-cog" aria-hidden="true"></span>&nbsp; Location Filter Template</span>
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#LocationMap">
      <div class="accordion-body">
        
		
			<?php
			echo $this->form->renderField('filter');
            echo $this->form->renderField('filter_position');
                	
                ?>
		
      </div>
    </div>
  </div>
  

</div>






  
 
						
						
						




</div></div>	
</div>

</div></div>



  
     
</form>
<style>
.address_box {
    padding: 9px 0;
}
.hide{display: none;}
.show{display: block; }

.control-group {
    display: flex !important;
    flex-direction: column !important;
	width: auto;
}

.control-label{
    display: flex !important;
    flex-direction: column !important;
	width: auto;
}


.cstmmap {
    padding: 0px !important;
    background: #f2f2f2;
    color: #000;
    margin: 20px 0;
}

.map_con, .res_con {
    padding: 12px;
    border: 1px solid #1b7fcf;
	border-radius: 8px;
}
.card.h-100.border-primary.map-theme, .map-skin {
    border: 1px solid;
}
.mpthm, .mpskn {
    opacity: 0;
    height: 0;
    padding: 0;
    width: 0;
}
.csthidn {
    opacity: 0;
    height: 0;
    padding: 0;
}

.card {
    transition: border-color 0.3s ease;
    cursor: pointer;
}

.card:hover {
    border-color: #0dcaf0;
}



.theme-preview img {
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.color-swatch {
    border-radius: 4px;
}

/* Custom breakpoint adjustments */
@media (min-width: 768px) and (max-width: 991.98px) {
    .row-cols-md-2 > * {
        flex: 0 0 auto;
        width: 50%;
    }
}

@media (min-width: 992px) {
    .row-cols-lg-5 > * {
        flex: 0 0 auto;
        width: 20%;
    }
}
.active_map
{
    background:var(--form-select-background) !important;
}
.res_con {
    position: relative;
}
h2.lctm {
    text-align: center;
}

.loc-map-locations {
    margin-top: 20px;
}
.loc-map-locations .p-3 {
    background: #323232;
}
#sortable-4 { list-style-type: none; margin: 0; padding: 0; width: 100%; }
#sortable-4 li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#sortable-4 li span { position: absolute; margin-left: -1.3em; }
</style>
<script>
    $('.map-theme').click(function(){
        var di = $(this).data('id');
        $('.map-theme').removeClass('active_map');
        $('.mpthm').find('select').val(di);
        $(this).addClass('active_map');
    });
    <?php if(isset($this->item->map_theme)) { ?>
        $('.map-theme[data-id="'+<?php echo $this->item->map_theme; ?>+'"]').addClass('active_map');
    <?php } ?>

    $('.map-skin').click(function(){
        var di = $(this).data('id');
        $('.map-skin').removeClass('active_map');
        $('.mpskn').find('select').val(di);
        $(this).addClass('active_map');
    });
    <?php if(isset($this->item->map_skin)) { ?>
        $('.map-skin[data-id="'+<?php echo $this->item->map_skin; ?>+'"]').addClass('active_map');
    <?php } ?>

    $(document).on('change', '.fsel', function(){
        updatecirteriadata();
        return false;
    });
    $(document).on('change', '.arrang input', function(){
        updatecirteriadata();
        return false;
    });
    function updatecirteriadata()  {
        var filterdata = [];
        jQuery('.single_box_arr').each(function(){
            var filter = jQuery(this);
            var lfd = filter.find('.fsel').val();
            var ctype = filter.find('.arrng_order:checked').val();
            filterdata.push(
                {
                    field: lfd, 
                    order: ctype,
                }
            );
        });
        
            jQuery('input#jform_list_results').val(JSON.stringify(filterdata));	
            console.log(filterdata);
    
    }
    $(document).on('click', '.ck', function(){
        var conf = confirm("Are you sure?");
        if(conf)
        {
            $(this).parent().remove();
            var tid = jQuery('.add_criteria_arrb').attr('data-index');
            var tjj = parseInt(tid)-1;
            jQuery('.add_criteria_arrb').attr('data-index', tjj);
            updatecirteriadata();
        }
        return false;
    });
    jQuery(document).on('click', '.add_criteria_arrb', function(){
        var tpq = jQuery(this);
        var did = jQuery(this).attr('data-index');
        var newid = parseInt(did)+1;
        var jqbx = jQuery('.single_box_arr').first().clone();
        jqbx.find('select').val('');
        jqbx.find('.arrng_order').attr('name', 'arrange_type['+newid+']')
        jqbx.find('label').each(function(){
            var tg = jQuery(this).attr('for');
            jQuery(this).attr('for', tg.replace(did, newid));
            var tgnew = jQuery(this).attr('for');
            jQuery(this).prev().attr('id', tgnew);
        });
        jQuery('.field-arrange').append(jqbx);
        jQuery('.add_criteria_arrb').attr('data-index',newid);
        return false;
    });
</script>