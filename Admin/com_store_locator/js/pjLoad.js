(function (window, undefined){
	"use strict";

	const urlParams = new URLSearchParams(window.location.search);
	const id = urlParams.get('id');
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		routes = [];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	var itemsPerPage = 3;
	var currentPage = 1;

	// Initialize pagination
	function initPagination(totalPages) {
		// Create pagination HTML
		var paginationHtml = '<ul class="pagination">';
		paginationHtml += '<li><a class="prev">«</a></li>';
	
		// Show ellipsis and last page if more than 3 pages
		if (totalPages > 3) {
			for (var i = 1; i <= 3; i++) {
				paginationHtml += '<li><a class="page-number">' + i + '</a></li>';
			}
			paginationHtml += '<li><span class="ellipsis">...</span></li>';
			paginationHtml += '<li><a class="page-number">' + totalPages + '</a></li>';
		} else {
			for (var i = 1; i <= totalPages; i++) {
				paginationHtml += '<li><a class="page-number">' + i + '</a></li>';
			}
		}
	
		paginationHtml += '<li><a class="next">»</a></li>';
		paginationHtml += '</ul>';
	
		$("#pagination").html(paginationHtml);
	
		// Show first page
		showPage(1, totalPages);
	}
	
	function showPage(page, totalPages) {
		var $list = $(".list-group");
		var $items = $list.find("li");
		var itemsPerPage = 3;
	
		// Hide all items
		$items.addClass('hide');
	
		// Show items for current page
		var startIndex = (page - 1) * itemsPerPage;
		var endIndex = startIndex + itemsPerPage;
	
		$items.slice(startIndex, endIndex).removeClass('hide');
		currentPage = page;
		// Update pagination display
		updatePaginationDisplay(page, totalPages);
	
		// Enable/disable prev/next buttons
		$('.prev').parent().toggleClass('disabled', page === 1);
		$('.next').parent().toggleClass('disabled', page === totalPages);
	}
	
	function updatePaginationDisplay(currentPage, totalPages) {
		var $pagination = $('.pagination');
		var $pageNumbers = $pagination.find('.page-number');
		var $ellipsis = $pagination.find('.ellipsis');
	
		if (totalPages <= 3) {
			$pageNumbers.each(function(index) {
				$(this).text(index + 1).parent().show();
			});
			$ellipsis.parent().hide();
		} else {
			if (currentPage <= 2) {
				$pageNumbers.eq(0).text(1);
				$pageNumbers.eq(1).text(2);
				$pageNumbers.eq(2).text(3);
				$ellipsis.parent().show();
				$pageNumbers.eq(3).text(totalPages);
			} else if (currentPage >= totalPages - 1) {
				$pageNumbers.eq(0).text(1);
				$ellipsis.parent().show();
				$pageNumbers.eq(1).text(totalPages - 2);
				$pageNumbers.eq(2).text(totalPages - 1);
				$pageNumbers.eq(3).text(totalPages);
			} else {
				$pageNumbers.eq(0).text(1);
				$ellipsis.parent().show();
				$pageNumbers.eq(1).text(currentPage - 1);
				$pageNumbers.eq(2).text(currentPage);
				$pageNumbers.eq(3).text(totalPages);
			}
		}
	
		$pageNumbers.parent().removeClass('active');
		$pageNumbers.filter(function() {
			return $(this).text() == currentPage;
		}).parent().addClass('active');
	}


	
	// Event handlers
	$(document).on('click', '.page-number', function() {
		var $list = $(".list-group");
		var $items = $list.find("li");
		var itemsPerPage = 3;
		var totalItems = $items.length;
		var totalPages = Math.ceil(totalItems / itemsPerPage);
		var page = parseInt($(this).text());
		showPage(page, totalPages);
	});
	
	$(document).on('click', '.prev', function() {
		var $list = $(".list-group");
		var $items = $list.find("li");
		var itemsPerPage = 3;
		var totalItems = $items.length;
		var totalPages = Math.ceil(totalItems / itemsPerPage);
		if (currentPage > 1) {
			showPage(currentPage - 1, totalPages);
		}
	});
	
	$(document).on('click', '.next', function() {
		var $list = $(".list-group");
		var $items = $list.find("li");
		var itemsPerPage = 3;
		var totalItems = $items.length;
		var totalPages = Math.ceil(totalItems / itemsPerPage);
		if (currentPage < totalPages) {
			showPage(currentPage + 1, totalPages);
		}
	});
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function StoreLocator(opts) {
		if (!(this instanceof StoreLocator)) {
			return new StoreLocator(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	StoreLocator.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	StoreLocator.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	StoreLocator.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.opts = {};
			
			this.map;
			this.storeData;
			this.directionsDisplay = new google.maps.DirectionsRenderer();
			this.directionsService = new google.maps.DirectionsService();
			this.geocoder = new google.maps.Geocoder();
			this.markersArray = [];
			this.address = null;
			
			this.$search_result = null;
			this.$current_location = null;
			this.$search_addresses = null;
			this.$search_directions = null;
			this.$email_menu = null;
			this.$directions_html = null;
			this.currentLocationIndex = null;
						
			return this;
		},
		
		disableButtons: function () {
			this.$container.find(".btn").each(function (i, el) {
				pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");
		},
		
		showOverlays: function () {
			var self = this;
			if (self.markersArray) {
				for (var i in self.markersArray) {
					if (self.markersArray.hasOwnProperty(i)) {
						self.markersArray[i].setMap(self.map);
					}
				}
			}
		},
		
		deleteOverlays: function () {
			var self = this;
			if (self.markersArray) {
				for (var i in self.markersArray) {
					if (self.markersArray.hasOwnProperty(i)) {
						self.markersArray[i].setMap(null);
					}
				}
				self.markersArray.length = 0;
			}
		},
		addMarker: function (location, title, category_marker, content, distance) {
			var self = this;
			if(category_marker != null && category_marker != '')
			{
				var marker = new google.maps.Marker({
					position: location,
					icon: category_marker,
					map: self.map,
					title: title
				});
			}else{
				var marker = new google.maps.Marker({
					position: location,
					map: self.map,
					title: title
				});
			}	
			
			if (content.length > 0 && content != "") {
				marker.infoWindow = new google.maps.InfoWindow({
					content: content
				});
				google.maps.event.addListener(marker, "click", function() {
					for (var i = self.markersArray.length - 1; i >= 0; i--) {
						self.markersArray[i].infoWindow.close();
					}
					this.infoWindow.open(self.map, marker);
				});
			}  
			self.markersArray.push(marker);
			return self.markersArray.length - 1;
		},
		resultMarkers: function (data) {
			var	self = this,
				latlng, title, category_marker, distance, i, LatLngList = [], store_list;
				
			self.storeData = data;
			self.deleteOverlays();
			if (data.length > 0) {
				var cnt = data.length - 1;
				for (var k in data) 
				{
					if (data.hasOwnProperty(k) && k < cnt) {
						latlng = new google.maps.LatLng(data[k].lat, data[k].lng);
						LatLngList.push(latlng);
						title = data[k].name;
						category_marker = data[k].marker;
						distance = data[k].distance;
						
						i = self.addMarker(latlng, title, category_marker, data[k].marker_content, distance);
					}			  
				}
				store_list = data[cnt].store_list;
				self.showOverlays();
				
				var bounds = new google.maps.LatLngBounds();
				for (var j = 0, len = LatLngList.length; j < len; j++) {
					bounds.extend(LatLngList[j]);
				}
				
				self.$search_directions.style.display = 'none';
				self.$search_addresses.innerHTML = store_list;
				self.$search_addresses.style.display = 'block';
				
				var $list = $(".list-group");
				var $items = $list.find("li");
				var totalItems = $items.length;
				var totalPages = Math.ceil(totalItems / itemsPerPage);
				var currentPage = 1;
				initPagination(totalPages);
				
				pjQ.$('.pjSlResult').on('click', function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}

					var loc_id = pjQ.$(this).attr('data-id');
					var card = pjQ.$(this).attr('data-card');
					pjQ.$.get("index.php?option=com_store_locator&task=pjgetcarddata&controller=Locatormap&format=raw&id="+loc_id+"&card="+card).done(function (data) {
						data = pjQ.$.parseJSON(data);
						const infoWindow = document.getElementById('info-window');
						const infodata = document.getElementById('info-content');
						infodata.innerHTML = `<div class="store-info">${data.data}</div>`;
						infoWindow.style.display = 'block'; // Show the info window
						// self.triggerMap();
					})
					/*
					if(!pjQ.$('.pjSlMapInfoWindow').is(":visible"))
					{
						self.currentLocationIndex = pjQ.$(this).attr('lang');
						google.maps.event.trigger(self.markersArray[pjQ.$(this).attr('lang')], 'click');
					}else{
						if(pjQ.$(this).attr('lang') != self.currentLocationIndex)
						{
							self.currentLocationIndex = pjQ.$(this).attr('lang');
							google.maps.event.trigger(self.markersArray[pjQ.$(this).attr('lang')], 'click');
						}
					}*/
				});
			} else {
				self.emptyResults();
			}
			
		},
		getMarkers: function (url, show_clear_filters){
			var self = this;
			var params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get(url, params).done(function (data) {
				self.resultMarkers(data);	
				if(show_clear_filters == true)
				{
					pjQ.$('.pjSlClearFilters').css('display', 'block');
				}else{
					pjQ.$('.pjSlClearFilters').css('display', 'none');
				}
			}).fail(function () {
				
			});
		},
		loadDirections: function(url, i)
		{
			var self = this;
			var params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get(url, params).done(function (json) {
				if(json.code == '200')
				{
					self.directionsDisplay.setMap(null);
					self.directionsDisplay.setMap(self.map);
					self.directionsDisplay.setPanel(document.getElementById("stl_search_directions_panel"));
				
					var start =new google.maps.LatLng(parseFloat(json.lat), parseFloat(json.lng));
					var end = new google.maps.LatLng(parseFloat(self.storeData[i].lat), parseFloat(self.storeData[i].lng));
					
					var request = {
									origin: start,
									destination: end,
									travelMode: google.maps.TravelMode.DRIVING
							};
					self.directionsService.route(request, function(result, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							self.directionsDisplay.setDirections(result);
						}
					});
					self.$search_result.style.display = "none";
					self.$search_directions.style.display = "block";
				}	
			}).fail(function () {
				
			});
		},
		getCurrentLocations: function() {
			var self = this, 
				search_form = document.forms[self.opts.search_form_name], 
				address = search_form['address'].value, 
				radius = search_form['radius'].value;
		
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		
					self.geocoder.geocode({'latLng': latlng}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							// Extract city and country
							var city = '', country = '';
							for (var i = 0; i < results[0].address_components.length; i++) {
								for (var b = 0; b < results[0].address_components[i].types.length; b++) {
									if (results[0].address_components[i].types[b] == "administrative_area_level_1") {
										city = results[0].address_components[i].long_name;
									}
									if (results[0].address_components[i].types[b] == "country") {
										country = results[0].address_components[i].short_name;
									}
								}
							}
							address = city + ', ' + country;
		
							if (address != self.address) {
								var stlOptions = {
									zoom: self.opts.zoom_level,
									center: {
										"lat": position.coords.latitude,
										"lng": position.coords.longitude
									},
									mapTypeId: google.maps.MapTypeId.ROADMAP
								};
								if (styles != '') {
									stlOptions.styles = styles;
								}
								self.map = new google.maps.Map(document.getElementById("stl_store_canvas"), stlOptions);
							}
		
							var ajax_url = self.opts.generate_xml_url;
							if (self.opts.session_id != '') {
								ajax_url += '&session_id=' + self.opts.session_id;
							}
		
							var show_clear_filters = false;
							var category_id = null;
							var filter_id = null;
		
							// Collect filter parameters
							const filterParams = $('.singlfilter')
								.map(function() {
									const $select = $(this);
									const filterId = $select.data('id');
									const filterValue = $select.find('select').val();
		
									return filterValue ? { id: filterId, value: filterValue } : null;
								})
								.get()
								.filter(Boolean);
		
							// Collect category parameters
							var category_id_arr = [];
							if (self.opts.use_categories == 'Yes') {
								pjQ.$('.pjSlCategoryCheckbox').each(function() {
									if (pjQ.$(this).is(':checked')) {
										category_id_arr.push(pjQ.$(this).val());
									}
								});
								if (category_id_arr.length > 0) {
									category_id = category_id_arr.join(",");
									show_clear_filters = true;
								}
							}
		
							// Construct query parameters
							const queryParams = [
								ajax_url,
								'&lat=', results[0].geometry.location.lat(),
								'&lng=', results[0].geometry.location.lng(),
								'&radius=', radius,
								"&distance=", self.opts.distance
							];
		
							// Add category parameter if exists
							if (category_id) {
								queryParams.push("&category_id=", category_id);
							}
		
							// Add filter parameters
							filterParams.forEach(filter => {
								queryParams.push(`&${filter.id}=${filter.value}`);
								show_clear_filters = true;
							});
		
							// Get markers with constructed parameters
							self.getMarkers(queryParams.join(""), show_clear_filters);
		
							search_form['address'].value = address;
							self.address = address;
						} else {
							self.notFoundAddress();
							self.address = address;
						}
					});
				});
			} else {
				alert(self.opts.label_geo_not_supported);
			}
		},
		searchLocations: function() { 
			var self = this, 
				search_form = document.forms[self.opts.search_form_name], 
				address = search_form['address'].value; 
		
			self.geocoder.geocode( { 'address': address}, function(results, status) { 
				if (status == google.maps.GeocoderStatus.OK) { 
					var radius = search_form['radius'].value; 
					if(address != self.address) 
					{ 
						var stlOptions = { 
							zoom: self.opts.zoom_level, 
							center: results[0].geometry.location, 
							mapTypeId: google.maps.MapTypeId.ROADMAP 
						}; 
						if(styles!='') 
						{ 
							stlOptions.styles = styles; 
						} 
						self.map = new google.maps.Map(document.getElementById("stl_store_canvas"), stlOptions); 
					} 
					var ajax_url = self.opts.generate_xml_url; 
					if(self.opts.session_id != '') 
					{ 
						ajax_url += '&session_id=' + self.opts.session_id; 
					} 
					var show_clear_filters = false; 
					var category_id = null; 
					var filter_id = null; 
					var category_id_arr = []; 
					var filter_id_arr = []; 
		
					// Collect filter parameters
					const filterParams = $('.singlfilter')
						.map(function() { 
							const $select = $(this); 
							const filterId = $select.data('id'); 
							const filterValue = $select.find('select').val(); 
		
							return filterValue ? { id: filterId, value: filterValue } : null; 
						}) 
						.get() 
						.filter(Boolean);
		
					// Collect category parameters
					if(self.opts.use_categories == 'Yes') 
					{ 
						pjQ.$('.pjSlCategoryCheckbox').each(function(){ 
							if(pjQ.$(this).is(':checked')) 
							{ 
								category_id_arr.push(pjQ.$(this).val()); 
							} 
						}); 
						if(category_id_arr.length > 0) 
						{ 
							category_id = category_id_arr.join(","); 
							show_clear_filters = true; 
						} 
					}
		
					// Construct query parameters
					const queryParams = [
						ajax_url,
						'&lat=', results[0].geometry.location.lat(),
						'&lng=', results[0].geometry.location.lng(),
						'&radius=', radius,
						"&distance=", self.opts.distance
					];
		
					// Add category parameter if exists
					if (category_id) {
						queryParams.push("&category_id=", category_id);
					}
		
					// Add filter parameters
					filterParams.forEach(filter => {
						queryParams.push(`&${filter.id}=${filter.value}`);
						show_clear_filters = true;
					});
		
					// Get markers with constructed parameters
					self.getMarkers(queryParams.join(""), show_clear_filters);
		
					self.address = address; 
				} else { 
					self.notFoundAddress(); 
					self.address = address; 
				} 
			}); 
		},
		emptyResults: function () {
			var self = this;
			this.$search_addresses.innerHTML = self.opts.label_not_found;
			this.$search_addresses.style.display = 'block';
			this.$search_directions.style.display = 'none';
			this.$search_directions.innerHTML = '';
		},
		notFoundAddress: function () {
			var self = this;
			this.$search_addresses.innerHTML = self.opts.label_address_not_found;
			this.$search_addresses.style.display = 'block';
			this.$search_directions.style.display = 'none';
			this.$search_directions.innerHTML = '';
		},
		triggerMap: function(){
			var self = this;
			
			self.$search_result = document.getElementById("stl_search_result");
			self.$current_location = document.getElementById("stl_current_location");
			self.$search_addresses = document.getElementById("stl_search_addresses");
			self.$search_directions = document.getElementById("stl_search_directions");
			self.$email_menu = document.getElementById("stl_email_menu");
			self.$directions_html = document.getElementById("stl_directions_html");
						
			self.geocoder.geocode( { 'address': self.opts.default_address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var stlOptions = {
						zoom: self.opts.zoom_level,
						center: results[0].geometry.location,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					if(styles!='')
					{
						stlOptions.styles = styles;
					}
					self.map = new google.maps.Map(document.getElementById("stl_store_canvas"), stlOptions);
					self.address = self.opts.default_address;
					self.searchLocations();
				} else {
					self.notFoundAddress();
				}
			});
			
			if (pjQ.$('.pjSlCheckbox, .pjSlRadio').length) {
				var checkedClass = 'pjSlCustomInputChecked';
				var disabledClass = 'pjSlCustomInputDisabled';
				var inputSelector = '.pjSlCustomCheckbox input, .pjSlCustomRadio input';

				pjQ.$(inputSelector).each(function() {
					var input = this;

					pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
				})
				.on('change', function() {
					var input = this;

					if(input.type === 'radio') {
						var name = input.name;
						pjQ.$(input.ownerDocument).find('[name=' + name + ']').each(function() {
							var radioInput = this;
							pjQ.$(radioInput).parent().toggleClass(checkedClass, radioInput.checked);
						});
					} else {
						pjQ.$(input).parent().toggleClass(checkedClass, input.checked);
					};
				})
				.on('disable', function() {
					var input = this;

					input.disabled = true;
					pjQ.$(input).parent().addClass(disabledClass);
				})
				.on('enable', function() {
					var input = this;

					input.disabled = false;
					pjQ.$(input).parent().removeClass(disabledClass);
				});
			};
		},
		getLocations: function(){
			var self = this;
			var params = {};
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?option=com_store_locator&task=pjActionGetLocations&controller=Locatormap&format=raw&id="+id].join(""), params).done(function (data) {
				self.$container.html(data);
				self.searchLocations();
				self.triggerMap();
				
			}).fail(function () {
				
			});
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjSlContainer_" + self.opts.index);
			
			self.$container = pjQ.$(self.container);
			
			self.getLocations();
			
			this.$container.on("click.sl", ".stl-store-title", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.stl-store-item').removeClass('stl-item-focus');
				pjQ.$(this).parent().parent().addClass('stl-item-focus');
				google.maps.event.trigger(self.markersArray[pjQ.$(this).attr('lang')], 'click');
				return false;
			}).on("click.sl", ".stl-store-image", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.stl-store-item').removeClass('stl-item-focus');
				pjQ.$(this).parent().addClass('stl-item-focus');
				google.maps.event.trigger(self.markersArray[pjQ.$(this).attr('lang')], 'click');
				return false;
			}).on("click.sl", ".stl-full-address", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				
				pjQ.$('#stl_hidden_container_' + lang).css('display', 'none');
				pjQ.$('#stl_store_address_' + lang).css('display', 'block');
				pjQ.$('#stl_close_address_' + lang).css('display', 'block');
				
				google.maps.event.trigger(self.markersArray[lang], 'click');
				return false;
			}).on("click.sl", ".stl-close-address", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				pjQ.$(this).css('display', 'none');
				
				pjQ.$('#stl_store_address_' + lang).css('display', 'none');
				pjQ.$('#stl_hidden_container_' + lang).css('display', 'block');
				return false;
			}).on("click.sl", ".stl-directions", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				
				pjQ.$('#stl_hidden_container_' + lang).css('display', 'none');
				pjQ.$('#stl_direction_box_' + lang).css('display', 'block');
				return false;
			}).on("click.sl", ".stl-close-direction", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				pjQ.$('#sendByEmail').collapse('show');
				pjQ.$('#stl_direction_text_' + lang).val('');
				pjQ.$('#stl_direction_box_' + lang).css('display', 'none');
				pjQ.$('#stl_hidden_container_' + lang).css('display', 'block');
				return false;
			}).on("click.sl", ".stl-direction-text", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				if(e.keyCode == 13)
				{
					var url = self.opts.get_latlng_url + '&address=' + pjQ.$(this).val();
					if(self.opts.session_id != '')
					{
						url += "&session_id=" + self.opts.session_id;
					}
					self.loadDirections(url, lang);
				}
				return false;
			}).on("click.sl", ".stl-go-button", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var lang = pjQ.$(this).attr('lang');
				if (typeof lang !== typeof undefined && lang !== false) {
					var url = self.opts.get_latlng_url + '&address=' + pjQ.$("#stl_direction_text_"+ lang).val();
					if(self.opts.session_id != '')
					{
						url += "&session_id=" + self.opts.session_id;
					}
					self.loadDirections(url, lang);
				}
				return false;
			}).on("click.sl", ".stl-directions-close", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$search_directions.style.display = "none";
				self.$search_result.style.display = "block";
				self.directionsDisplay.setMap(null);
				return false;
			}).on("click.sl", "#stl_email_menu", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$directions_html.value = document.getElementById("stl_search_directions_panel").innerHTML;
				return false;
			}).on("click.sl", "#stl_send_email", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $form = pjQ.$('#stl_send_email_form');
				var $email = $form.find('input[name="stl_email_text"]');
				var $captcha = $form.find('input[name="captcha"]');
				
				if($email.val() != '' && $captcha.val() != '')
				{
					var $this = pjQ.$(this);
					$this.prop('disabled', true);
					self.$directions_html.value = document.getElementById("stl_search_directions_panel").innerHTML;
					var post_url = self.opts.send_email_url;
					if(self.opts.session_id != '')
					{
						post_url += "&session_id=" + self.opts.session_id;
					}
					pjQ.$.post(post_url, $form.serialize()).done(function (json) {
						if(json.code == 200)
						{
							$email.val("");
							$captcha.val("");
							
							var rand = Math.floor((Math.random()*999999)+1); 
							if(self.opts.session_id != '')
							{
								pjQ.$('#pjSlCapthaImg').attr("src", self.opts.folder + "index.php?controller=pjFront&action=pjActionCaptcha&rand=" + rand + "&session_id=" + self.opts.session_id);
							}else{
								pjQ.$('#pjSlCapthaImg').attr("src", self.opts.folder + "index.php?controller=pjFront&action=pjActionCaptcha&rand=" + rand);
							}
							
						}else if(json.code == 100){
							alert(self.opts.label_empty_email);
						}else if(json.code == 300){
							alert(self.opts.label_invalid_email);
						}else if(json.code == 400){
							alert(self.opts.label_captcha_incorrect);
						}
						$this.prop('disabled', false);
					}).fail(function () {
						
					});
				}
				
				return false;
			}).on("click.sl", "input[name='stl_search_form_search']", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.searchLocations();
				return false;
			}).on("click.sl", ".pjSlSearchIcon", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.searchLocations();
				return false;
			}).on("change.sl", "select[name='radius']", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.searchLocations();
				return false;
			}).on("click.sl", "#stl_current_location", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.getCurrentLocations();
				return false;
			}).on("click.sl", ".pjSlClearFilters", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.singlfilter').each(function(){
					pjQ.$(this).find('select').val('');
				});
				pjQ.$('.singlfilter').each(function(){
					pjQ.$(this).find('select').val('');
				});
				pjQ.$('.pjSlBtnFilterBy').trigger('click');
				self.searchLocations();
				return false;
			});
		}
	};
	
	window.StoreLocator = StoreLocator;	
})(window);