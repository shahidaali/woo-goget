<?php

namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\Admin\WooGogetAdmin;
use ConnectPX\WooGoget\WooGogetWc;

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
		(new WooGogetFront())->init();

		//if(is_admin()) {
			(new WooGogetAdmin())->init();
		//}
		
		//if ( class_exists( 'woocommerce' ) ) {
			(new WooGogetWc())->init();

			
		//}
	}
}


endif; // class_exists check