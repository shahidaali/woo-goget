<?php 
use ConnectPX\WooGoget\WooGogetUtil;

$time_options = [];
$start = new \DateTime('00:00');
$times = 24 * 2; // 24 hours * 30 mins in an hour

for ($i = 0; $i < $times-1; $i++) {
	$time = $start->add(new \DateInterval('PT30M'))->format('H:i');
    $time_options[$time] = $time;
}
$warehouse = [
	'fields' => [
		[
			'name' => 'name',
		],
		[
			'name' => 'city',
		],
		[
			'name' => 'address',
			'info' => 'Pickup address',
		],
		[
			'name' => 'address_lat',
			'info' => 'Pickup address latitude',
		],
		[
			'name' => 'address_lng',
			'info' => 'Pickup address longitude',
		],
		[
			'name' => 'opening_hours',
			'options' => $time_options,
			'type' => 'select',
			'default' => '08:00',
		],
		[
			'name' => 'closing_hours',
			'options' => $time_options,
			'type' => 'select',
			'default' => '20:00',
		],
		[
			'name' => 'contact_person',
			'info' => 'Name of the contact person responsible for contacting with the couriers',
		],
		[
			'name' => 'contact_person_phone',
		],
		[
			'name' => 'contact_person_email',
		],
		[
			'name' => 'pickup_notes',
			'info' => 'Default comment for pick up point',
			'type' => 'textarea',
		]
	],
];
?>
<div class="wrap">
	<h1><?php _e('Woo Goget Settings'); ?></h1>

	<?php if( !empty($messages) ): ?>
		<div id="setting-error-settings_updated" class="notice notice-<?php echo $messages['status']; ?> settings-error is-dismissible"> 
			<p><strong><?php echo $messages['message']; ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.') ?></span></button>
		</div>
	<?php endif; ?>

	<form action="" method="post">
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row" colspan="2"><h2><?php _e('General Settings'); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><?php _e('Enable Staging'); ?></th>
					<td><label for="is_sandbox">
					<input name="woo_goget_settings[is_sandbox]" type="checkbox" id="is_sandbox" value="1" <?php echo WooGogetUtil::is_checked( $this->get_option('is_sandbox'), 1 ); ?>> Yes</label></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('GoGet Staging API Key'); ?></th>
					<td><input type="text" name="woo_goget_settings[staging_api_key]" value="<?php echo $this->get_option('staging_api_key'); ?>" size="60"></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('GoGet Live API Key'); ?></th>
					<td><input type="text" name="woo_goget_settings[api_key]" value="<?php echo $this->get_option('api_key'); ?>" size="60"></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2><?php _e('Email Templates'); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><?php _e('New Job Customer Email'); ?></th>
					<td><textarea name="woo_goget_settings[new_job_customer_email_tpl]" cols="65" rows="5"><?php echo $this->get_option('new_job_customer_email_tpl'); ?></textarea><br><small>This email is sent to customer when new job created on goget.</small><br><small>Tokens available: [GOGETTER_DETAIL] [CUSTOMER_NAME]</small></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('New Job Admin Email'); ?></th>
					<td><textarea name="woo_goget_settings[new_job_admin_email_tpl]" cols="65" rows="5"><?php echo $this->get_option('new_job_admin_email_tpl'); ?></textarea><br><small>This email is sent to admin when new job created on goget.</small><br><small>Tokens available: [GOGETTER_DETAIL] [ADMIN_NAME]</small></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2><?php _e('Geo Coding'); ?></h2></th>
				</tr>
				<tr>
					<th scope="row"><?php _e('Google API Key'); ?></th>
					<td><input type="text" name="woo_goget_settings[google_api_key]" value="<?php echo $this->get_option('google_api_key'); ?>" size="60"></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h2><?php _e('Warehouse Settings'); ?></h2></th>
				</tr>
				<?php foreach ($warehouse['fields'] as $key => $field) { ?>
					<?php 
					$field_info = $this->admin_field($field, 'warehouse');
					?>
					<tr>
						<th><?php echo $field_info['label']; ?></th>
						<td><?php echo $field_info['input']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<p class="submit">
	    	<input type="submit" class="button-primary" value="<?php _e('Submit'); ?>"/>
		</p>
	</form>
</div>
