<?php
namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\WooGoget;
use ConnectPX\WooGoget\WooGogetUtil;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGoget_Front
 *
 * Frontend Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGogetFront') ) :

class WooGogetFront extends WooGoget {

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
		add_action('wp_enqueue_scripts', [$this, 'woo_load_scripts']);
	}

	public function woo_load_scripts() {
		wp_register_script('woo-goget', WOO_GOGET_URL . 'assets/js/woo-goget.js', ['jquery'], $this->version, true);
		wp_localize_script( 'woo-goget', 'WooGogetSetting', array( 
			'ajax_url' => admin_url('admin-ajax.php'), 
			'woogoget_ajax_nonce' => wp_create_nonce('woogoget-ajax-nonce'), 
			'woo_goget_order_form' => admin_url('admin.php?page=goget-order-delivery-form') 
		));

		// Enqueue scripts
		wp_enqueue_script( 'woo-goget' );
	}
}

endif; // class_exists check
