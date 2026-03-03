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
	method="post" enctype="multipart/form-data" name="adminForm" id="cardtemplates-form" class="form-validate form-horizontal">

	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'LocationData', 'Admin Location Listing'); ?>
	<div class="row">
		<div class="col-md-9 form-horizontal">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('template_title'); ?>
				<div class="hidnclas">
				<?php echo $this->form->renderField('css_class'); ?>
				</div>
				<?php echo $this->form->renderField('template'); ?>
			</fieldset>
		</div>
		<div class="col-md-3 form-horizontal">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('state'); ?>
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
											<li><a href="#" class="ftag" data-tag="locationlistingtitle">Listing Title</a></li>
											<li><a href="#" class="ftag" data-tag="catid">Category</a></li>
											<li><a href="#" class="ftag" data-tag="user">User</a></li>
											<li><a href="#" class="ftag" data-tag="image">Image</a></li>
											<?php
										}
										elseif($gp->title=='Address')
										{
											?>
											<li><a href="#" class="ftag" data-tag="street">Street</a></li>
											<li><a href="#" class="ftag" data-tag="city">City</a></li>
											<li><a href="#" class="ftag" data-tag="user_state">State</a></li>
											<li><a href="#" class="ftag" data-tag="country">Country</a></li>
											<li><a href="#" class="ftag" data-tag="zip_code">Zip</a></li>
											<li><a href="#" class="ftag" data-tag="latitude">Latitude</a></li>
											<li><a href="#" class="ftag" data-tag="longitude">Longitude</a></li>
											<?php
										}
										elseif($gp->title=='Contact')
										{
											?>
											<li><a href="#" class="ftag" data-tag="email">Email</a></li>
											<li><a href="#" class="ftag" data-tag="website">Website</a></li>
											<li><a href="#" class="ftag" data-tag="phone">Phone</a></li>
											<?php
										}
										// echo "<pre>".print_r($fields_data, true)."</pre>";
										foreach($fields_data as $fd) {
											if($fd->field_group == $fgid) {
												$prms = json_decode($fd->params,true);
												$nm = 'field_'.$fd->id;
												if(!empty($prms['name']))
												{
													$nm = $prms['name'];
												}
												?>
												<li><a data-id="<?php echo $fd->id; ?>" href="#" class="ftag" data-tag="<?php echo $nm;?>"><?php echo $fd->title;?></a></li>
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
				</div>
			</fieldset>
		</div>
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
	.hidnclas
	{
		opacity:0;
		visibility:hidden;
		height:0;
	}
</style>