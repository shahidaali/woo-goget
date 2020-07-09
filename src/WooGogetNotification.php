<?php
namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\WooGoget;
use ConnectPX\WooGoget\WooGogetUtil;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGogetNotification
 *
 * Frontend Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGogetNotification') ) :

class WooGogetNotification extends WooGoget {

	/**
	 * __construct
	 *
	 * Class constructor
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function __construct() {
		parent::__construct();
	}

	public function init() {
		// add_action('wp_enqueue_scripts', [$this, 'woo_load_scripts']);
	}
	
	/**
	 * goget_notify_create_job
	 *
	 * Notification for goget delivery
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function goget_notify_create_job($order, $job) {
		// if(empty($response['job']->gogetters[0]))
		// 	return;
				
		$gogetter = $job->gogetters[0];

		$gogetter_detail = "<strong>Name:</strong> {$gogetter->name}<br>
			<strong>Phone:</strong> {$gogetter->phone_num}<br>
			<strong>Email:</strong> {$gogetter->email}";

		$this->goget_notify_create_job_customer($gogetter_detail, $order, $response);
		$this->goget_notify_create_job_admin($gogetter_detail, $order, $response);
	}

	function goget_notify_create_job_customer($gogetter_detail, $order, $response) {
		$customer = $order->get_user();
		$customer_name = ($customer) ? $customer->display_name : "There";
		
		// $template = "Hi [CUSTOMER_NAME],<br><br>"
		// 	. " Here is your GoGet delivery driver detail:<br><br>"
		// 	. "[GOGETTER_DETAIL]<br><br>"
		// 	. "Thank You";

		$template = nl2br($this->get_option('new_job_customer_email_tpl'));
		if(empty($template))
			return;

		$tokens = [
			'[GOGETTER_DETAIL]' => $gogetter_detail,
			'[CUSTOMER_NAME]' => $customer_name
		];

		return $this->send_email('goget_notify_create_job_customer', [
			'to' => $order->get_billing_email(),
			'subject' => 'GoGet Delivery Detail',
			'template' => $template,
			'tokens' => $tokens
		]);
	}

	function goget_notify_create_job_admin($gogetter_detail, $order, $response) {
		$admin_email = get_bloginfo( 'admin_email' );
		$admin_name = get_bloginfo( 'admin_name' );
		
		// $template = "Hi [ADMIN_NAME],<br><br>"
		// 	. " Here is your GoGet delivery driver detail:<br><br>"
		// 	. "[GOGETTER_DETAIL]<br><br>"
		// 	. "Thank You";

		$template = nl2br($this->get_option('new_job_admin_email_tpl'));
		if(empty($template))
			return;

		$tokens = [
			'[GOGETTER_DETAIL]' => $gogetter_detail,
			'[ADMIN_NAME]' => $admin_name
		];

		return $this->send_email('goget_notify_create_job_admin', [
			'to' => $admin_email,
			'subject' => 'GoGet Delivery Detail',
			'template' => $template,
			'tokens' => $tokens
		]);
	}

	/**
	 * woo_checkout_fields
	 *
	 * Custom checkout fields
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function send_email($type, $params) {
		$params = apply_filters('goget_send_email', $params, $type);

		extract($params);

		$headers[] = "Content-Type: text/html";
		$headers[] = "charset=UTF-8";

		if(empty($tokens))
			$tokens = [];

		$body = str_replace(array_keys($tokens), array_values($tokens), $template);

		return wp_mail( $to, $subject, $body, $headers );
	}
}

endif; // class_exists check
