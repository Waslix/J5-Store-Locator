<?php
/** Locator Locations
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
use \Joomla\CMS\Editor\Editor;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('bootstrap.modal');
HTMLHelper::_('formbehavior.chosen', 'select#jform_catid,.mpt');
HTMLHelper::_('jquery.framework');
$wa->useScript('field.calendar');
$document = Factory::getDocument();

$options = array("version" => "auto");
$attributes = array("defer" => "defer");
$scriptUrl = Uri::root() . "media/system/js/fields/joomla-field-media.min.js";
$headScripts = $document->_scripts;
// echo "<pre>".print_r($headScripts, true);
// die;
// Check if the script is already added
if (!isset($headScripts['/media/system/js/fields/joomla-field-media.min.js'])) {
    $document->addScript($scriptUrl, $options, array("type" => "module", "defer" => "defer"));
}
// $document->addScript(Uri::root() . "media/system/js/fields/joomla-field-media.min.js", $options, array("type" => "module", "defer" => "defer"));
// $document->addScript(Uri::root() . "media/system/js/fields/joomla-field-media.min.js", $options, $attributes);
$document->addScript(Uri::root() . "media/com_store_locator/js/custom.js", $options, $attributes);
$document->addStyleSheet(Uri::root()."media/system/css/fields/joomla-field-media.min.css");
$field_groups = $this->get('fieldgroups');
$fields = $this->get('fields');
$custom_data = array();
if(!empty($this->item->id))
{
	$custom_data = $this->getModel()->getstorecustom($this->item->id);
}
$editor = Editor::getInstance('tinymce');

$admin_entry = $this->item->admin_entry;
$admin_dat = array();
if(!empty($admin_entry))
{
	$admin_dat = $this->getModel()->getadminentry($admin_entry);
	if(!empty($admin_dat))
	{
		$admin_dat = $admin_dat[0];
		$templ = $admin_dat->template;
		$templateVariables = array(
			'{locationlistingtitle}' => $this->form->renderField('locationlistingtitle'),
			'{email}' => $this->form->renderField('email'),
			'{website}' => $this->form->renderField('website'),
			'{phone}' => $this->form->renderField('phone'),
			'{country}' => $this->form->renderField('country'),
			'{street}' => $this->form->renderField('street'),
			'{city}' => $this->form->renderField('city'),
			'{user_state}' => $this->form->renderField('user_state'),
			'{zip_code}' => $this->form->renderField('zip_code'),
			'{latitude}' => $this->form->renderField('latitude'),
			'{longitude}' => $this->form->renderField('longitude'),
			'{catid}' => $this->form->renderField('catid'),
			'{map}' => $this->form->renderField('map'),
			'{state}' => $this->form->renderField('state'),
			'{user}' => $this->form->renderField('user'),
			'{admin_entry}' => $this->form->renderField('admin_entry'),
			'{frontend_entry}' => $this->form->renderField('frontend_entry'),
			'{result_template}' => $this->form->renderField('result_template'),
			'{card_template}' => $this->form->renderField('card_template'),
			'{details_tempate}' => $this->form->renderField('details_tempate'),
		);

		foreach($fields as $f) { 
			$nm = 'field_'.$f->id;
			$prm = json_decode($f->params, true);
			$def = $prm['default'];
			$placeholder = $prm['placeholder'];
			$cls = $prm['class'];
			$req = $prm['required'];
			if(!empty($prm['name']))
			{
				$nm = $prm['name'];
			}
			$html = '';
			ob_start();
			if($f->type=='text') {
					$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
				?>
				<div class="control-group">
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="text" name="<?php echo $nm; ?>" id="" value="<?php echo $this_val; ?>" class="form-control <?php echo $cls; ?>" aria-describedby="-desc">
					</div>
				</div>
			<?php } elseif($f->type=='url') {
					$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
				?>
				<div class="control-group">
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="url" name="<?php echo $nm; ?>" id="" value="<?php echo $this_val; ?>" class="form-control <?php echo $cls; ?>" aria-describedby="-desc">
					</div>
				</div>
			<?php }	elseif($f->type=='calendar') {
					$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
					if(empty($this_val))
					{
						$this_val = date("Y-m-d");
					}
				?>
				<div class="control-group">
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
					<?php 
					$csdt = $nm;
					echo HTMLHelper::calendar($this_val,$csdt, 'date', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10', 'class'=>' validate[\'required\']',)); ?>
					</div>
				</div>
			<?php }
			elseif($f->type=='textarea') {
				$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
				?>

				<div class="control-group"> 
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<textarea placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> name="<?php echo $nm; ?>" id="" class="form-control <?php echo $cls; ?>" rows="3" aria-describedby="-desc"><?php echo $this_val; ?></textarea>
					</div>
				</div>									
				<?php
			}

			elseif($f->type=='editor') {
				$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
				?>

				<div class="control-group"> 
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<?php
						$params = array(
							'name' => $nm, // Name attribute for the textarea
							'id' => 'custom_editor_'.$f->id, // ID attribute for the textarea
							'class' => 'form-control '.$cls, // CSS classes
							'rows' => 10, // Number of rows
							'cols' => 50, // Number of columns
							'width' => '100%', // Width of the editor
							'height' => '300px' // Height of the editor
						);
						
						// Initial content (optional)
						$content = $this_val;
						
						// Render the editor
						echo $editor->display(
							$prm['name'],     // Name attribute
							$content,            // Initial content
							$prm['width'],    // Width
							$prm['height'],   // Height
							$prm['rows'],     // Rows
							$prm['cols'],     // Columns
							true,                // Buttons
							$prm['id'],       // ID attribute
							null,                // Asset
							null,                // Author
							$prm              // Additional parameters
						);
						?>
					</div>
				</div>									
				<?php
			}

			elseif($f->type=='image') {
				$this_val = $def;
					if(!empty($custom_data))
					{
						foreach($custom_data as $cdtk)
						{
							if($cdtk->field_id==$f->id)
							{
								if(!empty($cdtk->field_value))
								{
									$this_val = $cdtk->field_value;
								}
							}
						}
					}
				?>

				<div class="control-group"> 
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<joomla-field-media class="field-media-wrapper" type="image" base-path="https://vulcandescaler.joomtechsolutions.com/" root-folder="images" url="/administrator/index.php?option=com_jce&amp;task=plugin.display&amp;element=jform_custom_<?php echo $f->id; ?>&amp;mediatype=images&amp;converted=1&amp;context=427&amp;plugin=browser&amp;standalone=1&amp;f71bde1260e2b6acf8c203cfbcbc35b4=1&amp;client=1&amp;path=" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" modal-title="Change Image" preview="static" preview-container=".field-media-preview" preview-width="200" preview-height="200" supported-extensions="{&quot;images&quot;:&quot;jpg,jpeg,png,gif&quot;,&quot;audios&quot;:&quot;&quot;,&quot;videos&quot;:&quot;&quot;,&quot;documents&quot;:&quot;&quot;}">
							<div class="field-media-preview">
								<div class="preview_empty">No image selected.</div>             
								<div class="preview_img"><img class="media-preview" style="max-width:200px;max-height:200px;" src="" alt="Selected image."></div>        
							</div>
							<div class="input-group">
								<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="text" name="<?php echo $nm; ?>" id="jform_custom_<?php echo $f->id; ?>" value="<?php echo $this_val; ?>" data-wf-converted="1"class="field-media-input  input-medium wf-media-input wf-media-input-active wf-media-input-upload wf-media-input-converted form-control <?php echo $cls; ?>" aria-describedby="-desc">
								<button type="button" class="btn btn-success button-select">Select</button>
								<button type="button" class="btn btn-danger button-clear"><span class="icon-times" aria-hidden="true"></span><span class="visually-hidden">Clear</span></button>
							</div>
						</joomla-field-media>
					</div>
				</div>									
				<?php
			}
		elseif($f->type=='list') {
			$fg = $prm['field_options'];
			$txv = $fg['text'];
			$txw = $fg['value'];
			$this_val = '';
			// echo "<pre>".print_r($custom_data, true);
			if(!empty($custom_data))
			{
				foreach($custom_data as $cdtk)
				{
					if($cdtk->field_id==$f->id)
					{
						// echo "<pre>".print_r($cdtk, true);
						if(!empty($cdtk->field_value))
						{
							$this_val = $cdtk->field_value;
						}
					}
				}
			}
			?>
			<div class="control-group">
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						<select class="form-select selsm" name="<?php echo $nm; ?>">
							<option value="">Select Value</option>
							<?php for ($i=0; $i < count($txv); $i++) { 
								$ev = $txv[$i];
								$eq = $txw[$i];
							?>
							<option <?php if($this_val==$eq) { ?>selected="selected" <?php } ?> value="<?php echo $eq; ?>"><?php echo $ev; ?></option>
							<?php }?>
						</select>
					</div>
					
			</div>
			<?php
		}
		elseif($f->type=='radio') {
			$fg = $prm['field_options'];
			$txv = $fg['text'];
			$txw = $fg['value'];
			$this_val = '';
			if(!empty($custom_data))
			{
				foreach($custom_data as $cdtk)
				{
					if($cdtk->field_id==$f->id)
					{
						if(!empty($cdtk->field_value))
						{
							$this_val = $cdtk->field_value;
						}
					}
				}
			}
			?>
			<div class="control-group">
					<div class="control-label">
						<label id="-lbl" for=""><?php echo $f->title; ?></label>
					</div>
					<div class="controls">
						
					<?php for ($i=0; $i < count($txv); $i++) {
						$ev = $txv[$i];
						$eq = $txw[$i];
						?>
						<div class="form-check form-check-inline">
						<input type="radio" <?php if($this_val==$eq) {?>checked="checked" <?php }?> name="<?php echo $nm; ?>" id="radio<?php echo $i;?>" value="<?php echo $eq;?>" />
							<label class="form-check-label" for="radio<?php echo $i;?>"><?php echo $ev;?></label>
						</div>
						
								<?php }?>
					</div>
					
			</div>
			<?php
		}
		elseif($f->type=='checkbox') {
				$fg = $prm['field_options'];
				$txv = $fg['text'];
				$txw = $fg['value'];
				$this_val = '';
				if(!empty($custom_data))
				{
					foreach($custom_data as $cdtk)
					{
						if($cdtk->field_id==$f->id)
						{
							if(!empty($cdtk->field_value))
							{
								$this_val = $cdtk->field_value;
							}
						}
					}
				}
				?>
				<div class="control-group">
						<div class="control-label">
							<label id="-lbl" for=""><?php echo $f->title; ?></label>
						</div>
						<div class="controls">
							
						<?php for ($i=0; $i < count($txv); $i++) {
							$ev = $txv[$i];
							$eq = $txw[$i];
							?>
							<div class="form-check form-check-inline">
								<input type="checkbox" <?php if(in_array($eq,explode(',', $this_val
								))) {?>checked="checked" <?php }?> name="<?php echo $nm; ?>" id="checkbox<?php echo $i;?>" value="<?php
								echo $eq;?>" />
								<label class="form-check-label" for="checkbox<?php echo $i;?>"><?php
								echo $ev;?></label>
								</div>

							
							<?php }?>
						</div>
						
				</div>
				<?php
			}
			$html = ob_get_contents();
			ob_end_clean();
			$templateVariables['{'.$nm.'}'] =  $html;
		}
		// echo "<pre>".print_r($templateVariables, true)."</pre>";
		// die;
		
		// Replace the template variables with the corresponding form fields
		$templ = str_replace(array_keys($templateVariables), array_values($templateVariables), $templ);
	}
	
}
// echo "<pre>".print_r($fields,true);
?>

<form
	action="<?php echo Route::_('index.php?option=com_store_locator&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="locatorlocation-form" class="form-validate form-horizontal">


<div class="row main-card px-4 py-4 border rounded">

<div class="col-md-3 bg-light rounded-3 border px-3 py-3">
	<div class="row">
	<div class="col-3">
	<img src="/administrator/components/com_store_locator/tmpl/locatorlocation/location-listing.png" alt=" ">
	</div>
	<div class="col-9">
	<h2><span style="color: #2e486b;">Map Location</span></h2>
<h3><span style="color: #3e6aa7;">Create A Listing</span></h3>
</div>
	
	</div>


<small><span style="color: #a8a8a8;">Create a location listing and link it to maps, categories and select what layout templates should be used to display the information.</span></small>
	</div>




	 <div class="col-4">
	 <?php echo $this->form->renderField('locationlistingtitle');?> 
	
	 
	 </div>
		
		 <div class="col-lg-3">
		  <?php 
		 echo $this->form->renderField('catid');
		 echo $this->form->renderField('map'); ?>
		 
		 </div>
		 
		<div class="col-lg-2"> 
		
		<fieldset class="adminform"> <?php echo $this->form->renderField('state'); ?> 
		</fieldset> </div>
		

</div>
<br/>


	<div class="row">
		 <div class="col-lg-9"> 
			<div class="main-card px-4 py-4 border rounded">
				<?php if(!empty($admin_dat)) { 
					echo "<h2>".$admin_dat->template_title."</h2>";
					echo $templ;
				} else {  ?>
			<fieldset class="general">
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'LocationData')); ?>

				<?php if(!empty($field_groups)) { 
					$i = 0;
					foreach($field_groups as $fd) {
						$i++;
					?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'fieldgroup_'.$i, $fd->title); ?>
					<div class="row-fluid">
						<div class="col-md-12 ">
							<fieldset class="adminform">
								<?php if($fd->title=='Core') {

									// echo $this->form->renderField('image');
		
									
								} elseif($fd->title =='Address') { ?>
								
								
								<div class="row">
						<div class="col-md-8 px-5 py-1"> 
						<div class="border bg-light p-3 pb-1" style="border-radius: 0.5rem;">
								<?php 
									echo $this->form->renderField('country');?>
									</div>
									<br/>
									
									<?php 	
									echo $this->form->renderField('street');
									echo $this->form->renderField('city');
									echo $this->form->renderField('user_state');
									
									echo $this->form->renderField('zip_code');
						?>
						</div>
								
								
								
								 
								<div class="col-md-4 px-5 py-5 bg-light border rounded shadow-sm"><br/>
					<div class="geo_box">
				<h3>Map Coordinates</h3>
					<div class="loc_stacked">
					<?php echo $this->form->renderField('latitude');
					echo $this->form->renderField('longitude');  ?>
					<button class="find_coords btn btn-success mb-4">Find Geo Location Map Points</button>
					
				</div>

					</div>	
</div>					
								
								<?php 
								
									
									
								} elseif($fd->title=='Contact') { 
									echo $this->form->renderField('email');
									echo $this->form->renderField('website');
									echo $this->form->renderField('phone');
									
								} elseif($fd->title=='Open Schedule') { 
									?>
									
													
									<?php
								} ?>
								<?php foreach($fields as $f) { 
									if($f->field_group==$fd->id) {
										$prms = $f->params;
										$prms = json_decode($prms, true);
										$def = $prms['default'];
										$placeholder = $prms['placeholder'];
										$cls = $prms['class'];
										$req = $prms['required'];
										$nm = 'custom_data['.$f->id.']';
										if(!empty($prms['name']))
										{
											$nm = $prms['name'];
										}
										// echo "<pre>".print_r($prms, true);
									if($f->type=='text') {
										$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
									?>
									<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="text" name="<?php echo $nm; ?>" id="" value="<?php echo $this_val; ?>" class="form-control <?php echo $cls; ?>" aria-describedby="-desc">
										</div>
									</div>
								<?php } elseif($f->type=='url') {
										$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
									?>
									<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="url" name="<?php echo $nm; ?>" id="" value="<?php echo $this_val; ?>" class="form-control <?php echo $cls; ?>" aria-describedby="-desc">
										</div>
									</div>
								<?php }	elseif($f->type=='calendar') {
										$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
										if(empty($this_val))
										{
											$this_val = date("Y-m-d");
										}
									?>
									<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
										<?php 
										$csdt = $nm;
										echo HTMLHelper::calendar($this_val,$csdt, 'date', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10', 'class'=>' validate[\'required\']',)); ?>
										</div>
									</div>
								<?php }
								elseif($f->type=='textarea') {
									$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
									?>

									<div class="control-group"> 
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<textarea placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> name="<?php echo $nm; ?>" id="" class="form-control <?php echo $cls; ?>" rows="3" aria-describedby="-desc"><?php echo $this_val; ?></textarea>
										</div>
									</div>									
									<?php
								}

								elseif($f->type=='editor') {
									$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
									?>

									<div class="control-group"> 
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<?php
											$params = array(
												'name' => $nm, // Name attribute for the textarea
												'id' => 'custom_editor_'.$f->id, // ID attribute for the textarea
												'class' => 'form-control '.$cls, // CSS classes
												'rows' => 10, // Number of rows
												'cols' => 50, // Number of columns
												'width' => '100%', // Width of the editor
												'height' => '300px' // Height of the editor
											);
											
											// Initial content (optional)
											$content = $this_val;
											
											// Render the editor
											echo $editor->display(
												$params['name'],     // Name attribute
												$content,            // Initial content
												$params['width'],    // Width
												$params['height'],   // Height
												$params['rows'],     // Rows
												$params['cols'],     // Columns
												true,                // Buttons
												$params['id'],       // ID attribute
												null,                // Asset
												null,                // Author
												$params              // Additional parameters
											);
											?>
										</div>
									</div>									
									<?php
								}

								elseif($f->type=='image') {
									$this_val = $def;
										if(!empty($custom_data))
										{
											foreach($custom_data as $cdtk)
											{
												if($cdtk->field_id==$f->id)
												{
													if(!empty($cdtk->field_value))
													{
														$this_val = $cdtk->field_value;
													}
												}
											}
										}
									?>

									<div class="control-group"> 
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<joomla-field-media class="field-media-wrapper" type="image" base-path="https://vulcandescaler.joomtechsolutions.com/" root-folder="images" url="/administrator/index.php?option=com_jce&amp;task=plugin.display&amp;element=jform_custom_<?php echo $f->id; ?>&amp;mediatype=images&amp;converted=1&amp;context=427&amp;plugin=browser&amp;standalone=1&amp;f71bde1260e2b6acf8c203cfbcbc35b4=1&amp;client=1&amp;path=" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" modal-title="Change Image" preview="static" preview-container=".field-media-preview" preview-width="200" preview-height="200" supported-extensions="{&quot;images&quot;:&quot;jpg,jpeg,png,gif&quot;,&quot;audios&quot;:&quot;&quot;,&quot;videos&quot;:&quot;&quot;,&quot;documents&quot;:&quot;&quot;}">
												<div class="field-media-preview">
													<div class="preview_empty">No image selected.</div>             
													<div class="preview_img"><img class="media-preview" style="max-width:200px;max-height:200px;" src="" alt="Selected image."></div>        
												</div>
												<div class="input-group">
													<input placeholder="<?php echo $placeholder; ?>" <?php if($req==1) { ?>required <?php } ?> type="text" name="<?php echo $nm; ?>" id="jform_custom_<?php echo $f->id; ?>" value="<?php echo $this_val; ?>" data-wf-converted="1"class="field-media-input  input-medium wf-media-input wf-media-input-active wf-media-input-upload wf-media-input-converted form-control <?php echo $cls; ?>" aria-describedby="-desc">
													<button type="button" class="btn btn-success button-select">Select</button>
													<button type="button" class="btn btn-danger button-clear"><span class="icon-times" aria-hidden="true"></span><span class="visually-hidden">Clear</span></button>
												</div>
											</joomla-field-media>
										</div>
									</div>									
									<?php
								}
							elseif($f->type=='list') {
								$fg = $prms['field_options'];
								$txv = $fg['text'];
								$txw = $fg['value'];
								$this_val = '';
								// echo "<pre>".print_r($custom_data, true);
								if(!empty($custom_data))
								{
									foreach($custom_data as $cdtk)
									{
										if($cdtk->field_id==$f->id)
										{
											// echo "<pre>".print_r($cdtk, true);
											if(!empty($cdtk->field_value))
											{
												$this_val = $cdtk->field_value;
											}
										}
									}
								}
								?>
								<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											<select class="form-select selsm" name="<?php echo $nm; ?>">
												<option value="">Select Value</option>
												<?php for ($i=0; $i < count($txv); $i++) { 
													$ev = $txv[$i];
													$eq = $txw[$i];
												?>
												<option <?php if($this_val==$eq) { ?>selected="selected" <?php } ?> value="<?php echo $eq; ?>"><?php echo $ev; ?></option>
												<?php }?>
											</select>
										</div>
										
								</div>
								<?php
							}
							elseif($f->type=='radio') {
								$fg = $prms['field_options'];
								$txv = $fg['text'];
								$txw = $fg['value'];
								$this_val = '';
								if(!empty($custom_data))
								{
									foreach($custom_data as $cdtk)
									{
										if($cdtk->field_id==$f->id)
										{
											if(!empty($cdtk->field_value))
											{
												$this_val = $cdtk->field_value;
											}
										}
									}
								}
								?>
								<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											
										<?php for ($i=0; $i < count($txv); $i++) {
											$ev = $txv[$i];
											$eq = $txw[$i];
											?>
											<div class="form-check form-check-inline">
											<input type="radio" <?php if($this_val==$eq) {?>checked="checked" <?php }?> name="<?php echo $nm; ?>" id="radio<?php echo $i;?>" value="<?php echo $eq;?>" />
												<label class="form-check-label" for="radio<?php echo $i;?>"><?php echo $ev;?></label>
											</div>
											
													<?php }?>
										</div>
										
								</div>
								<?php
							}
							elseif($f->type=='checkbox') {
								$fg = $prms['field_options'];
								$txv = $fg['text'];
								$txw = $fg['value'];
								$this_val = '';
								if(!empty($custom_data))
								{
									foreach($custom_data as $cdtk)
									{
										if($cdtk->field_id==$f->id)
										{
											if(!empty($cdtk->field_value))
											{
												$this_val = $cdtk->field_value;
											}
										}
									}
								}
								?>
								<div class="control-group">
										<div class="control-label">
											<label id="-lbl" for=""><?php echo $f->title; ?></label>
										</div>
										<div class="controls">
											
										<?php for ($i=0; $i < count($txv); $i++) {
											$ev = $txv[$i];
											$eq = $txw[$i];
											?>
											<div class="form-check form-check-inline">
												<input type="checkbox" <?php if(in_array($eq,explode(',', $this_val
												))) {?>checked="checked" <?php }?> name="<?php echo $nm; ?>" id="checkbox<?php echo $i;?>" value="<?php
												echo $eq;?>" />
												<label class="form-check-label" for="checkbox<?php echo $i;?>"><?php
												echo $ev;?></label>
												</div>

											
											<?php }?>
										</div>
										
								</div>
								<?php
							}
							} } ?>
							</fieldset>
						</div>
					</div>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
				<?php } } ?>
			</fieldset>
			<?php } ?>
		</div>
		</div>
		
	
		 <div class="col-lg-3"> <div class="main-card px-4 py-4 border rounded">
		<?php echo $this->form->renderField('user');?>
		<br/>
			<div class="accordion" id="sidebar">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#TemplateOption" aria-expanded="false" aria-controls="TemplateOption">
        <span style="color: #1b7fcf;"><span class="icon-cog" aria-hidden="true"></span>&nbsp; Template Layout Options</span>
      </button>
    </h2>
    <div id="TemplateOption" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#sidebar">
      <div class="accordion-body">
        <fieldset class="options">				
				<br/>
				<h4><span style="color: #4d4d4d;">Entry Templates:</span></h4>
				<div class="loc_stacked">
				
					<?php
			
				echo $this->form->renderField('admin_entry');	
				echo $this->form->renderField('frontend_entry'); 
 ?>
			 
				</div>
				<br/>
				
				<h4><span style="color: #4d4d4d;">Location Templates:</span></h4>
					<div class="loc_stacked">
					<?php echo $this->form->renderField('result_template'); ?>
					<?php echo $this->form->renderField('card_template'); ?>
					<?php echo $this->form->renderField('details_tempate'); ?>
					
				</div>

			</fieldset>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#StoreHours" aria-expanded="false" aria-controls="StoreHours">
       <span style="color: #1b7fcf;"><span class="icon-cog" aria-hidden="true"></span>&nbsp; Location Hours</span>
      </button>
    </h2>
    <div id="StoreHours" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#sidebar">
      <div class="accordion-body">
        
		
		
		
		
					
		<div class="accordion" id="accStoreHours">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingMonday">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Monday" aria-expanded="false" aria-controls="Monday">Monday</button>
    </h2>
    <div id="Monday" class="accordion-collapse collapse" aria-labelledby="headingMonday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		
										
														<select name="schedule[0][status]" class="form-select mb-2 dropdown-toggle">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
														
														<div class="col-2">From</div>
															
															<div class="col-auto">
																<select name="schedule[0][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[0][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																	
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w1_am" name="schedule[0][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w1_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w1_pm" name="schedule[0][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w1_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[0][to_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[0][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w1_to_am" name="schedule[0][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w1_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w1_to_pm" name="schedule[0][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w1_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
										
		
		
      </div>
    </div>
  </div>
  
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="Tuesday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTuesday" aria-expanded="false" aria-controls="collapseTuesday">
        Tuesday
      </button>
    </h2>
    <div id="collapseTuesday" class="accordion-collapse collapse" aria-labelledby="Tuesday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		<select name="schedule[1][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[1][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[1][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w2_am" name="schedule[1][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w2_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w2_pm" name="schedule[1][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w2_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[1][to_hr]" class="btn btn-primary">
															<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[1][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w2_to_am" name="schedule[1][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w2_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w2_to_pm" name="schedule[1][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w2_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
		
		
      </div>
    </div>
  </div>
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="Wednesday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWednesday" aria-expanded="false" aria-controls="collapseWednesday">Wednesday</button>
    </h2>
    <div id="collapseWednesday" class="accordion-collapse collapse" aria-labelledby="Wednesday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        <select name="schedule[2][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[2][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[2][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w3_am" name="schedule[2][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w3_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w3_pm" name="schedule[2][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w3_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[2][to_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[2][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w3_to_am" name="schedule[2][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w3_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w3_to_pm" name="schedule[2][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w3_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
      </div>
    </div>
  </div>
  
  
  
  
  <div class="accordion-item">
    <h2 class="accordion-header" id="Thursday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThursday" aria-expanded="false" aria-controls="collapseThursday">Thursday</button>
    </h2>
    <div id="collapseThursday" class="accordion-collapse collapse" aria-labelledby="Thursday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		<select name="schedule[3][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[3][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[3][from_min]" class="btn btn-primary">
																	<option selected> </option>
																
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w4_am" name="schedule[3][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w4_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w4_pm" name="schedule[3][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w4_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[3][to_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[3][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w4_to_am" name="schedule[3][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w4_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w4_to_pm" name="schedule[3][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w4_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
		
      </div>
    </div>
  </div>
   
  
  
    <div class="accordion-item">
    <h2 class="accordion-header" id="Friday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFriday" aria-expanded="false" aria-controls="collapseFriday">Friday</button>
    </h2>
    <div id="collapseFriday" class="accordion-collapse collapse" aria-labelledby="Friday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		<select name="schedule[4][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[4][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[4][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w5_am" name="schedule[4][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w5_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w5_pm" name="schedule[4][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w5_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[4][to_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[4][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w5_to_am" name="schedule[4][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w5_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w5_to_pm" name="schedule[4][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w5_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
		
      </div>
    </div>
  </div>
   
   
   
     <div class="accordion-item">
    <h2 class="accordion-header" id="Saturday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSaturday" aria-expanded="false" aria-controls="collapseSaturday">Saturday</button>
    </h2>
    <div id="collapseSaturday" class="accordion-collapse collapse" aria-labelledby="Saturday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		<select name="schedule[5][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[5][from_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[5][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w6_am" name="schedule[5][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w6_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w6_pm" name="schedule[5][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w6_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[5][to_hr]" class="btn btn-primary">
															<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[5][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w6_to_am" name="schedule[5][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w6_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w6_to_pm" name="schedule[5][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w6_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
		
      </div>
    </div>
  </div>
   
   
   
     <div class="accordion-item">
    <h2 class="accordion-header" id="Sunday">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSunday" aria-expanded="false" aria-controls="collapseSunday">Sunday</button>
    </h2>
    <div id="collapseSunday" class="accordion-collapse collapse" aria-labelledby="Sunday" data-bs-parent="#accStoreHours">
      <div class="accordion-body">
        
		<select name="schedule[6][status]" class="form-select mb-2 sel-status">
															<option value="">Select</option>
															<option value="1">Open</option>
															<option value="2">Closed</option>
															<option value="3">Closed All Day</option>
														</select>
														<div class="row g-2 align-items-center">
															<div class="col-2">From</div>
															<div class="col-auto">
																<select name="schedule[6][from_hr]" class="btn btn-primary">
																	<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[6][from_min]" class="btn btn-primary">
																	<option selected> </option>
																	
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w7_am" name="schedule[6][fromt]" value="am" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w7_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w7_pm" name="schedule[6][fromt]" value="pm" required="" aria-invalid="false">
																	<label for="jform_w7_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
														<div class="row g-2 align-items-center mt-2">
															<div class="col-2">To</div>
															<div class="col-auto">
																<select name="schedule[6][to_hr]" class="btn btn-primary">
																<option selected> </option>
																	<option>1</option>
																	<option>2</option>
																	<option>3</option>
																	<option>4</option>
																	<option>5</option>
																	<option>6</option>
																	<option>7</option>
																	<option>8</option>
																	<option>9</option>
																	<option>10</option>
																	<option>11</option>
																	<option>12</option>
																</select>
															</div>
															<div class="col-auto">:</div>
															<div class="col-auto">
																<select name="schedule[6][to_min]" class="btn btn-primary">
																<option selected> </option>
																	<option>00</option>
																	<option>05</option>
																	<option>10</option>
																	<option>15</option>
																	<option>20</option>
																	<option>25</option>
																	<option>30</option>
																	<option>35</option>
																	<option>40</option>
																	<option>45</option>
																	<option>50</option>
																	<option>55</option>
																</select>
															</div>
															<div class="col-2">
																<div class="btn-group w-50 btn-sm">
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w7_to_am" name="schedule[6][tot]" value="am" required="" aria-invalid="false">
																	<label for="jform_w7_to_am" class="btn btn-outline-success">AM</label>
																	<input class="btn-check valid form-control-success" type="radio" id="jform_w7_to_pm" name="schedule[6][tot]" value="pm" checked="checked" required="" aria-invalid="false">
																	<label for="jform_w7_to_pm" class="btn btn-outline-success">PM</label>
																</div>
															</div>
														</div>
		
      </div>
    </div>
  </div>
   
  
  
</div>		
		
	
      </div>
    </div>
  </div>
 
</div>			
			
		</div>
	</div>
	</div>

	
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />


	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
<style>
select.form-select.selsm {
    padding: 8px 20px;
}
</style>
<?php
if(isset($this->item->map))
{ $ctd = explode(',',$this->item->map);  ?>
<script>
var valuesToSelect2 = <?php echo json_encode($ctd); ?>;
jQuery('.mpt').val(valuesToSelect2);
</script>
<?php } ?>
<?php if(isset($this->item->catid))
{ $ctp = explode(',',$this->item->catid); ?>
<script>
	 var valuesToSelect = <?php echo json_encode($ctp); ?>;
	jQuery('select#jform_catid').val(valuesToSelect);
</script>
<?php } ?>



<style>
.control-group {
	width: auto;
}

.control-label{
    display: flex !important;
    flex-direction: column !important;
	width: auto;
}

.geo_box button {
    float: right;
}

.geo_box h3 {
    overflow: hidden;
    display: block;
    line-height: 50px;
}
.loc_stacked .control-group {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-items: baseline;
}

.loc_stacked .control-group .control-label {
    max-width: 100px;
}
.field-media-wrapper {
    margin-bottom: 15px;
}
.image-preview {
    margin-top: 10px;
}
.image-preview img {
    max-width: 100%;
    height: auto;
}
.modal-dialog {
    max-width: 70%;
}
.modal-content {
    height: 80vh;
}
.modal-body {
    height: calc(100% - 56px);
    padding: 0;
}
.modal-body iframe {
    height: 100%;
}

</style>