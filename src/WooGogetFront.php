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
		
	}
}

endif; // class_exists check
