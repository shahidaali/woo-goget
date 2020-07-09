jQuery(document).ready(function($){
	// this is the id of the form
	$(".check-fee").on('click', function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		$('#order_form_action').val('check_fee');

		ajaxOrderForm();
	});

	$(".check-fee").click();

	$(".create-order").on('click', function(e) {
		e.preventDefault(); // avoid to execute the actual submit of the form.
		$('#order_form_action').val('create_order');

		ajaxOrderForm();
	});

	function ajaxOrderForm() {
		var form = $('#order_form');
		$('.loader').show();
		$.ajax({
			type: "POST",
			url: WooGogetSetting.ajax_url,
			data: form.serialize(), // serializes the form's elements.
			success: function(data) {
				if(data.status == 'success') {
					if(data.data.fee !== undefined) {
						$('.order_form_order_general_shipping_fee').val(data.data.fee);
					}
				}

				showMessage(data.message, data.status);

				$('.loader').hide();

				$('body, html').animate({
			        scrollTop: $('.woo-goget-messages').offset().top - 70
			    }, 1000);
			}
		});
	}

	function showMessage(message, type) {
		var msg_str = '<div id="setting-error-settings_updated" class="notice notice-'+type+' settings-error is-dismissible"><p><strong>'+message+'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		$('.woo-goget-messages').html(msg_str)
	}
});