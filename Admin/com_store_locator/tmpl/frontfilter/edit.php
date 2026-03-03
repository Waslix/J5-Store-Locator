<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Store_locator
 * @author     Rohit Kishore <probloggerrohit@gmail.com>
 * @copyright  2024 Rohit Kishore
 * @license    GNU General Public License version 2 or later; see LICENSE.txt

<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'LocationData', Text::_('COM_STORE_LOCATOR_FORM_LBL_FILTER_DATA', true)); ?> 
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
HTMLHelper::_('jquery.framework');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
$filterfields = $this->get('filterfields');
// echo '<pre>'.print_r($filterfields, true);

?>

<form
	action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="frontfilters-form" class="form-validate form-horizontal">

	
	
	
	
<br/>
<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">

	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/frontfilter/frontend-filter.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Location Filter Designer</span></h2>
<h3><span style="color: #3e6aa7;">Frontend Filter</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">Setup a custom field layout to be used to filter location results.</span></small>
	
</div>


<div class="col-md-5">
			<fieldset class="adminform">
			<?php echo $this->form->renderField('filter_title'); ?>
				<?php echo $this->form->renderField('description'); ?>
			</fieldset>
</div>




<div class="col-md-2"><br/>
			<fieldset class="adminform">
				<?php echo $this->form->renderField('filter_position'); ?>
				<?php echo $this->form->renderField('show_text_search'); ?>
			</fieldset>
			</div>
			
<div class="col-md-2"><br/>
			<fieldset class="adminform">
				<?php echo $this->form->renderField('state'); ?>
				
			</fieldset>
			</div>
</div>
<br/><br/>
	
	
	
	
	
	<div class="row main-card px-4 py-4 border rounded">
		
			<fieldset class="adminform">
				
				
				
				<div class="filter_data-field">
					<?php echo $this->form->renderField('filter_data'); ?>
				</div>
				<?php
				$fdt = array();
				if(isset($this->item->filter_data))
				{
					$fd = $this->item->filter_data;
					$fd = json_decode($fd,true);
					$fdt = $fd;
					// echo "<pre>".print_r($fd,true)."</pre>";
				}
				if(!empty($fdt))
				{
					?>
					<div class="filter_data">
						<div class="column-box">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h2 class="m-0">Frontend Layout</h2>
							<button class="btn btn-success add-btn" onclick="addColumn(); return false;">+ Add Column</button>
						</div>
						<div class="filtering-container" id="filter-columns">
							<?php foreach ($fdt as $s) { ?>
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
								<div class="field-box m-0 mb-3 mt-2 row">
										<div class="col p-0">
											<select name="ftype[]" class="form-select ftypsel">
												<option>1 Line without Title</option>
											</select>
										</div>
										<div class="col p-0">
											<select name="filterfield[]" class="form-select ffd fldfilter">
												<option>Select Field</option>
												<?php foreach($filterfields as $f=>$field) {
													?>
													<option <?php if($s['field']==$f) { ?>selected="selected" <?php } ?> value="<?php echo $f; ?>"><?php echo $field; ?></option>
													<?php
												} ?>
											</select>
										</div>
										<div class="col-12 mt-2 p-0">
											<div class="fg-group row">
												<div class="col-md-8">
													<input value="<?php echo $s['class']; ?>" name="filteclass[]" type="text" class="form-control csscls" placeholder="CSS Class">
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
					<?php
				}
				else
				{
					?>
					<div class="filter_data">
						<div class="column-box">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h2>Frontend Layout</h2>
							<button class="btn btn-link add-btn" onclick="addColumn(); return false;">+ Add Column</button>
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
											<select name="ftype[]" class="form-select ftypsel">
												<option>1 Line without Title</option>
											</select>
										</div>
										<div class="col p-0">
											<select name="filterfield[]" class="form-select ffd fldfilter">
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
						</div>
					</div>
					</div>
					<?php
				}
				?>
				
			</fieldset>
	

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
select.form-select.col-wd {
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
.filter_data {
    background: #f5f5f5;
    padding: 20px;
}
[data-color-scheme="dark"] .filter_data {
	background: #282828;
}
[data-color-scheme="dark"] .filter-column {
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
.filter-column {
	min-width: 380px;
	border: 1px solid #ced4da;
	padding: 10px;
	border-radius: 5px;
	background-color: #fff;
}
.filter-column.dragging {
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
</style>
<script>
	function addColumn() {
	// HTML structure for a new column
	var ffd = jQuery('.ffd').html();
	var cl = jQuery('.col-wd').html();
	ffd = '<select name="filterfield[]" class="form-select fldfilter">'+ffd+'</select>';
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
						<select name="ftype[]" class="form-select ftypsel">
							<option>1 Line without Title</option>
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
	document.getElementById("filter-columns").insertAdjacentHTML("beforeend", columnHTML);
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
        var tk = jQuery(this).parent().parent().parent().parent();
        tk.remove();
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
		var filter = jQuery(this);
		var filter_width = filter.find('.col-wd').val();
		var filterheading = filter.find('.headingdt').val();
		var ftype = filter.find('.ftypsel').val();
		var filter_field = filter.find('.fldfilter').val();
		var csscls = filter.find('.csscls').val();
		filterdata.push(
			{
				heading: filterheading, 
				width: filter_width,
				class: csscls,
				type: ftype,
				field: filter_field
			}
		);
	});
	jQuery('input#jform_filter_data').val(JSON.stringify(filterdata));
    console.log(filterdata);
}
</script>