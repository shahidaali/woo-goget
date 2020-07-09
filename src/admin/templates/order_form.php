<?php 
use ConnectPX\WooGoget\WooGogetUtil;

$time_options = [];
$start = new \DateTime('00:00');
$times = 24 * 2; // 24 hours * 30 mins in an hour

for ($i = 0; $i < $times-1; $i++) {
	$time = $start->add(new \DateInterval('PT30M'))->format('H:i');
    $time_options[$time] = $time;
}

$items = $order->get_items();
$items_count = $order->get_item_count();
$shipping_fee = 0;

$item_name = "";
foreach ($items as $key => $item) {
	$item_name .= $item->get_name();
}

$item_name = apply_filters('woo_goget_order_items_name', $item_name, $order);

$pickupTime = strtotime("+30 hours");
$workFinishTime = WooGogetUtil::get_data($warehouse, 'closing_hours');
if ($workFinishTime && $pickupTime > strtotime($workFinishTime)) {
	$pickupTime = strtotime('tomorrow 08:00');
}


$pickup_address = WooGogetUtil::get_data($warehouse, 'address');
$pickup_address_lat = WooGogetUtil::get_data($warehouse, 'address_lat');
$pickup_address_lng = WooGogetUtil::get_data($warehouse, 'address_lng');
$reference = "Order #{$order_id}";
$person_in_charge_name = WooGogetUtil::get_data($warehouse, 'contact_person');
$person_in_charge_phone_num = WooGogetUtil::get_data($warehouse, 'contact_person_phone');

$dropoff_address = $order->get_shipping_address_1();
$dropoff_address_lat = $OrderLatLng['lat'];
$dropoff_address_lng = $OrderLatLng['lng'];
$recipient_name = $order->get_billing_first_name() . " " . $order->get_billing_first_name();
$recipient_phone_num = $order->get_billing_phone();
$sender_name = WooGogetUtil::get_data($warehouse, 'contact_person');
$sender_email = WooGogetUtil::get_data($warehouse, 'contact_person_email');

$form = [
	'order_general' => [
		[
			'name' => 'vehicle_type',
			'options' => [
				1 => 'Bike',
				2 => 'Car'
			],
			'type' => 'select'
		],
		[
			'name' => 'num_of_items',
			'default' => $items_count
		],
		[
			'name' => 'shipping_fee',
			'default' => 0,
			'attributes' => [
				'readonly' => ''
			]
		],
		[
			'name' => 'order_reference',
			'default' => $reference
		],
	],
	'pickup_details' => [
		[
			'name' => 'item_name',
			'default' => $item_name
		],
		[
			'name' => 'address',
			'info' => 'Pickup address',
			'default' => $pickup_address
		],
		[
			'name' => 'address_lat',
			'info' => 'Pickup address latitude',
			'default' => $pickup_address_lat
		],
		[
			'name' => 'address_lng',
			'info' => 'Pickup address longitude',
			'default' => $pickup_address_lng
		],
		[
			'name' => 'reference',
			'info' => 'Pickup reference',
			'default' => $reference
		],
		[
			'name' => 'location_notes',
			'info' => 'Pickup location notes',
			'type' => 'textarea'
		],
		[
			'name' => 'person_in_charge_name',
			'info' => 'Name of person in charge',
			'default' => $person_in_charge_name
		],
		[
			'name' => 'person_in_charge_phone_num',
			'default' => $person_in_charge_phone_num
		],
		[
			'name' => 'pickup_time',
			'default' => date('Y-m-d H:i', $pickupTime),
			'type' => 'text'
		]
	],
	'dropoff_details' => [
		[
			'name' => 'address',
			'info' => 'Dropoff address',
			'default' => $dropoff_address
		],
		[
			'name' => 'address_lat',
			'default' => $dropoff_address_lat
		],
		[
			'name' => 'address_lng',
			'default' => $dropoff_address_lng
		],
		[
			'name' => 'reference',
			'info' => 'Dropoff reference',
			'default' => $reference
		],
		[
			'name' => 'location_notes',
			'info' => 'Dropoff location notes',
			'default' => $order->get_customer_note(),
			'type' => 'textarea'
		],
		[
			'name' => 'recipient_name',
			'default' => $recipient_name,
		],
		[
			'name' => 'recipient_phone_num',
			'default' => $recipient_phone_num
		],
		[
			'name' => 'sender_name',
			'default' => $sender_name
		],
		[
			'name' => 'sender_email',
			'default' => $sender_email
		]
	],
];
?>
<style type="text/css">
	form {
		position: relative;
	}
	.loader {
		position: absolute;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		background: rgba(255, 255, 255, 0.8);
		z-index: 1;
		display: none;
	}
	.loader img {
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
	}
</style>
<div class="wrap">
	<h1><?php _e('Woo Goget Order Form'); ?></h1>

	<?php if( !empty($messages) ): ?>
		<div id="setting-error-settings_updated" class="notice notice-<?php echo $messages['status']; ?> settings-error is-dismissible"> 
			<p><strong><?php echo $messages['message']; ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.') ?></span></button>
		</div>
	<?php endif; ?>

	<div class="woo-goget-messages"></div>
	<form action="" method="post" id="order_form">
		<table class="form-table" role="presentation">
			<tbody>
				<?php foreach ($form as $section => $fields) { ?>
					<tr>
						<th scope="row" colspan="2"><h2><?php _e( strtoupper(str_replace("_", " ", $section)) ); ?></h2></th>
					</tr>
					<?php foreach ($fields as $key => $field) { ?>
						<?php 
						$field_info = $this->admin_field($field, $section, 'order_form');
						?>
						<tr>
							<th><?php echo $field_info['label']; ?></th>
							<td><?php echo $field_info['input']; ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
	    	<input type="hidden" name="action" value="check_fee" id="order_form_action">
	    	<button type="button" class="button-primary check-fee"><?php _e('Check Fee'); ?></button>
	    	<input type="button" class="button-primary create-order" value="<?php _e('Create Order'); ?>"/>
		</p>
		<div class="loader">
			<img src="data:image/gif;base64,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">
		</div>
	</form>
</div>
