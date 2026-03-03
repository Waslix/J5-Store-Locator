jQuery(document).ready(function($) {
    const searchButton = $('#search-button');
    const loadingIndicator = $('#search-loading');
    
    searchButton.on('click', function() {
        const location = $('#location-search').val();
        const radius = $('#search-radius').val();
        
        if (!location) {
            alert('Please enter a location or ZIP code');
            return;
        }
        
        // Show loading indicator
        loadingIndicator.removeClass('d-none');
        searchButton.prop('disabled', true);
        
        // Perform AJAX search
        $.ajax({
            url: 'index.php?option=com_store_locator&task=your_task&controller=Locatormap&format=raw',
            method: 'POST',
            data: {
                location: location,
                radius: radius,
                format: 'json'
            },
            success: function(response) {
                if (response.success) {
                    clearMarkers();
                    updateStoreList(response.stores);
                    
                    response.stores.forEach(store => {
                        addMarker(store);
                    });
                    
                    centerMapOnResults(response.stores);
                } else {
                    alert('Error performing search. Please try again.');
                }
            },
            error: function() {
                alert('Error connecting to server. Please try again.');
            },
            complete: function() {
                // Hide loading indicator
                loadingIndicator.addClass('d-none');
                searchButton.prop('disabled', false);
            }
        });
    });
    
    // Enable search on Enter key
    $('#location-search').on('keypress', function(e) {
        if (e.which === 13) {
            searchButton.click();
        }
    });
    $(document).on('click', '.cloz_info', function(){
        $(this).parent().hide();
    });
    jQuery(document).on('click', '.stl-store-item a', function(e){
        e.stopPropagation();
           var lf = jQuery(this).attr('href');
           location.href = lf;
    });
    jQuery(document).on('click', '.pjSlBtnFilterBy', function(){
        if(jQuery('#pjSlFormFiltersDropdown').is(':visible'))
        {
            jQuery('#pjSlFormFiltersDropdown').removeClass('show');
            jQuery('#pjSlFormFiltersDropdown').removeClass('in');
        }
        else
        {
            jQuery('#pjSlFormFiltersDropdown').addClass('show');
            jQuery('#pjSlFormFiltersDropdown').addClass('in');
        }
        return false;
    });
});