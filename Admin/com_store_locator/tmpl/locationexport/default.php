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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('jquery.framework');

$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
?>

<form action="<?php echo Route::_('index.php?option=com_store_locator&task=locationexport&controller=Locatorlocation'); ?>"
      method="post" 
      name="adminForm" 
      id="export-form" 
      class="form-validate">
    
       <div class="row-fluid main-card px-4 py-4 border rounded">
        <div class="span12">
            <div class="well">
                <h2 class="module-title nav-header">
                    <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_OPTIONS'); ?>
                </h2>
                
                <fieldset class="form-horizontal">
                    <!-- Export Range Options -->
                    <div class="control-group">
                        <div class="control-label">
                            <label for="export_range">
                                <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_RANGE'); ?>
                            </label>
                        </div>
                        <div class="controls">
                            <select name="export_range" id="export_range" class="input-large">
                                <option value="all">
                                    <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_RANGE_ALL'); ?>
                                </option>
                                <option value="selected">
                                    <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_RANGE_SELECTED'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Date Range (shown/hidden based on selection) -->
                    <div class="control-group" id="date-range-group" style="display: none;">
                        <div class="control-label">
                            <label for="date_start">
                                <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_DATE_START'); ?>
                            </label>
                        </div>
                        <div class="controls">
                            <?php echo HTMLHelper::_('calendar', '', 'date_start', 'date_start', '%Y-%m-%d'); ?>
                            
                            <label for="date_end" style="margin-left: 20px;">
                                <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_DATE_END'); ?>
                            </label>
                            <?php echo HTMLHelper::_('calendar', '', 'date_end', 'date_end', '%Y-%m-%d'); ?>
                        </div>
                    </div>
                    
                    <!-- Export Format -->
                    <div class="control-group">
                        <div class="control-label">
                            <label for="export_format">
                                <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_FORMAT'); ?>
                            </label>
                        </div>
                        <div class="controls">
                            <select name="export_format" id="export_format" class="input-medium">
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    
                
                </fieldset>
                
                <!-- Export Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <span class="icon-download"></span>
                        <?php echo Text::_('COM_YOURCOMPONENT_EXPORT_START'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php echo HTMLHelper::_('form.token'); ?>
    <input type="hidden" name="task" value="locationexport" />
</form>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#export_range').on('change', function() {
        if ($(this).val() === 'date') {
            $('#date-range-group').show();
        } else {
            $('#date-range-group').hide();
        }
    });
    
    // Form validation
    $('#export-form').on('submit', function(e) {
        if ($('#export_range').val() === 'date') {
            var startDate = $('#date_start').val();
            var endDate = $('#date_end').val();
            
            if (!startDate || !endDate) {
                alert('<?php echo Text::_('COM_YOURCOMPONENT_EXPORT_DATE_REQUIRED'); ?>');
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
<style>
 select#export_format {
    min-width: 145px;
}
</style>