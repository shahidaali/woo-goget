<?php

namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\Admin\WooGogetAdmin;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGoget
 *
 * Main Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WooGogetLoader') ) :

class WooGogetLoader {
	
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
		
	}

	public function init() {
		(new WooGogetAdmin())->init();
		
		//if ( class_exists( 'woocommerce' ) ) {
			add_action( 'woocommerce_shipping_methods', [$this, 'add_shipping_method'] );
			add_action('woocommerce_shipping_init', [$this, 'include_woocommerce_shipping_method']);
		//}
	}

	public function add_shipping_method($methods) {
		$methods['goget'] = 'WooGogetShippingMethod';
        return $methods;
	}

	public function include_woocommerce_shipping_method()
    {
        require_once __DIR__ . '/WooGogetShippingMethod.php';
    }
}


endif; // class_exists check