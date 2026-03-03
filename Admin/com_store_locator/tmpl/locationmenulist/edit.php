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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Storelocator\Component\Store_locator\Administrator\Model\LocatorlocationModel;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('jquery.framework');
HTMLHelper::_('formbehavior.chosen', '.mpt');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
$document->addStyleSheet(Uri::root() . "media/com_store_locator/css/custom.css");
$field_groups = LocatorlocationModel::getfieldgroups();
$fields_data = LocatorlocationModel::getfields();
$filterfields = $this->get('filterfields');
// echo "<pre>".print_r($custom_data2, true);
?>

<form
	action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="locationresults-form" class="form-validate form-horizontal">






<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">
	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/locationmenulist/location-list.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Location List</span></h2>
<h3><span style="color: #3e6aa7;">Custom list of locations</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">Compile a custom list of locations based on specific field parameters</span></small>
	</div>




	 <div class="col-3 cstm_title">
	 <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
	 </div>
		
		 <div class="col-lg-2">
			<fieldset class="adminform">
				  <?php echo $this->form->renderField('usergroup');?>
			</fieldset>
		 </div>
		  
		 
		 <div class="col-lg-2">		
			<fieldset class="adminform">
				  <?php	echo $this->form->renderField('frontend_access'); ?>
			</fieldset>
		 </div>	 
		 
		 
		<div class="col-lg-2"> 
		<fieldset class="adminform"> 
				<?php echo $this->form->renderField('state'); ?> 
		</fieldset> </div>
	

</div>


