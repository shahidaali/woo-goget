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
if( ! class_exists('WooGogetGeo') ) :

class WooGogetGeo extends WooGoget {

	var $google_api_key = null;

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
	function __construct($api_key = null) {
		parent::__construct();
		if(!$api_key) {
			$api_key = $this->get_option('google_api_key');
		}
		$this->init($api_key);
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
	function init($api_key = null) {
		$this->google_api_key = $api_key;
	}

	/**
	 * getLatLng
	 *
	 * Get lat long from address
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function getLatLng($address) {
		$address = str_replace(" ", "+", $address);

	    $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$this->google_api_key);
	    $json = json_decode($json);
	    // __pre($json);
	    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
	    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
	    return [$lat, $long];
	}
}

endif; // class_exists check
