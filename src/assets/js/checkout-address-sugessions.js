jQuery(document).ready(function($){
	$(window).load(function(){
		var $billingField = $('#billing_address_1');		
		var $shippingField = $('#shipping_address_1');
			
		if( $billingField.length ) {
			var billingAutocomplete = new WooGogetAutocomplete({
				reference: 'billing',
				autocompleteField: $billingField[0],
				countryField: 'billing_country',
				component_form: {
					locality: ["billing_city", "long_name"],
					administrative_area_level_1: ["billing_state", "short_name"],
					country: ["billing_country", "long_name"],
					postal_code: ["billing_postcode", "short_name"]
				},
				formFieldsValue: {
					billing_city: "",
					billing_state: "",
					billing_postcode: "",
					billing_country: ""
				},
				selectFields: {
					billing_country : billing_country,
					billing_state : billing_state
				},
				geoFields: {
					lat: 'billing_lat',
					lng: 'billing_lng',
				},
				addressCallback: function(autocomplete) {
					$("#billing_state").trigger("change"), 
					"undefined" != typeof FireCheckout && checkout.update(checkout.urls.billing_address)
				},
				locationCallback: function(autocomplete) {
					if(autocomplete.location && !$('#ship-to-different-address-checkbox').is(':checked')) {
						var location = autocomplete.location;
						if($('#shipping_lat').length) {
							document.getElementById('shipping_lat').value = location.lat();
						}
						if($('#shipping_lng').length) {
							document.getElementById('shipping_lng').value = location.lng();
						}
					}
				}
			});

			$(document).on("keydown", $billingField[0], function(e) {
				13 == e.keyCode && e.preventDefault()
			});
		}

		if( $shippingField.length ) {
			var shippingAutocomplete = new WooGogetAutocomplete({
				reference: 'shipping',
				autocompleteField: $shippingField[0],
				countryField: 'shipping_country',
				component_form: {
					locality: ["shipping_city", "long_name"],
					administrative_area_level_1: ["shipping_state", "short_name"],
					country: ["shipping_country", "long_name"],
					postal_code: ["shipping_postcode", "short_name"]
				},
				formFieldsValue: {
					shipping_city: "",
					shipping_state: "",
					shipping_postcode: "",
					shipping_country: ""
				},
				selectFields: {
					shipping_country : shipping_country,
					shipping_state : shipping_state
				},
				geoFields: {
					lat: 'shipping_lat',
					lng: 'shipping_lng',
				},
				addressCallback: function(autocomplete) {
					console.log(autocomplete);
					$("#shipping_state").trigger("change"), 
					"undefined" != typeof FireCheckout && checkout.update(checkout.urls.shipping_address)
				}
			});

			$(document).on("keydown", $shippingField[0], function(e) {
				13 == e.keyCode && e.preventDefault()
			});
		}
	});
});