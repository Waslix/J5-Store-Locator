<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'LocationData', Text::_('COM_STORE_LOCATOR_TAB_MAPTHEME', true)); ?>
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="locatorlocation-form" class="form-validate form-horizontal">




<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">

	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/maptheme/map-theme.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Map Designer</span></h2>
<h3><span style="color: #3e6aa7;">Map Theme</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">Create and change the visual style of your frontend location map. </span></small>
	
</div>



<div class="col-md-4">
			<fieldset class="adminform">
			<?php echo $this->form->renderField('theme_title'); ?>
			</fieldset>
</div>



<div class="col-md-3">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('description'); ?>
			</fieldset>
</div>


<div class="col-md-2">
			<fieldset class="adminform">
				<?php echo $this->form->renderField('state'); ?>
			</fieldset>
			</div>




</div>


<br/>






	<div class="row-fluid"><div class="main-card px-4 py-4 border rounded">

	
	
	<div class="row">

	<div class="col-lg-6 px-5">
			<fieldset class="adminform">

				<?php echo $this->form->renderField('toolbar_bg'); ?>
				<?php echo $this->form->renderField('results_bg'); ?>
				<?php echo $this->form->renderField('button_color'); ?>
				<?php echo $this->form->renderField('link_color'); ?>
				
			</fieldset>
	
	</div>
	<div class="col-lg-6">
			<fieldset class="adminform">
				<br/><br/>
			
				<?php echo $this->form->renderField('theme_icon'); ?>
			</fieldset>
	
	</div>
	</div>

	
	
	
	</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

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
</style>
