<?php
namespace ConnectPX\WooGoget\Admin;

use ConnectPX\WooGoget\WooGoget;
use ConnectPX\WooGoget\WooGogetUtil;
use ConnectPX\WooGoget\WooGogetWc;
use ConnectPX\WooGoget\WooGogetNotification;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGogetAdmin
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGogetAdmin') ) :

class WooGogetAdmin extends WooGoget {

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
	
	/**
	 * init
	 *
	 * Class init
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function init() {
		// Admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' )  );

		add_action('admin_enqueue_scripts', [$this, 'woo_load_admin_scripts']);

		add_action( 'wp_ajax_check_fee', [$this, 'ajax_check_fee'] );
		add_action( 'wp_ajax_nopriv_wp_ajax_check_fee', [$this, 'ajax_check_fee'] );

		add_action( 'wp_ajax_create_order', [$this, 'ajax_create_order'] );
		add_action( 'wp_ajax_nopriv_wp_ajax_create_order', [$this, 'ajax_create_order'] );
	}

	public function woo_load_admin_scripts() {
		wp_register_script('woo-goget-admin', WOO_GOGET_URL . 'assets/js/woo-goget-admin.js', ['jquery'], $this->version, true);
		wp_localize_script( 'woo-goget-admin', 'WooGogetSetting', array( 'ajax_url' => admin_url('admin-ajax.php'), 'woogoget_ajax_nonce' => wp_create_nonce('woogoget-ajax-nonce')) );

		// Enqueue scripts
		wp_enqueue_script( 'woo-goget-admin' );

		wp_register_script('woo-goget-google-autocomplete', 'https://maps.googleapis.com/maps/api/js?v=3&libraries=places&key='. $this->get_option('google_api_key'), [], $this->version, true);
		wp_register_script('address-autocomplete', WOO_GOGET_URL . 'assets/js/address-autocomplete.js', ['jquery'], $this->version, true);
		wp_register_script('woo-goget-order-form-autocomplete', WOO_GOGET_URL . 'assets/js/woo-goget-order-form-autocomplete.js', ['woo-goget-google-autocomplete', 'address-autocomplete'], $this->version, true);
	}

	/**
	 * admin_menu
	 *
	 * admin_menu callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function admin_menu() {
		global $_wp_last_object_menu;
		$_wp_last_object_menu++;

		$slug = 'woo-goget';

		// Woo GoGet Admin Page
		add_menu_page( 
			__( 'Woo Goget', $slug ),
			__( 'Woo Goget', 'woo-goget' ),
			'manage_options', 
			'goget-settings',
			array( $this, 'woo_goget_admin_settings_page' ), 
			'dashicons-welcome-view-site',
			$_wp_last_object_menu 
		);

		// Order Form
		$order_form = add_submenu_page( $slug . '_private',
			__( 'Order Delivery', 'goget' ),
			__( 'Order Delivery', 'goget' ),
			'edit_posts', 
			'goget-order-delivery-form',
			array( $this, 'woo_goget_admin_order_delivery_form' ) 
		);
	}

	/**
	 * admin_menu
	 *
	 * admin_menu callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function woo_goget_admin_settings_page() {
		// Check for permission
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Save submitted form
	    $messages = $this->settings_page_save();

		// Include admin settings page
	    include( WOO_GOGET_PATH . 'Admin/templates/settings.php');
	}

	/**
	 * admin_menu
	 *
	 * admin_menu callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function woo_goget_admin_order_delivery_form() {
		// Check for permission
		// if ( ! current_user_can( 'manage_options' ) )  {
		// 	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		// }

		wp_enqueue_script('woo-goget-order-form-autocomplete');

		$WooGogetWc = new WooGogetWc();

		$order_ids = !empty($_GET['ids']) ? $_GET['ids'] : "";
		$order_ids = wp_parse_id_list($order_ids);

		if(empty($order_ids[0])) {
			return;
		}

		$order_id = $order_ids[0];
		$order = wc_get_order($order_id);
        $order = apply_filters('woo_goget_order_form_order', $order);

        $warehouse = WooGoget()->get_option('warehouse');
        $warehouse = apply_filters('woo_goget_order_form_warehouse', $warehouse);

        $OrderLatLng = $WooGogetWc->get_order_delivery_lat_lng($order);
		// Save submitted form
	    // $messages = $this->settings_page_save();

  //       $api = WooGoget()->getApi();
  //       $job = $api->jobDetail(12069);

  //       __pre($api);

  //       $notification = new WooGogetNotification();
		// $notification->goget_notify_create_job($order, $job);

	    if($order->get_meta('_goget_is_delivered')) {
	    	$messages = [
	    		'status' => 'error',
	    		'message' => 'This order is already added to GoGet. GoGet job id #' . $order->get_meta('_goget_job_id')
	    	];
	    }

		// Include admin settings page
	    include( WOO_GOGET_PATH . 'Admin/templates/order_form.php');
	}

	/**
	 * admin_menu_settings_page_save
	 *
	 * Save admin settings
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function settings_page_save() {
		// Check for permission
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( empty( $_POST[ 'woo_goget_settings' ] ) ) {
	        return;
	    }

	    $options = WooGogetUtil::request( 'woo_goget_settings' );

	    // Update options
	    update_option( self::OPTIONS_KEY,  $options );

	    // Reset options
	    $this->set_options();

	    return [
	    	'status' => 'success',
	    	'message' => __( 'Settings saved' )
	    ];
	}

	function ajax_check_fee() {

		$json_data = [
			'status' => 'error',
			'message' => __('Nothing to process.'),
			'data' => []
		];

		$order_general = $_POST['order_form']['order_general'];
		$pickup_detail = $_POST['order_form']['pickup_details'];
		$dropoff_details = $_POST['order_form']['dropoff_details'];

		$fee_data = [
		    "pickup" => [
		        "name" => $pickup_detail['item_name'],
		        "location" => $pickup_detail['address'],
		        "location_lat" => $pickup_detail['address_lat'],
		        "location_long" => $pickup_detail['address_lng'],
		        "parking" => false,
		        "start_at" => $pickup_detail['pickup_time']
		    ],
		    "dropoff" => [
		        [
		            "location" => $dropoff_details['address'],
			        "location_lat" => $dropoff_details['address_lat'],
			        "location_long" => $dropoff_details['address_lng'],
		        ]
		    ],
		    "ride_id" => $order_general['vehicle_type'],
		    "bulky" => false,
		    "guarantee" => false,
		    "num_of_items" => $order_general['num_of_items'],
		    "flexi" => false,
		    "route" => false
		];

		$fee_data = apply_filters('goget_ajax_fee_data', $fee_data, $this);

		//__pre($fee_data);

		$api = WooGoget()->getApi();
		$fee = $api->checkFee($fee_data);

		if(!$api->isError()) {
			$json_data = [
				'status' => 'success',
				'message' => "Goget order delivery fee is {$fee}",
				'data' => [
					'fee' => $fee
				]
			];
		}
		else {
			$json_data['message'] = $api->error_str();
		}

		header('Content-Type: application/json');
		echo json_encode($json_data);
		exit();
	}

	function ajax_create_order() {

		$json_data = [
			'status' => 'error',
			'message' => __('Nothing to process.'),
			'data' => []
		];

		$order_general = $_POST['order_form']['order_general'];
		$pickup_detail = $_POST['order_form']['pickup_details'];
		$dropoff_details = $_POST['order_form']['dropoff_details'];

		$fee_data = [
		    "pickup" => [
		        "name" => $pickup_detail['item_name'],
		        "location" => $pickup_detail['address'],
		        "location_lat" => $pickup_detail['address_lat'],
		        "location_long" => $pickup_detail['address_lng'],
		        "location_notes" => $pickup_detail['location_notes'],
		        "parking" => false,
		        "start_at" => $pickup_detail['pickup_time'],
		        "person_in_charge_name" => $pickup_detail['person_in_charge_name'],
		        "person_in_charge_phone_num" => $pickup_detail['person_in_charge_phone_num'],
		    ],
		    "dropoff" => [
		        [
		            "location" => $dropoff_details['address'],
			        "location_lat" => $dropoff_details['address_lat'],
			        "location_long" => $dropoff_details['address_lng'],
			        "location_notes" => $dropoff_details['location_notes'],
			        "recipient_name" => $dropoff_details['recipient_name'],
			        "recipient_phone_num" => $dropoff_details['recipient_phone_num'],
		        ]
		    ],
		    "notes" => $order_general['order_notes'],
		    "ride_id" => $order_general['vehicle_type'],
		    "bulky" => false,
		    "guarantee" => false,
		    "num_of_items" => $order_general['num_of_items'],
		    "flexi" => false,
		    "route" => false,
		    "on_demand_fallback" => false,
    		"weekday_fallback" => false,
    		"exclusive" => false,
    		"non_halal" => false
		];

		$fee_data = apply_filters('goget_ajax_fee_data', $fee_data, $this);

		$api = WooGoget()->getApi();
		$fee = $api->createJob($fee_data);

		if(!$api->isError()) {
			$response = $api->getResponseData();

			$job_id = $response['job']->id;

			$order_id = $_POST['order_id'];
			$order = wc_get_order($order_id);
			if( $order ) {
				$order->update_meta_data( '_goget_is_delivered', 1 );
				$order->update_meta_data( '_goget_job_id', $job_id );
				$order->save();

				// $notification = new WooGogetNotification();
				// $notification->goget_notify_create_job($order, $response);
			}
			

			$json_data = [
				'status' => 'success',
				'message' => "Goget job #{$job_id} created succesfully.",
				'data' => [
					'response' => $response
				]
			];
		}
		else {
			$json_data['message'] = $api->error_str();
		}

		header('Content-Type: application/json');
		echo json_encode($json_data);
		exit();
	}

	/**
	 * admin_field
	 *
	 * Save admin settings
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function admin_field($field, $setting_subname = "", $setting_name = "woo_goget_settings") {
		// Check for permission
		$name = $field['name'];
		$label = ucwords(str_replace("_", " ", $name));
		$info = isset($field['info']) ? $field['info'] : '';
		$type = isset($field['type']) ? $field['type'] : 'text';
		$default = isset($field['default']) ? $field['default'] : '';
		$options = isset($field['options']) ? $field['options'] : [];
		$attributes = isset($field['attributes']) ? $field['attributes'] : [];
		$id = "{$setting_name}_{$setting_subname}_{$name}";
		$class = $id; 
		$class .= " field-{$type}";

		$attributes_arr = [];
		foreach ($attributes as $key => $value) {
			$attributes_arr[] = "{$key}=\"{$value}\"";
		}
		$atts = implode(" ", $attributes_arr);

		if($setting_subname) {
			$value = $this->get_option([$setting_subname, $name], $default);
			$name = "{$setting_name}[{$setting_subname}][{$name}]";
		}
		else {
			$value = $this->get_option($name, $default);
			$name = "{$setting_name}[{$name}]";
		}
		
		$input = "";
		if($type == 'select') {
			$input = "<select name=\"{$name}\" class=\"{$class}\" id=\"{$id}\" {$atts}>"
				. WooGogetUtil::select_options($options, $value, "", true)
				. "</select>";
		}
		else if($type == 'textarea') {
			$input = "<textarea name=\"{$name}\" class=\"{$class}\" id=\"{$id}\" cols=\"63\" rows=\"5\" { {$atts}}>{$value}</textarea>";	
		} else {
			$input = "<input type=\"{$type}\" class=\"{$class}\" id=\"{$id}\" value=\"{$value}\" name=\"{$name}\" size=\"60\" { {$atts}}>";	
		}
		if($info) {
			$input .= "<br><small>{$info}</small>";
		}

		return [
			'label' => $label,
			'input' => $input
		];
	}
}

endif; // class_exists check