<br/><br/>












	
	
	

	<div class="row main-card px-1 py-4 border rounded">
	
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); ?>
				
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'LocationData', 'Layout Structure'); ?>

	<div class="row py-2">
							<div class="col-2">	
								<h2 class="m-0">Location List Layout</h2>
								<small><span style="color: #a8a8a8;">Customize your listing layout </span></small>
							</div>
							<div class="col-2">	<?php echo $this->form->renderField('display'); ?> </div>					
						</div>					
											
		
					
					
						
						
						
						<div class="row main-card px-4 py-4 mt-4 border rounded">
						
						
						
						
												
												
												
						<div class="row py-3 bg-light">
								<div class="col-10">	</div>	
								<div class="col-2"> <button data-id="<?php echo $porr; ?>" class="btn btn-info" onclick="addColumn(); return false;">+ Add Column</button> </div>				
						</div>
											
											
		
								<fieldset class="adminform mt-4">
		
									<div class="filter_data-field">
										<?php echo $this->form->renderField('template_data'); ?>
									</div>
									<?php
									$fdt = array();
									if(isset($this->item->template_data))
									{
										$fd = $this->item->template_data;
										$fd = json_decode($fd,true);
										$fdt = $fd;
										// echo "<pre>".print_r($fd,true)."</pre>";
										// die;
									}
									if(!empty($fdt))
									{
										$porr = count($fdt)-1;
										?>
										<div class="filter_data">
											<div class="column-box">
											
											
											
										
											
											
											
											
											
											<div class="filtering-container" id="filter-columns">
												<?php foreach ($fdt as $l=>$s) { ?>
												<div class="filter-column" draggable="true">
													<div class="align-items-baseline row">
														<div class="col-md-8">
															<div class="form-group">
															<label class="">Column Width</label>
																<select name="filterwidth[]" class="form-select col-wd">
																	<option value=""></option>
																	<option <?php if($s['width']==1) { ?>selected="selected" <?php } ?> value="1">1</option>
																	<option <?php if($s['width']==2) { ?>selected="selected" <?php } ?> value="2">2</option>
																	<option <?php if($s['width']==3) { ?>selected="selected" <?php } ?> value="3">3</option>
																	<option <?php if($s['width']==4) { ?>selected="selected" <?php } ?> value="4">4</option>
																	<option <?php if($s['width']==5) { ?>selected="selected" <?php } ?> value="5">5</option>
																	<option <?php if($s['width']==6) { ?>selected="selected" <?php } ?> value="6">6</option>
																	<option <?php if($s['width']==7) { ?>selected="selected" <?php } ?> value="7">7</option>
																	<option <?php if($s['width']==8) { ?>selected="selected" <?php } ?> value="8">8</option>
																	<option <?php if($s['width']==9) { ?>selected="selected" <?php } ?> value="9">9</option>
																	<option <?php if($s['width']==10) { ?>selected="selected" <?php } ?> value="10">10</option>
																	<option <?php if($s['width']==11) { ?>selected="selected" <?php } ?> value="11">11</option>
																	<option <?php if($s['width']==12) { ?>selected="selected" <?php } ?> value="12">12</option>
																</select>
																</div>
															</div>
														<div class="col-md-4">
															<div class="control-buttons">
																	<button class="control-btn"><i class="icon-move"></i></button>
																	<button class="control-btn rmvfulrow">-</button>
																</div>
														</div>
													</div>
													
													<input value="<?php echo $s['heading']; ?>" name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
													<?php
													$sffu = $s['fields'];
													if(!empty($sffu))
													{
													foreach($sffu as $q=>$sfs) { ?>
													<div class="field-box m-0 mb-3 mt-2 row">
															<div class="col p-0">
																<select name="ftype[<?php echo $l; ?>][]" class="form-select ftypsel">
																	<option value="">-Select Display-</option>
																	<option <?php if($sfs['type']==1) { ?>selected="selected" <?php } ?> value="1">1 Line with Title</option>
																	<option <?php if($sfs['type']==2) { ?>selected="selected" <?php } ?> value="2">1 Line with empty Title</option>
																	<option <?php if($sfs['type']==3) { ?>selected="selected" <?php } ?> value="3">1 Line without Title</option>
																	<option <?php if($sfs['type']==4) { ?>selected="selected" <?php } ?> value="4">2 Lines with Title</option>
																</select>
															</div>
															<div class="col p-0">
																<select name="filterfield[<?php echo $l; ?>][]" class="form-select ffd fldfilter">
																	<option>Select Field</option>
																	<?php foreach($filterfields as $f=>$field) {
																		?>
																		<option <?php if($sfs['field']==$f) { ?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
																		<?php
																	} ?>
																</select>
															</div>
															<div class="col-12 mt-2 p-0">
																<div class="fg-group row">
																	<div class="col-md-8">
																		<input value="<?php echo $sfs['class']; ?>" name="filteclass[<?php echo $l; ?>][]" type="text" class="form-control csscls" placeholder="CSS Class">
																	</div>
																	<div class="col-md-4">
																		<button class="control-btn"><i class="icon-move"></i></button>
																		<button class="control-btn rmv-fd">-</button>
																		<button class="add-btn-fgp">+</button>
																	</div>
																</div>
															</div>
													</div>
													<?php } 
													} else { ?>
													<div class="field-box m-0 mb-3 mt-2 row">
														<div class="col p-0">
															<select name="ftype[<?php echo $l; ?>][]" class="form-select ftypsel">
																<option value="">-Select Display-</option>
																<option value="1">1 Line with Title</option>
																<option value="2">1 Line with empty Title</option>
																<option value="3">1 Line without Title</option>
																<option value="4">2 Lines with Title</option>
															</select>
														</div>
														<div class="col p-0">
															<select name="filterfield[<?php echo $l; ?>][]" class="form-select ffd fldfilter">
																<option>Select Field</option>
																<?php foreach($filterfields as $f=>$field) {
																	?>
																	<option value="<?php echo $f; ?>"><?php echo $field; ?></option>
																	<?php
																} ?>
															</select>
														</div>
														<div class="col-12 mt-2 p-0">
															<div class="fg-group row">
																<div class="col-md-8">
																	<input value="" name="filteclass[<?php echo $l; ?>][]" type="text" class="form-control csscls" placeholder="CSS Class">
																</div>
																<div class="col-md-4">
																	<button class="control-btn"><i class="icon-move"></i></button>
																	<button class="control-btn rmv-fd">-</button>
																	<button class="add-btn-fgp">+</button>
																</div>
															</div>
														</div>
													</div>
													<?php } ?>
												</div>
												<?php } ?>
											</div>
										</div>
										</div>
										<?php
									}
									else
									{
										?>
										<div class="filter_data">
											<div class="column-box">
											<div class="d-flex justify-content-between align-items-center mb-3">
												<h2>Frontend Layout</h2>
												<button data-id="0" class="btn btn-link add-btn" onclick="addColumn(); return false;">+ Add Column</button>
											</div>
											<div class="filtering-container" id="filter-columns">
												<!-- Example of a Filter Column -->
												<div class="filter-column" draggable="true">
													<div class="align-items-baseline row">
														<div class="col-md-8">
															<div class="form-group">
															<label class="">Column Width</label>
																<select name="filterwidth[]" class="form-select col-wd">
																	<option value=""></option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																	<option value="4">4</option>
																	<option value="5">5</option>
																	<option value="6">6</option>
																	<option value="7">7</option>
																	<option value="8">8</option>
																	<option value="9">9</option>
																	<option value="10">10</option>
																	<option value="11">11</option>
																	<option value="12">12</option>
																</select>
																</div>
															</div>
														<div class="col-md-4">
															<div class="control-buttons">
																	<button class="control-btn"><i class="icon-move"></i></button>
																	<button class="control-btn rmvfulrow">-</button>
																</div>
														</div>
													</div>
													
													<input name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
													<div class="field-box m-0 mb-3 mt-2 row">
															<div class="col p-0">
																<select name="ftype[0][]" class="form-select ftypsel">
																	<option value="">-Select Display-</option>
																	<option value="1">1 Line with Title</option>
																	<option value="2">1 Line with empty Title</option>
																	<option value="3">1 Line without Title</option>
																	<option value="4">2 Lines with Title</option>
																</select>
															</div>
															<div class="col p-0">
																<select name="filterfield[0][]" class="form-select ffd fldfilter">
																	<option>Select Field</option>
																	<?php foreach($filterfields as $f=>$field) {
																		?>
																		<option value="<?php echo $f; ?>"><?php echo $field; ?></option>
																		<?php
																	} ?>
																</select>
															</div>
															<div class="col-12 mt-2 p-0">
																<div class="fg-group row">
																	<div class="col-md-8">
																		<input name="filteclass[0][]" type="text" class="form-control csscls" placeholder="CSS Class">
																	</div>
																	<div class="col-md-4">
																		<button class="control-btn"><i class="icon-move"></i></button>
																		<button class="control-btn rmv-fd">-</button>
																		<button class="add-btn-fgp">+</button>
																	</div>
																</div>
															</div>
													</div>
												</div>
											</div>
										</div>
										</div>
										<?php
									}
									?>
									
								</fieldset>
						


								<div class="row col-12 py-4 bg-light">
										<div class="col-2"><h3> PAGING</h3><small><span style="color: #a8a8a8;">total listings per page </span></small></div>	
										<div class="col-1">	 <?php echo $this->form->renderField('paging'); ?></div>
										
								</div>								





						</div>
					
				
			
			<?php echo HTMLHelper::_('uitab.endTab'); ?>


	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'Fieldgroup', 'Field Criteria'); ?>
	
	<h2 class="m-0">Field Criteria</h2>	
