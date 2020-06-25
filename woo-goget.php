<?php
/*
Plugin Name: Woo Goget
Plugin URI: http://connectpx.com/
Description: Wordpress plugin to integrate Goget delivery with woocommerce.
Version: 1.0.0
Author: ConnectPX
Author URI: http://connectpx.com/
Text Domain: woo_goget
Domain Path: /lang
*/

use ConnectPX\WooGoget;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( trailingslashit( dirname( __FILE__ ) ) . 'vendor/autoload.php' );

(new WooGoget\WooGogetLoader())->init();

/**
 * WooGoget
 *
 * Get global instance of WooGoget
 *
 * @since	1.0.0
 *
 * @param	Void
 * @return	$WooGoget
 */	
if( !function_exists('WooGoget') ) {
	function WooGoget() {
		global $WooGoget;
		
		// Instantiate only once.
		if( !isset($WooGoget) ) {
			$WooGoget = WooGoget\WooGoget::get_instance();
		}

		$GLOBALS['WooGoget'] = $WooGoget;
		return $WooGoget;
	}
}

// $api = WooGoget()->getApi();
// $fee = $api->checkFee([
//     "pickup" => [
//         "name" => "Roses",
//         "location" => "Publika Shopping Gallery, 1 Jalan Dutamas 1, Solaris Dutamas, KL., Solaris Dutamas, 50480 Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia",
//         "location_lat" => 3.1704279,
//         "location_long" => 101.666192,
//         "parking" => true,
//         "start_at" => "2020-06-25 16:00"
//     ],
//     "dropoff" => [
//         [
//             "location" => "IKEA Damansara@2, Jalan PJU 7/2, Mutiara Damansara, 47800 Petaling Jaya, Selangor, Malaysia",
//             "location_lat" => 3.1570648,
//             "location_long" => 101.6130658
//         ]
//     ],
//     "ride_id" => 1,
//     "bulky" => false,
//     "guarantee" => true,
//     "num_of_items" => "1-2",
//     "flexi" => false,
//     "route" => false
// ]);
// $Credits = $api->getCredits();

// echo '<pre>';
// print_r($api);
// print_r($api->error_str());
// exit();