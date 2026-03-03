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
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
?>

<form action="<?php echo Route::_('index.php?option=com_store_locator&task=locationimport&controller=Locatorlocation'); ?>"
      method="post" 
      enctype="multipart/form-data" 
      name="adminForm" 
      id="adminForm">
  




  
    <div class="row-fluid main-card px-4 py-4 border rounded">
        <div class="span6">
            <div class="control-group">
                <div class="control-label">
                    <label for="csvfile">Select CSV File</label>
                </div>
                <div class="controls">
                    <input type="file" name="csvfile" id="csvfile" accept=".csv" required />
                </div>
            </div>
        </div>
    
    <div id="fieldMapping" style="display:none;">
        <h3>Map Fields</h3>
        <div id="mappingContainer"></div>
    </div>
         <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <span class="icon-download"></span>
                        Import File
                    </button>
                </div>
    <?php echo HTMLHelper::_('form.token'); ?>
	
	
	</div>
	
</form>

<script>
jQuery(document).ready(function($) {
    $('#csvfile').on('change', function(e) {
        var file = e.target.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var csv = e.target.result;
            var lines = csv.split("\n");
            var headers = lines[0].split(",");
            
            var availableFields = <?php echo json_encode($this->availableFields); ?>;
            var mappingHtml = '';
            
            $.each(availableFields, function(field, label) {
                mappingHtml += '<div class="control-group">';
                mappingHtml += '<label class="control-label">' + label + '</label>';
                mappingHtml += '<div class="controls">';
                mappingHtml += '<select class="form-select" name="field_mapping[' + field + ']">';
                mappingHtml += '<option value="">-- Select Field --</option>';
                
                $.each(headers, function(i, header) {
                    mappingHtml += '<option value="' + header.trim() + '">' + header.trim() + '</option>';
                });
                
                mappingHtml += '</select>';
                mappingHtml += '</div></div>';
            });
            
            $('#mappingContainer').html(mappingHtml);
            $('#fieldMapping').show();
        };
        
        reader.readAsText(file);
    });
});
</script>