<small><span style="color: #a8a8a8;">Location listings matching these field criteria will be displayed your custom location list.</span></small><br/>
	
		<div class="row">
	<div class="col-12 main-card px-4 py-4 mt-4 border rounded">
		
	
	

				<div class="criteria-field">
					<?php echo $this->form->renderField('criteria'); ?>
				</div>
				<div class="bg-body-secondary field-criteria p-3">
					<a href="#" class="btn btn-info add_criteria"><span class="icon-save-new" aria-hidden="true"></span> Add Field Criteria</a>
					<?php
					$fdtc = array();
					if(isset($this->item->criteria))
					{
						$fdc = $this->item->criteria;
						$fdc = json_decode($fdc,true);
						$fdtc = $fdc;
						// echo "<pre>".print_r($fdtc,true)."</pre>";
						// die;
					}
					if(!empty($fdtc))
					{
						foreach($fdtc as $fcb)
						{
							$fied = $fcb['field'];
							$ftyp = $fcb['type'];
							$fval = $fcb['value'];
							?>
							<div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3 single_box">
								<select name="list_field[]" class="form-select fsel" data-val="<?php echo $fval; ?>">
									<option value="">Select Field</option>
									<?php foreach($filterfields as $f=>$field) {
										?>
										<option <?php if($f==$fied) {?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
										<?php
									} ?>
								</select>
								<select name="condition_type[]" class="form-select cdtype">
									<option value="">Select Condition Type</option>
									<option <?php if($ftyp==1) { ?>selected="selected" <?php } ?> value="1">Contains</option>
									<option <?php if($ftyp==2) { ?>selected="selected" <?php } ?> value="2">Does not contains</option>
									<option <?php if($ftyp==3) { ?>selected="selected" <?php } ?> value="3">Equal To</option>
									<option <?php if($ftyp==4) { ?>selected="selected" <?php } ?> value="4">Not Equal To</option>
								</select>
								<div class="dynamic_field_choice">

								</div>
								<a class="ck" href="#">-</a>
							</div>
							<?php
						}
					}
					else
					{
					?>
					<div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3 single_box">
						<select name="list_field[]" class="form-select fsel">
							<option value="">Select Field</option>
							<?php foreach($filterfields as $f=>$field) {
								?>
								<option value="<?php echo $f; ?>"><?php echo $field; ?></option>
								<?php
							} ?>
						</select>
						<select name="condition_type[]" class="form-select cdtype">
							<option value="">Select Condition Type</option>
							<option value="1">Contains</option>
							<option value="2">Does not contains</option>
							<option value="3">Equal To</option>
							<option value="4">Not Equal To</option>
						</select>
						<div class="dynamic_field_choice">

						</div>
						<a class="ck" href="#">-</a>
					</div>
					<?php } ?>
				</div>
	

	</div>
	
	
	
	

	

</div>
	
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'Arrangeby', 'Arrange By Fields'); ?>
			
				<h2 class="m-0">Arrange By</h2>
	<small><span style="color: #a8a8a8;">Listings will be arranged according to these fields</span></small><br/>
			
			
				<div class="col-12 main-card px-4 py-4 mt-4 border rounded">
	
		
				<div class="arrange-by-field">
					<?php echo $this->form->renderField('arrange_by'); ?>
				</div>
				<div class="bg-body-secondary field-arrange p-3">
					<a href="#" class="btn btn-info add_criteria_arr"><span class="icon-save-new" aria-hidden="true"></span>Add Field</a>
					<?php
					$fdtcarr = array();
					if(isset($this->item->arrange_by))
					{
						$fdcarr = $this->item->arrange_by;
						$fdcarr = json_decode($fdcarr,true);
						$fdtcarr = $fdcarr;
					}
					if(!empty($fdtcarr))
					{
						foreach($fdtcarr as $fo)
						{
							$fdi = $fo['field'];
							$fod = $fo['order'];
							?>
							<div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3  single_box_arr">
								<select name="arrange_field[]" class="form-select farr">
									<option value="">Select Field</option>
									<?php foreach($filterfields as $f=>$field) {
										?>
										<option <?php if($fdi==$f) { ?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
										<?php
									} ?>
								</select>
								<fieldset id="jform_display">
									<div class="btn-group radio">
										<input <?php if($fod==1) { ?> checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_a_<?php echo $fdi; ?>" name="arrange_type_<?php echo $fdi; ?>[]" value="1">
										<label for="jform_display_a_<?php echo $fdi; ?>" class="btn btn-outline-secondary">Ascending</label>
										<input <?php if($fod==2) { ?> checked="checked" <?php } ?> class="arrng_order btn-check" type="radio" id="jform_display_b_<?php echo $fdi; ?>" name="arrange_type_<?php echo $fdi; ?>[]" value="2">
										<label for="jform_display_b_<?php echo $fdi; ?>" class="btn btn-outline-secondary">Descending</label>
									</div>
								</fieldset>
								<a href="#" class="btn btn-info add_criteria_arr"><span class="icon-save-new" aria-hidden="true"></span>Add</a>
								<a class="ck" href="#">-</a>
							</div>
							<?php
						}
					}
					else
					{
					?>
					<div class="align-items-center bg-body d-flex gap-5 has-success mt-3 p-3  single_box_arr">
						<select name="arrange_field[]" class="form-select farr">
							<option value="">Select Field</option>
							<?php foreach($filterfields as $f=>$field) {
								?>
								<option value="<?php echo $f; ?>"><?php echo $field; ?></option>
								<?php
							} ?>
						</select>
						<fieldset id="jform_display">
							<div class="btn-group radio">
								<input class="arrng_order btn-check" type="radio" id="jform_display_a" name="arrange_type[]" value="1">
								<label for="jform_display_a" class="btn btn-outline-secondary">Ascending</label>
								<input class="arrng_order btn-check" type="radio" id="jform_display_b" name="arrange_type[]" value="2">
								<label for="jform_display_b" class="btn btn-outline-secondary">Descending</label>
							</div>
						</fieldset>
						<a href="#" class="btn btn-info add_criteria_arr"><span class="icon-save-new" aria-hidden="true"></span>Add</a>
						<a class="ck" href="#">-</a>
					</div>
					<?php } ?>
				</div>
	
	</div>
			
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
	


			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'Searchfilter', 'Search and Filter'); ?>
			
			
			<div class="row mt-2">
			<div class="col-4">
			
				<h2 class="m-0">Search and Filter</h2>	
<small><span style="color: #a8a8a8;">Add fields to create a custom search and filtering function for this location list</span></small><br/>
			
			</div>
		
			
			
			<div class="col-8">
			<div class="row py-3 bg-light">

				<div class="col-3"><?php echo $this->form->renderField('search_pos'); ?></div>
				<div class="col-2"><?php echo $this->form->renderField('search_expanded'); ?></div>
				<div class="col-7"><br/>
				
				<div class="search-field">
					<?php echo $this->form->renderField('search_data'); ?>
				</div>
				
				
				<div class="search-from">
					<div class="from-field">
						<?php echo $this->form->renderField('search_from'); ?>
					</div>
					<?php
					$opo = array();
						if(isset($this->item->search_from))
						{
							$sfr = $this->item->search_from;
							$opo = json_decode($sfr, true);
						}
					?>
					<div class="from-inner">
						<label>Search <div class="srch-bxx"><i class="icon-search"></i></div></label>
						<label><select class="inputbox mpt" multiple style="width:400px;">
						<?php foreach($filterfields as $f=>$field) {
							?>
						    <option <?php if(in_array($f, $opo)) { ?> selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
							<?php
						} ?>
						</select>
						<br>
						<small>use these fields as part of a text search</small></label>
					</div>
				</div>
				</div>
			
			</div>
			
			</div>
				</div>
			
			
				
				
				
				
				<div class="filter_data2 mt-4">
					<div class="column-box">
					<div class="d-flex justify-content-between align-items-center mb-3">
						
						
						
						
					<div class="row">
						<div class="col-12 main-card px-4 py-4 mt-2 border rounded">		
						
						
						
						
						
						<div class="row py-4 px-2 bg-light">
								<div class="col-10"><h2 class="m-0">Filtering Layout</h2>
								<small><span style="color: #a8a8a8;">Customize your filter function layout </span></small>	</div>	
								<div class="col-2"> 	<button class="btn btn-info mt-2" onclick="addColumn2(); return false;">+ Add Column</button> </div>				
						</div>
						
						
						
					
					
					<div class="filtering-container mt-4" id="filter-columns2">
					<?php
						$fdtk = array();
						if(isset($this->item->search_data))
						{
							$fdkss = $this->item->search_data;
							$fdkss = json_decode($fdkss,true);
							$fdtk = $fdkss;
							// echo "<pre>".print_r($fd,true)."</pre>";
							// die;
						}
						if(!empty($fdtk))
						{
							foreach($fdtk as $fqq)
							{
								?>
								<div class="filter-column2" draggable="true">
									<div class="align-items-baseline row">
										<div class="col-md-8">
											<div class="form-group">
											<label class="">Column Width</label>
												<select name="filterwidth[]" class="form-select col-wd2">
													<option value=""></option>
													<option <?php if($fqq['width']==1) {?>selected="selected" <?php } ?> value="1">1</option>
													<option <?php if($fqq['width']==2) {?>selected="selected" <?php } ?> value="2">2</option>
													<option <?php if($fqq['width']==3) {?>selected="selected" <?php } ?> value="3">3</option>
													<option <?php if($fqq['width']==4) {?>selected="selected" <?php } ?> value="4">4</option>
													<option <?php if($fqq['width']==5) {?>selected="selected" <?php } ?> value="5">5</option>
													<option <?php if($fqq['width']==6) {?>selected="selected" <?php } ?> value="6">6</option>
													<option <?php if($fqq['width']==7) {?>selected="selected" <?php } ?> value="7">7</option>
													<option <?php if($fqq['width']==8) {?>selected="selected" <?php } ?> value="8">8</option>
													<option <?php if($fqq['width']==9) {?>selected="selected" <?php } ?> value="9">9</option>
													<option <?php if($fqq['width']==10) {?>selected="selected" <?php } ?> value="10">10</option>
													<option <?php if($fqq['width']==11) {?>selected="selected" <?php } ?> value="11">11</option>
													<option <?php if($fqq['width']==12) {?>selected="selected" <?php } ?> value="12">12</option>
												</select>
												</div>
											</div>
										<div class="col-md-4">
											<div class="control-buttons">
													<button class="control-btn"><i class="icon-move"></i></button>
													<button class="control-btn rmvfulrow">-</button>
												</div>
										</div>
									</div>
									
									<input value="<?php echo $fqq['heading']; ?>" name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
									<div class="field-box m-0 mb-3 mt-2 row">
											<div class="col p-0">
												<select name="ftype[]" class="form-select ftypsel">
													<option value="">-Select Display-</option>
													<option <?php if($fqq['type']==1) {?>selected="selected" <?php } ?> value="1">1 Line with Title</option>
													<option <?php if($fqq['type']==2) {?>selected="selected" <?php } ?> value="2">1 Line with empty Title</option>
													<option <?php if($fqq['type']==3) {?>selected="selected" <?php } ?> value="3">1 Line without Title</option>
													<option <?php if($fqq['type']==4) {?>selected="selected" <?php } ?> value="4">2 Lines with Title</option>
												</select>
											</div>
											<div class="col p-0">
												<select name="filterfield[]" class="form-select ffd2 fldfilter">
													<option>Select Field</option>
													<?php foreach($filterfields as $f=>$field) {
														?>
														<option <?php if($fqq['field']==$f) {?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
														<?php
													} ?>
												</select>
											</div>
											<div class="col-12 mt-2 p-0">
												<div class="fg-group row">
													<div class="col-md-8">
														<input value="<?php echo $fqq['class']; ?>" name="filteclass[]" type="text" class="form-control csscls" placeholder="CSS Class">
													</div>
													<div class="col-md-4">
														<button class="control-btn"><i class="icon-move"></i></button>
														<button class="control-btn rmv-fd">-</button>
														<button class="add-btn-fgp">+</button>
													</div>
												</div>
											</div>
									</div>
								</div>
								<?php
							}
						}
						else {
						?>
						<div class="filter-column2" draggable="true">
							<div class="align-items-baseline row">
								<div class="col-md-8">
									<div class="form-group">
									<label class="">Column Width</label>
										<select name="filterwidth[]" class="form-select col-wd2">
											<option value=""></option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
											<option value="12">12</option>
										</select>
										</div>
									</div>
								<div class="col-md-4">
									<div class="control-buttons">
											<button class="control-btn"><i class="icon-move"></i></button>
											<button class="control-btn rmvfulrow">-</button>
										</div>
								</div>
							</div>
							
							<input name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
							<div class="field-box m-0 mb-3 mt-2 row">
									<div class="col p-0">
										<select name="ftype[]" class="form-select ftypsel">
											<option value="">-Select Display-</option>
											<option value="1">1 Line with Title</option>
											<option value="2">1 Line with empty Title</option>
											<option value="3">1 Line without Title</option>
											<option value="4">2 Lines with Title</option>
										</select>
									</div>
									<div class="col p-0">
										<select name="filterfield[]" class="form-select ffd2 fldfilter">
											<option>Select Field</option>
											<?php foreach($filterfields as $f=>$field) {
												?>
												<option value="<?php echo $f; ?>"><?php echo $field; ?></option>
												<?php
											} ?>
										</select>
									</div>
									<div class="col-12 mt-2 p-0">
										<div class="fg-group row">
											<div class="col-md-8">
												<input name="filteclass[]" type="text" class="form-control csscls" placeholder="CSS Class">
											</div>
											<div class="col-md-4">
												<button class="control-btn"><i class="icon-move"></i></button>
												<button class="control-btn rmv-fd">-</button>
												<button class="add-btn-fgp">+</button>
											</div>
										</div>
									</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			
				</div>
				
			
			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />


			<?php echo $this->form->renderField('created_by'); ?>
			<?php echo $this->form->renderField('modified_by'); ?>

			
			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		
		
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
<style>
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

button.control-btn.rmv-fd {
    position: relative;
    top: 2px;
}
.filter-card {
	background: white;
	border: 1px solid #dee2e6;
	border-radius: 4px;
	padding: 15px;
	margin-bottom: 15px;
}
.column-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 15px;
}
.control-buttons {
	display: flex;
    gap: 5px;
    justify-content: flex-end;
}
select.form-select.col-wd, select.form-select.col-wd2 {
    max-width: 40%;
    display: inline-block;
    margin-left: 10px;
}
.control-btn {
	border: 1px solid #dee2e6;
	background: none;
	padding: 0 5px;
	border-radius: 4px;
}
button.control-btn.rmv-fd {
    background: #f5f5f5;
    border-radius: 100%;
    padding: 10px 10px;
    line-height: 7px;
    font-size: 22px;
}
.add-btn {
	color: #0d6efd;
	background: none;
	border: none;
}
.field-box {
    background: #f5f5f5;
    padding: 10px;
    border-radius: 10px;
}
.fg-group.row {
    display: flex;
    align-items: center;
}
.field-box.m-0.mb-3.mt-2.row .col {
    padding-right: 10px !important;
}
.filter_data. .filter_data2 {
    background: #f5f5f5;
    padding: 20px;
}
[data-color-scheme="dark"] .filter_data, [data-color-scheme="dark"] .filter_data2 {
	background: #282828;
}
[data-color-scheme="dark"] .filter-column, [data-color-scheme="dark"] .filter-column2 {
    background: #3c3c3c;
    border-color: #000;
}

[data-color-scheme="dark"] button.control-btn.rmvfulrow {
    background: #2a2a2a;
}

[data-color-scheme="dark"] .field-box {
    background: #000;
}

[data-color-scheme="dark"] button.control-btn.rmv-fd {
    background: #000;
}
button.add-btn {
    padding: 10px;
    background: #fff;
    color: #000;
    border-radius: 10px;
}
button.add-btn-fgp {
    background: #2196f3;
    border: none;
    border-radius: 100%;
    padding: 3px 8px;
    color: #fff;
    font-size: 19px;
    line-height: 22px;
}
.status-box {
	padding: 15px;
	border-radius: 5px;
	border: 1px solid #ced4da;
	background-color: #fff;
}
.column-box {
	padding: 10px;
	border-radius: 5px;
	margin-bottom: 10px;
}
.title-box {
	border: 1px solid #ced4da;
	padding: 10px;
	border-radius: 5px;
	background-color: #fff;
}
.header-box {
	border: 1px solid #ced4da;
	padding: 10px;
	border-radius: 5px;
	background-color: #212529;
	color: #fff;
}
.filtering-container {
	display: flex;
	overflow-x: auto;
	gap: 10px;
	padding-bottom: 10px;
}
.filter-column, .filter-column2 {
	min-width: 380px;
	border: 1px solid #ced4da;
	padding: 10px;
	border-radius: 5px;
	background-color: #fff;
	margin: 0 10px 10px 0;
}
.filter-column.dragging, .filter-column2.dragging {
	opacity: 0.5;
}
.tags-container {
	display: flex;
	flex-wrap: wrap;
	gap: 5px;
}
.tag {
	background-color: #007bff;
	color: white;
	padding: 3px 8px;
	border-radius: 20px;
	font-size: 0.9rem;
}
.tag i {
	margin-left: 5px;
	cursor: pointer;
}
button.control-btn.rmvfulrow {
    background: #f5f5f5;
    border-radius: 30px;
    padding: 0 10px;
    font-size: 17px;
}
.filter_data-field {
    opacity: 0;
    height: 0;
	visibility: hidden;
}
.from-inner {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: flex-start;
    align-items: center;
}

.from-inner label {
    width: 40%;
}

.from-inner label .srch-bxx {
    width: 60%;
    display: inline-block;
    margin-left: 20px;
    padding: 7px;
    background: #f2f2f2;
    border-radius: 6px;
}

.cstm_title .col-md-6 {
    width: 100%;
}
.srch-bxx i.icon-search {
    color: #000;
}
.filter_data2.mt-4 {
    overflow-x: auto;
}
select.inputbox.mpt {
    height: 50px;
}
ul.chosen-choices {
    min-height: 30px;
}
.single_box_arr a.btn.btn-info.add_criteria_arr {
    position: absolute;
    right: 60px;
    border-radius: 50px;
    padding: 5px 17px;
}

.single_box_arr {
    position: relative;
}
</style>
<script>
	function addColumn() {
	// HTML structure for a new column
	var did = jQuery('.add-btn').attr('data-id');
	var pk = parseInt(did)+1;
	var ffd = jQuery('.ffd').html();
	var cl = jQuery('.col-wd').html();
	ffd = '<select name="filterfield['+pk+'][]" class="form-select fldfilter">'+ffd+'</select>';
	cl = '<select name="filterwidth[]" class="form-select col-wd">'+cl+'</select>';
	let columnHTML = `
		<div class="filter-column" draggable="true">
			<div class="row align-items-baseline">
				<div class="col-md-8">
					<div class="form-group">
					<label class="">Column Width</label>
						${cl}
						</div>
					</div>
				<div class="col-md-4">
					<div class="control-buttons">
							<button class="control-btn"><i class="icon-move"></i></button>
							<button class="control-btn rmvfulrow">-</button>
						</div>
				</div>
			</div>
			<input name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
			<div class="field-box m-0 mb-3 mt-2 row">
					<div class="col p-0">
						<select name="ftype[${pk}][]" class="form-select ftypsel">
							<option value="">-Select Display-</option>
							<option value="1">1 Line with Title</option>
							<option value="2">1 Line with empty Title</option>
							<option value="3">1 Line without Title</option>
							<option value="4">2 Lines with Title</option>
						</select>
					</div>
					<div class="col p-0">
						${ffd}
					</div>
					<div class="col-12 mt-2 p-0">
						<div class="fg-group row">
							<div class="col-md-8">
								<input name="filteclass[${pk}][]" type="text" class="form-control csscls" placeholder="CSS Class">
							</div>
							<div class="col-md-4">
								<button class="control-btn"><i class="icon-move"></i></button>
								<button class="control-btn rmv-fd">-</button>
								<button class="add-btn-fgp">+</button>
							</div>
						</div>
					</div>
			</div>
		</div>
	`;
	jQuery('.add-btn').attr('data-id',pk);
	document.getElementById("filter-columns").insertAdjacentHTML("beforeend", columnHTML);
	initializeDragAndDrop();
}
function addColumn2() {
	// HTML structure for a new column
	var ffd = jQuery('.ffd2').html();
	var cl = jQuery('.col-wd2').html();
	ffd = '<select name="filterfield[]" class="form-select fldfilter">'+ffd+'</select>';
	cl = '<select name="filterwidth[]" class="form-select col-wd2">'+cl+'</select>';
	let columnHTML = `
		<div class="filter-column2" draggable="true">
			<div class="row align-items-baseline">
				<div class="col-md-8">
					<div class="form-group">
					<label class="">Column Width</label>
						${cl}
						</div>
					</div>
				<div class="col-md-4">
					<div class="control-buttons">
							<button class="control-btn"><i class="icon-move"></i></button>
							<button class="control-btn rmvfulrow">-</button>
						</div>
				</div>
			</div>
			<input name="filterheading[]" type="text" class="form-control mb-2 headingdt" placeholder="Heading">
			<div class="field-box m-0 mb-3 mt-2 row">
					<div class="col p-0">
						<select name="ftype[]" class="form-select ftypsel">
							<option value="">-Select Display-</option>
							<option value="1">1 Line with Title</option>
							<option value="2">1 Line with empty Title</option>
							<option value="3">1 Line without Title</option>
							<option value="4">2 Lines with Title</option>
						</select>
					</div>
					<div class="col p-0">
						${ffd}
					</div>
					<div class="col-12 mt-2 p-0">
						<div class="fg-group row">
							<div class="col-md-8">
								<input name="filteclass[]" type="text" class="form-control csscls" placeholder="CSS Class">
							</div>
							<div class="col-md-4">
								<button class="control-btn"><i class="icon-move"></i></button>
								<button class="control-btn rmv-fd">-</button>
								<button class="add-btn-fgp">+</button>
							</div>
						</div>
					</div>
			</div>
		</div>
	`;
	document.getElementById("filter-columns2").insertAdjacentHTML("beforeend", columnHTML);
	initializeDragAndDrop();
}

// Handle adding and removing tags
function addTag() {
	const input = document.getElementById("tag-input");
	const tagValue = input.value.trim();
	if (tagValue) {
		const tagContainer = document.getElementById("tags-container");
		const tag = document.createElement("span");
		tag.classList.add("tag");
		tag.innerHTML = `${tagValue} <i class="bi bi-x-circle" onclick="removeTag(this)"></i>`;
		tagContainer.appendChild(tag);
		input.value = "";
	}
}

function removeTag(element) {
	element.parentElement.remove();
}

// Drag and Drop for Columns
function initializeDragAndDrop() {
	const filterColumns = document.querySelectorAll('.filter-column');
	let draggedItem = null;

	filterColumns.forEach(column => {
		column.addEventListener('dragstart', function () {
			draggedItem = this;
			setTimeout(() => this.classList.add('dragging'), 0);
		});

		column.addEventListener('dragend', function () {
			setTimeout(() => this.classList.remove('dragging'), 0);
			draggedItem = null;
		});

		column.addEventListener('dragover', function (e) {
			e.preventDefault();
			if (draggedItem !== this) {
				const container = this.parentNode;
				const siblings = [...container.querySelectorAll('.filter-column')];
				const draggedOverIndex = siblings.indexOf(this);
				const draggedItemIndex = siblings.indexOf(draggedItem);
				if (draggedItemIndex < draggedOverIndex) {
					container.insertBefore(draggedItem, this.nextSibling);
				} else {
					container.insertBefore(draggedItem, this);
				}
			}
		});
	});
}
jQuery(document).on('click', '.add-btn-fgp', function(){
	var tk = jQuery(this).parent().parent().parent().parent().clone();
	var mn = jQuery(this).parent().parent().parent().parent().parent();
	tk.appendTo(mn);
	return false;
});
jQuery(document).on('click','.rmv-fd', function(){
	var tk = jQuery(this).parent().parent().parent().parent();
	tk.remove();
	return false;
});
jQuery(document).on('click', '.rmvfulrow', function(){
	var cnf = confirm("Are you sure you want to delete this filter?");
	if(cnf){
		var did = jQuery('.add-btn').attr('data-id');
		var pk = parseInt(did)-1;
        var tk = jQuery(this).parent().parent().parent().parent();
        tk.remove();
		jQuery('.add-btn').attr('data-id',pk);
    }
	return false;
});
initializeDragAndDrop();
jQuery(document).on('change', '.filter_data input', function(){
	updatefilterdata();
});
jQuery(document).on('change', '.filter_data select', function(){
	updatefilterdata();
});
function updatefilterdata()  {
	var filterdata = [];
	jQuery('.filter-column').each(function(){
		var flds = [];
		var filter = jQuery(this);
		var filter_width = filter.find('.col-wd').val();
		var filterheading = filter.find('.headingdt').val();
		// var ftype = filter.find('.ftypsel').val();
		// var filter_field = filter.find('.fldfilter').val();
		// var csscls = filter.find('.csscls').val();
		jQuery(this).find('.field-box').each(function(){
			var ts = jQuery(this);
			var ftype = ts.find('.ftypsel').val();
			var filter_field = ts.find('.fldfilter').val();
			var csscls = ts.find('.csscls').val();
			flds.push(
				{
					class: csscls,
					type: ftype,
					field: filter_field
				}
			);
		});
		filterdata.push(
			{
				heading: filterheading, 
				width: filter_width,
				fields: flds,
			}
		);
	});
	jQuery('input#jform_template_data').val(JSON.stringify(filterdata));
    console.log(filterdata);
}

jQuery(document).on('change', '.field-criteria input', function(){
	updatecirteriadata();
});
jQuery(document).on('change', '.field-criteria select', function(){
	updatecirteriadata();
});
$(document).on('click', '.ck', function(){
	var conf = confirm("Are you sure?");
	if(conf)
	{
		$(this).parent().remove();
		updatecirteriadata();
	}
	return false;
});
function updatecirteriadata()  {
	setTimeout(() => {
	var filterdata = [];
	jQuery('.single_box').each(function(){
		var filter = jQuery(this);
		var lfd = filter.find('.fsel').val();
		var ctype = filter.find('.cdtype').val();
		var cndval = filter.find('.cndval').val();
		filterdata.push(
			{
				field: lfd, 
				type: ctype,
				value: cndval
			}
		);
	});
	
		jQuery('input#jform_criteria').val(JSON.stringify(filterdata));	
		console.log(filterdata);
	}, 1000);
   
}

jQuery(document).on('change', '.field-arrange .farr', function(){
	updatearrangedata();
});
jQuery(document).on('change', '.field-arrange .arrng_order', function(){
	updatearrangedata();
});
function updatearrangedata()  {
	var uparrangedata = [];
	jQuery('.single_box_arr').each(function(){
		var filter = jQuery(this);
		var lfield = filter.find('.farr').val();
		var arrorder = filter.find('.arrng_order:checked').val();
		uparrangedata.push(
			{
				field: lfield, 
				order: arrorder,
			}
		);
	});
	jQuery('input#jform_arrange_by').val(JSON.stringify(uparrangedata));
}

jQuery(document).on('change', '.filter_data2 input', function(){
	updatesearchdata();
});
jQuery(document).on('change', '.filter_data2 select', function(){
	updatesearchdata();
});

function save_from_values() {
	var select_button_text = jQuery('.mpt option:selected')
                .toArray().map(item => item.value);
	jQuery('input#jform_search_from').val(JSON.stringify(select_button_text));
}

jQuery(document).on('change', '.mpt', function(){
	save_from_values();
});
function updatesearchdata()  {
	var filterdata2 = [];
	jQuery('.filter-column2').each(function(){
		var filter = jQuery(this);
		var filter_width = filter.find('.col-wd2').val();
		var filterheading = filter.find('.headingdt').val();
		var ftype = filter.find('.ftypsel').val();
		var filter_field = filter.find('.fldfilter').val();
		var csscls = filter.find('.csscls').val();
		filterdata2.push(
			{
				heading: filterheading, 
				width: filter_width,
				class: csscls,
				type: ftype,
				field: filter_field
			}
		);
	});
	jQuery('input#jform_search_data').val(JSON.stringify(filterdata2));
}

<?php if(!empty($fdtc)) { ?>
	setTimeout(() => {
		jQuery('.fsel').trigger('change');
		setTimeout(() => {
			jQuery('.fsel').each(function(){
				var jatt = jQuery(this).attr('data-val');
				// console.log(jatt);
				jQuery(this).parent().find('.cndval').val(jatt);
			})
		}, 1000);
	}, 500);
	
<?php } ?>
</script>