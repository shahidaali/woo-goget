<?php
namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\WooGoget;
use ConnectPX\WooGoget\WooGogetUtil;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGogetWc
 *
 * Frontend Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGogetWc') ) :

class WooGogetWc extends WooGoget {
	var $lat_lng_fields = [
		'billing_lat',
		'billing_lng',
		'shipping_lat',
		'shipping_lng'
	];

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
		add_filter( 'woocommerce_default_address_fields' , [$this, 'override_default_address_fields'] );
		add_filter( 'woocommerce_checkout_fields' , [$this, 'woo_checkout_fields'] );
		add_action( 'woocommerce_shipping_methods', [$this, 'add_shipping_method'] );
		add_action('woocommerce_shipping_init', [$this, 'include_woocommerce_shipping_method']);
		add_action('woocommerce_checkout_update_order_review', [$this, 'save_customer_lat_lng']);
		add_action('wp_enqueue_scripts', [$this, 'woo_load_scripts']);
	}

	/*
		Load Google Api Javascript and Sugession javascript
	*/
	
	public function woo_load_scripts() {
		if(is_checkout() || is_account_page()){
			wp_enqueue_script('woo-goget-google-autocomplete', 'https://maps.googleapis.com/maps/api/js?v=3&libraries=places&key='. $this->get_option('google_api_key'), [], $this->version, true);
			wp_enqueue_script('address-autocomplete', WOO_GOGET_URL . 'assets/js/address-autocomplete.js', ['jquery'], $this->version, true);
			wp_enqueue_script('checkout-address-sugessions', WOO_GOGET_URL . 'assets/js/checkout-address-sugessions.js', ['jquery'], $this->version, true);
		}
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
	function woo_checkout_fields($fields) {

		$values = $this->get_customer_lat_lng();
		
		$fields['billing']['billing_state']['label'] = __('State', 'woocommerce');
		$fields['shipping']['shipping_state']['label'] = __('State', 'woocommerce');

	    $fields['billing']['billing_lat'] = array(
	        'label'     => __('Address Lat', 'woo-goget'),
	        'placeholder'   => "101.6130658",
	        'required'  => true,
	        'default' => $values['billing_lat'],
	    );
	    $fields['billing']['billing_lng'] = array(
	        'label'     => __('Address Lng', 'woo-goget'),
	        'placeholder'   => "101.6130658",
	        'required'  => true,
	        'default' => $values['billing_lng'],
	    );

	    $fields['shipping']['shipping_lat'] = array(
	        'label'     => __('Address Lat', 'woo-goget'),
	        'placeholder'   => "3.1570648",
	        'required'  => true,
	        'default' => $values['shipping_lat'],
	    );
	    $fields['shipping']['shipping_lng'] = array(
	        'label'     => __('Address Lng', 'woo-goget'),
	        'placeholder'   => "101.6130658",
	        'required'  => true,
	        'default' => $values['shipping_lng'],
	    );
	    return $fields;
	}

	public function override_default_address_fields($fields) {
	    $fields['state']['label'] = __('State', 'woocommerce');
	    return $fields;
	}

	public function get_customer_lat_lng() {
		$data = [];

		foreach ($this->lat_lng_fields as $field) {
			$data[$field] = WC()->session->get($field);
		}

		return $data;
	}

	public function get_customer_delivery_lat_lng() {
		$latLng = $this->get_customer_lat_lng();
		extract($latLng);

		if(!empty($shipping_lat) && !empty($shipping_lng)) {
			return [
				'lat' => $shipping_lat,
				'lng' => $shipping_lng, 
			];
		} 
		
		return [
			'lat' => $billing_lat,
			'lng' => $billing_lng, 
		];	
	}

	public function get_order_delivery_lat_lng($order) {
		$latLng = [];

		foreach ($this->lat_lng_fields as $field) {
			$latLng[$field] = $order->get_meta('_' . $field);
		}
		extract($latLng);

		if(!empty($shipping_lat) && !empty($shipping_lng)) {
			return [
				'lat' => $shipping_lat,
				'lng' => $shipping_lng, 
			];
		} 
		
		return [
			'lat' => $billing_lat,
			'lng' => $billing_lng, 
		];	
	}

	public function save_customer_lat_lng($posted_data = []) {
		if( !empty($_POST['post_data']) ) {
			parse_str(wp_unslash($_POST['post_data']), $post_data);

			foreach ($this->lat_lng_fields as $field) {
				WC()->session->set($field, $post_data[$field]);
			}
		}
	}

	public function add_shipping_method($methods) {
		$methods['goget'] = 'WooGogetShippingMethod';
        return $methods;
	}

	public function include_woocommerce_shipping_method()
    {
        require_once __DIR__ . '/WooGogetShippingMethod.php';
    }

    public function is_goget_shipping($order) {
    	$shipping_lines = $order->get_items( 'shipping' );

    	$found = false;
    	if(!empty($shipping_lines)) {
			foreach ($shipping_lines as $key => $item) {
				if($item->get_method_id() == 'goget') {
					$found = true;
					break;
				}
			}
		}

		return apply_filters('is_order_goget_shipping', $found, $order);
    }
}

endif; // class_exists check
