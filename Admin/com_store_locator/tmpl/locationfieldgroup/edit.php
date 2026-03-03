<?php

/** Location Field Group
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Banners\Administrator\View\Client\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>

<form action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="client-form" aria-label="<?php echo Text::_('COM_BANNERS_CLIENT_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">



<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">
	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/locationfieldgroup/field-group.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Location Field Group</span></h2>
<h3><span style="color: #3e6aa7;">Group Fields Together</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">Create a field group to keep your custom fields organized. Field Groups will appear as tabs in your admin location listing.</span></small>
	</div>




	 <div class="col-4">
	 <?php echo $this->form->renderField('title');?>
	
	 
	 </div>
		
		 <div class="col-lg-3">
	<?php echo $this->form->renderField('description'); ?>
		 
		 </div>
		 
		<div class="col-lg-2"> 
		
		<fieldset class="adminform"> 
		<?php echo $this->form->renderField('state'); ?> 
		
		</fieldset> 
		
		
		 
		
		</div>
		

</div>
<br/>





 <div class="row">
  <div class="col-lg-9"><div class="main-card px-4 py-4 border rounded"> 
  
  
  
  <br/>
  
 
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', 'Icons'); ?>
     
		  <div class="border rounded-3 bg-light px-4 py-4">
				function- select icons for tabs/ field groups
            </div>
  
  
  </div></div>

  <div class="col-lg-3"><div class="main-card px-4 py-4 border rounded"> 
   
    
				
				<?php echo  $this->form->renderField('usergroup'); echo "Function Control Access <br/><br/>"?>
				
  </div></div>

</div>
		   
		 
	     
        
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    


    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
	

</form>

<style>
.control-group .control-label {
  width: auto;
}
</style>