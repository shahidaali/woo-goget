<?php
namespace ConnectPX\WooGoget;

use ConnectPX\WooGoget\Api\WooGogetApi;
use ConnectPX\WooGoget\WooGogetUtil;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGoget
 *
 * Base Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGoget') ) :

class WooGoget {
	
	/** @var string The plugin version number. */
	var $version = '1.0.0';

	/** @private static ensures only one instance of class. */
	private static $instance;
	
	/** @var array The plugin options array. */
	var $options = array();
	
	const OPTIONS_KEY   =   'woo_goget_options';

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
		$this->set_options();

		define("WOO_GOGET_PATH",  plugin_dir_path( __FILE__ ));
		define("WOO_GOGET_URL",   plugins_url( '/', __FILE__ ));
	}

	/**
	 * get_instance
	 *
	 * Load instance of class
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public static function get_instance() {
        // Check is $_instance has been set
        if(!isset(self::$instance)) 
        {
            // Creates sets object to instance
            self::$instance = new WooGoget();
        }

        // Returns the instance
        return self::$instance;
    }

	/**
	 * set_options
	 *
	 * Load plugin options from wp_options
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function set_options() {
		$this->options = get_option(self::OPTIONS_KEY) ? get_option(self::OPTIONS_KEY) : [];
	}

	/**
	 * get_option
	 *
	 * Get plugin option
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function get_option($key, $default = null) {
		return WooGogetUtil::get_data($this->options, $key, $default);
	}

	/**
	 * getApi
	 *
	 * Get api instance
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function getApi() {
		$WooGogetApi = new WooGogetApi([
			'api_key' => $this->get_option('api_key'),
			'is_sandbox' => $this->get_option('is_sandbox')
		]);
		return $WooGogetApi;
	}
}

endif; // class_exists check
