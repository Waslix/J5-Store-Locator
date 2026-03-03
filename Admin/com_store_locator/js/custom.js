jQuery(document).ready(function($){
    $('.find_coords').click(function(e){
        e.preventDefault(); // Prevent default button action
        
        $.ajax({
            url: 'index.php?option=com_store_locator&task=your_task&controller=Locatorlocation&format=raw',
            type: 'POST',
            dataType: 'json',
            data: {
                'street': $('textarea#jform_street').val(),
                'city': $('input#jform_city').val(),
                'state': $('input#jform_user_state').val(),
                'country': $('select#jform_country').find('option:selected').text(),
                'zip': $('input#jform_zip_code').val(),
                [Joomla.getOptions('csrf.token')]: '1'
            },
            beforeSend: function() {
                // Show loading indicator if needed
            },
            success: function(response) {
                if(response.code == 200) {
                    var dat = response.data;
                    $('input#jform_latitude').val(dat.lat);
                    $('input#jform_longitude').val(dat.lng);
                } else {
                    // Handle error
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle ajax errors
                console.error('Ajax Error:', error);
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Get all elements with class 'ftag'
    var fieldTags = document.querySelectorAll('.ftag');
    
    fieldTags.forEach(function(tag) {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the data-tag attribute value
            var tagValue = this.getAttribute('data-tag');
            
            // Get the editor instance
            var editor = Joomla.editors.instances['jform_template'];
            
            // Insert the tag at cursor position
            if (editor) {
                // Format the tag with curly braces
                var formattedTag = '{' + tagValue + '}';
                
                // Insert the content
                editor.replaceSelection(formattedTag);
            }
        });
    });
});


// Add icons to submenu
jQuery(document).ready(function() {

	var getUrlParameter = function getUrlParameter(sParam, url) {
		var sPageURL = url,
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
		return false;
	};

	jQuery('.item-level-3').find('a').each(function() {
		var view = getUrlParameter('option', jQuery(this).attr('href'));
		view = String(view);
		if(view.includes('com_store_locator')) {
			var className = view.replace('com_store_locator', '');
			jQuery(this).addClass('ph-submenu ph-submenu-' + className);
		}

		


		if (jQuery(this).attr('href') == 'index.php?option=com_store_locator') {
			jQuery(this).addClass('ph-submenu ph-submenu-cp');
		}
	});

	const container = jQuery('.main-nav-container');
	if (container) {
		const menu = container.children('ul');
		if (menu) {
			const submenu = menu.find('a[href="index.php?option=com_store_locator"] + ul');
			if (submenu) {
				const phSubmenu = submenu.clone();
				phSubmenu.attr('class', 'nav flex-column main-nav metismenu child-open ph-menu');
				phSubmenu.find('li').each(function() {
					jQuery(this).removeClass('item-level-3').addClass('item-level-1').children().addClass('ps-2');
				});

				phSubmenu.prepend(jQuery('<li class="item item-level-1"><a href="#" class="no-dropdown ph-submenu ph-submenu-back"><span class="icon-puzzle-piece icon-fw" aria-hidden="true"></span><span class="sidebar-item-title"><span class="icon-angle-left" aria-hidden="true"></span>Back to main menu</span></a></li>'));
				const phMenuSwitch = phSubmenu.find('.ph-submenu-back');
				phMenuSwitch.click(function(e) {
					e.preventDefault();
					menu.css('display', 'block');
					phSubmenu.css('display', 'none');
				});

				menu.prepend(jQuery('<li class="item item-level-1"><a href="#" class="no-dropdown ph-submenu ph-submenu-phocacart"><span class="icon-puzzle-piece icon-fw" aria-hidden="true"></span><span class="sidebar-item-title"><span class="icon-angle-left" aria-hidden="true"></span>Back to main menu</span></a></li>'));
				const menuSwitch = menu.find('.ph-submenu-phocacart');
				menuSwitch.click(function(e) {
					e.preventDefault();
					phSubmenu.css('display', 'block');
					menu.css('display', 'none');
				});

				container.append(phSubmenu);
				menu.css('display', 'none');
			}
		}
	}
})

jQuery(document).on('click', '.add_criteria', function(){
	var jqbx = jQuery('.single_box').clone();
	jqbx.find('select').val('');
	jQuery('.field-criteria').append(jqbx);
	return false;
});

jQuery(document).on('click', '.add_criteria_arr', function(){
	var jqbx = jQuery('.single_box_arr').first().clone();
	jqbx.find('select').val('');
	jQuery('.field-arrange').append(jqbx);
	return false;
});

$(document).on('change', '.fsel', function(e){
	e.preventDefault(); 
	var vk = $(this).val();
	var ths = $(this);
	if(vk!='')
	{
		$.ajax({
			url: 'index.php?option=com_store_locator&task=get_field_criteria&controller=Locatorlocation&format=raw',
			type: 'POST',
			dataType: 'json',
			data: {
				'field': vk,
				[Joomla.getOptions('csrf.token')]: '1'
			},
			beforeSend: function() {
				// Show loading indicator if needed
			},
			success: function(response) {
				if(response.success)
				{
					var arr = response.data;
					var typ = response.type;
					// console.log(typ);
					if(typ=='options')
					{
						var htm = '<select class="form-select cndval" name="condition_opt[]"><option value="">Select Value</option>';
						$.each(arr, function(key, value) {
							htm += '<option value="'+key+'">'+value+'</option>';
							// console.log("Key: " + key + ", Value: " + value);
						});
						htm += '</select>';
						$(ths).parent().find('.dynamic_field_choice').html(htm);
						$(ths).parent().find('.cdtype option[value="1"]').hide();
						$(ths).parent().find('.cdtype option[value="2"]').hide();
					 }
					 else
					 {
						var htm = '<input name="condition_opt[]" class="form-control cndval"/>';
						$(ths).parent().find('.dynamic_field_choice').html(htm);
						$(ths).parent().find('.cdtype option[value="1"]').show();
						$(ths).parent().find('.cdtype option[value="2"]').show();
					 }
				}
				// if(response.code == 200) {
				// 	var dat = response.data;
				// 	$('input#jform_latitude').val(dat.lat);
				// 	$('input#jform_longitude').val(dat.lng);
				// } else {
				// 	// Handle error
				// 	alert(response.message);
				// }
			},
			error: function(xhr, status, error) {
				// Handle ajax errors
				console.error('Ajax Error:', error);
			}
		});
	}
	
});


function get_location_lists() {
	$.ajax({
		url: 'index.php?option=com_store_locator&task=get_location_lists&controller=Locatorlocation&format=raw',
		type: 'POST',
		dataType: 'json',
		data: {
			[Joomla.getOptions('csrf.token')]: '1'
		},
		success: function(response) {
			if(response.success)
			{
				$('ul.nav.flex-column.main-nav.metismenu.child-open.ph-menu').append(response.data);
			}
			else
			{
				alert(response.message);
			}
		}
	});
}
$(document).ready(function(){
	$('a.no-dropdown.ph-submenu.ph-submenu-back').parent().after('<li class="item item-level-2"><a class="no-dropdown ps-2" href="index.php?option=com_store_locator&view=adminlocationentries"><span class="sidebar-item-title">Admin Location Listings</span></a></li>');
	get_location_lists();
});