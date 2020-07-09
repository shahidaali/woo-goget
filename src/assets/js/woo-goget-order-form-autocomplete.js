jQuery(document).ready(function($){
	$(window).load(function(){
		var $dropoff_address = $('#order_form_dropoff_details_address');		
			
		if( $dropoff_address.length ) {
			var DropoffAutocomplete = new WooGogetAutocomplete({
				reference: 'dropoff',
				autocompleteOptions: {
					country: 'MY'
				},
				autocompleteField: $dropoff_address[0],
				geoFields: {
					lat: 'order_form_dropoff_details_address_lat',
					lng: 'order_form_dropoff_details_address_lng',
				},
				geoCodeDefault: false,
				addressCallback: function(autocomplete) {

				},
				locationCallback: function(autocomplete) {
					
				}
			});

			$(document).on("keydown", $dropoff_address[0], function(e) {
				13 == e.keyCode && e.preventDefault()
			});
		}

		var $pickup_address = $('#order_form_pickup_details_address');		
			
		if( $pickup_address.length ) {
			var PickupAutocomplete = new WooGogetAutocomplete({
				reference: 'dropoff',
				autocompleteOptions: {
					country: 'MY'
				},
				autocompleteField: $pickup_address[0],
				geoFields: {
					lat: 'order_form_pickup_details_address_lat',
					lng: 'order_form_pickup_details_address_lng',
				},
				geoCodeDefault: false,
				addressCallback: function(autocomplete) {

				},
				locationCallback: function(autocomplete) {
					
				}
			});

			$(document).on("keydown", $pickup_address[0], function(e) {
				13 == e.keyCode && e.preventDefault()
			});
		}
	});
});