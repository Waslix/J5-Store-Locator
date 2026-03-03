<?php

/**Location Fields
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
    HTMLHelper::_('jquery.framework');
?>

<form action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="client-form" aria-label="<?php echo Text::_('COM_BANNERS_CLIENT_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>" class="form-validate">

    <?php //echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    
	
<div class="row main-card px-4 py-4 border rounded">

		<div class="col-md-3 bg-light rounded-3 border px-4 py-3">
				<div class="row">
					<div class="col-3"> <img src="/administrator/components/com_store_locator/tmpl/locationfield/fields.png" alt=" "> </div>
	
					<div class="col-9">
					<h2><span style="color: #2e486b;">Location Fields</span></h2>
					<h3><span style="color: #3e6aa7;">Create Custom Fields</span></h3>
					</div>
					
				</div>

		<small><span style="color: #a8a8a8;">Create custom fields to save additional information about a location.</span></small>
		</div>


	 <div class="col-4"> <?php echo $this->form->renderField('title');?>
	   
	
		 <div class="row">
	 <div class="col-6"><?php echo $this->form->renderField('name'); ?> </div>
	 <div class="col-6"> <?php echo $this->form->renderField('label'); ?> </div>
	</div>
	
	 
	 </div>
		
		 <div class="col-lg-3">
		<br/>
		 <?php echo $this->form->renderField('description'); ?>
		 
		 </div>
		 
		<div class="col-lg-2"> 
		<br/>
		 <?php  echo $this->form->renderField('field_group'); ?>
	</div>
		

</div>
<br/>

	
	
	
	
	 <div class="row">			
           <div class="col-lg-9"> <div class="main-card px-4 py-4 border rounded">

	
	
	<?php echo "<br/>" ?>
	
        <?php echo  HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', 'Field Details'); ?>
        <div class="row">
		
		
		   <div class="col-lg-2">
		   
		  
		   
			<?php echo $this->form->renderField('required'); ?> <br/>
			 <?php echo $this->form->renderField('detail_link'); ?> 
		</div>
		
		
		
            <div class="col-lg-7">
                <?php
                echo $this->form->renderField('type');
                //echo $this->form->renderField('ordering');
                ?>
            <div class="control-group conditional_options">
                <div class="control-label">
                    <label id="jform_type-lbl" for="jform_type" class="required">Field Options<span class="star" aria-hidden="true">&nbsp;*</span></label>
                </div>
                <div class="controls has-success">
                    <div class="table-responsive">
                        <table class="table" id="subfieldList_jform_fieldparams_options">
                        <thead>
                            <tr>
                                <th scope="col" style="width:45%">label&nbsp;*</th>
                                <th scope="col" style="width:45%">Database Value</th>
                                <td style="width:8%;">
                                <div class="btn-group">
                                    <button type="button" class="group-add btn btn-sm btn-success" aria-label="Add">
                                        <span class="icon-plus" aria-hidden="true"></span>
                                    </button>
                                </div>
                                </td>
                            </tr>
                            </thead>
                            <tbody class="subform-repeatable-container">
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
			
			
            <div class="col-lg-3">
			<?php  //echo $this->form->renderField('class');?>	
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'design', 'Additional Options'); ?>
        <div class="row">
            <div class="col-12 col-lg-6">
				<?php
                	echo $this->form->renderField('default_value');
                	echo $this->form->renderField('placeholder');
			?>
                	
             
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JCONFIG_PERMISSIONS_LABEL')); ?>
        <div class="row">
            <div class="col-12">
                <?php echo $this->form->getInput('rules'); ?>
                
                
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?> </div>
		   
		   
		   
		   
	 <div class="col-lg-3"><div class="main-card px-4 py-4 border rounded">
	
		<fieldset class="adminform"> 
	 <?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
		
		</fieldset> 
	
	   <?php echo $this->form->renderField('usergroup'); ?>
	 	
	</div></div>

	</div>
	
	
	
	
	
</form>

<script>
<?php
if(isset($this->item->params))
{
    $ps = $this->item->params;
    $psd = json_decode($ps,true);
    $fopt = $psd['field_options'];
    ?>
    $('input#jform_default_value').val('<?php echo $psd['default']; ?>');
    $('input#jform_placeholder').val('<?php echo $psd['placeholder']; ?>');
    $('#jform_class').val('<?php echo $psd['class']; ?>');
    $('#jform_name').val('<?php echo $psd['name']; ?>');
    $('#jform_label').val('<?php echo $psd['label']; ?>');
    var tyyp = $('select#jform_type').val();
    if((tyyp=='list') || (tyyp=='radio') || (tyyp=='checkbox')){
            $('.conditional_options').addClass('hactive');
    }
    else
    {
        $('.conditional_options').removeClass('hactive');
    }
    <?php
    if(!empty($fopt))
    {
       $txp = $fopt['text'];
       $txval = $fopt['value'];
       for ($i=0; $i < count($txp); $i++) { 
            ?>
            var newRow = `
            <tr class="subform-repeatable-group">
                    <td>
                        <input type="text" value="<?php echo $txp[$i]; ?>" name="field_options[text][]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" value="<?php echo $txval[$i]; ?>" name="field_options[value][]" class="form-control">
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="group-remove btn btn-sm btn-danger" aria-label="Remove">
                                <span class="icon-minus" aria-hidden="true"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            $('.subform-repeatable-container').append(newRow);
            <?php
       }
    }
    // die;
    // $ps = substr($ps, 1, -1);
    // echo $ps;
    // parse_str($ps,$myArray);
    // echo "<pre>".print_r($psd, true);
    // echo $ps;
}
?>
jQuery(document).ready(function($) {
    $(document).on('change', 'select#jform_type', function(){
        var vl = $(this).val(); 
        if((vl=='list') || (vl=='radio') || (vl=='checkbox')){
            $('.conditional_options').addClass('hactive');
        }
        else
        {
            $('.conditional_options').removeClass('hactive');
        }
    });
    // Handle add button click
    $('.group-add').on('click', function() {
        addNewRow();
    });

    // Function to remove row
    $(document).on('click', '.group-remove', function() {
        $(this).closest('tr').remove();
    });

    // Function to add new row
    function addNewRow() {
        var newRow = `
            <tr class="subform-repeatable-group">
                <td>
                    <input type="text" name="field_options[text][]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="field_options[value][]" class="form-control">
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="group-remove btn btn-sm btn-danger" aria-label="Remove">
                            <span class="icon-minus" aria-hidden="true"></span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        $('.subform-repeatable-container').append(newRow);
    }
});
</script>
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

.control-group.conditional_options {
    display:none !important;
}
body .control-group.conditional_options.hactive
{
    display:flex !important;
}
tr.subform-repeatable-group input {
    width: 100%;
}
</style>
