<?php
/** Location Detail Page Template Designer
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access

//<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); 
// <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'LocationData', Text::_('COM_STORE_LOCATOR_TAB_DETAIL_TEMPLATE', true));


defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Storelocator\Component\Store_locator\Administrator\Model\LocatorlocationModel;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('jquery.framework');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
$document->addStyleSheet(Uri::root() . "media/com_store_locator/css/custom.css");
$field_groups = LocatorlocationModel::getfieldgroups();
$fields_data = LocatorlocationModel::getfields();
?>

<form
	action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="locationdetail-form" class="form-validate form-horizontal">
	
	
	

<br/>
<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">

	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/locationdetail/detail-page-styling.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Layout Designer</span></h2>
<h3><span style="color: #3e6aa7;">Detail Page Styling</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">This template is used for the informative detailed page for a location. Use the text editor below, along with your fields to create a custom style layout for a location.</span></small>
	
</div>


<div class="col-md-5">
			<fieldset class="adminform">
			<?php echo $this->form->renderField('template_title'); ?>
				<?php echo $this->form->renderField('description'); ?>
			</fieldset>
</div>




<div class="col-md-2">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('css_class'); ?>
			</fieldset>
			</div>
			
<div class="col-md-2">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('state'); ?>
			</fieldset>
			</div>
</div>
<br/><br/>



<div class="row">
<div class="col-md-9"> <div class="main-card px-4 py-4 border rounded">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('template'); ?>
			</fieldset>
			
</div></div>
<div class="col-md-3"> <div class="main-card px-4 py-4 border rounded">


<h3>Available Fields:</h3>
<small><span style="color: #a8a8a8;">use these fields to build a custom detailed page for a location </span></small><br/><br/>



			<fieldset class="adminform">
				
				<div class="accordion" id="accordionExample">
					<?php if(!empty($field_groups)) {
						$i = 0;
						foreach($field_groups as $f=>$gp) {
							$fgid = $gp->id;
							$i++;
							?>
							<div class="accordion-item">
								<h2 class="accordion-header" id="heading<?php echo $i; ?>">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="false" aria-controls="collapseTwo">
								<?php echo $gp->title; ?>
								</button>
								</h2>
								<div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#accordionExample">
								<div class="accordion-body">
									<ul class="cs_list">
										<?php
										if($gp->title=='Core')
										{
											?>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="locationlistingtitle">Listing Title</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="catid">Category</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="user">User</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="image">Image</a></li>
											<?php
										}
										elseif($gp->title=='Address')
										{
											?>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="street">Street</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="city">City</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="user_state">State</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="country">Country</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="zip_code">Zip</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="latitude">Latitude</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="longitude">Longitude</a></li>
											<?php
										}
										elseif($gp->title=='Contact')
										{
											?>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="email">Email</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="website">Website</a></li>
											<li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="phone">Phone</a></li>
											<?php
										}
											foreach($fields_data as $fd) {
												if($fd->field_group == $fgid) {
                                                    ?>
                                                    <li><a href="#" class="ftag rounded-3 px-3 py-2" data-tag="field_<?php echo $fd->id;?>"><?php echo $fd->title;?></a></li>
                                                    <?php
                                                }
											}
										?>
									
									</ul>
								</div>
								</div>
							</div>
							<?php
						}
					} ?>
					</div>
					
	</fieldset>				
</div></div>
</div>
	


	
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />


	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>


<style>
.control-group .control-label {
  width: auto;
</style>

