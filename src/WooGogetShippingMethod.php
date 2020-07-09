<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ConnectPX\WooGoget\WooGogetUtil;
use ConnectPX\WooGoget\WooGogetGeo;
use ConnectPX\WooGoget\WooGogetWc;

/**
 * WooGogetShippingMethod
 *
 * WooGogetShippingMethod Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */
if( ! class_exists('WooGogetShippingMethod') ) :

	class WooGogetShippingMethod extends WC_Shipping_Method {

		/**
	     * Constructor for your shipping class
	     *
	     * @access public
	     * @return void
	     */
	    public function __construct($instance_id = 0) {
	        parent::__construct($instance_id);

	        $this->id                 = 'goget'; 
	        $this->method_title       = __( 'GoGet Shipping', 'goget' );  
	        $this->method_description = __( 'Custom Shipping Method for GoGet', 'goget' ); 
	        
	        $this->supports = [
	            'shipping-zones',
	            'instance-settings',
	            'instance-settings-modal',
	        ];

	        // Availability & Countries
			// $this->availability = 'including';
			// $this->countries = array(
			//     'MY', // Malaysia
			// );
	        $this->init();
	    }

	    /**
	     * Init your settings
	     *
	     * @access public
	     * @return void
	     */
	    function init() {
	        // Load the settings API
	        $this->init_form_fields(); 
	        $this->init_settings(); 

	        $this->enabled = $this->get_option('enabled', 'yes');
	        $this->title = $this->get_option('title');

	        // Save settings in admin if you have any defined
	        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	    }

	    /**
	     * Define settings field for this shipping
	     * @return void 
	     */
	    function init_form_fields() { 
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable', 'goget' ),
					'type' => 'checkbox',
					'description' => __( 'Enable this shipping.', 'goget' ),
					'default' => 'yes'
				),
				'title' => array(
					'title' => __( 'Title', 'goget' ),
					'type' => 'text',
					'description' => __( 'Title to be display on site', 'goget' ),
					'default' => $this->method_title
				),
			);
	    }

	    /**
	     * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
	     *
	     * @access public
	     * @param mixed $package
	     * @return void
	     */
	    public function calculate_shipping( $package = [] ) {
	    	$warehouse = WooGoget()->get_option('warehouse');
	    	// __pre($warehouse);

	    	if(empty($warehouse) || empty($warehouse['address'])) {
	    		return;
	    	}

	    	$totalItems = 0;
	        foreach ($package['contents'] as $contentItem) {
	            $totalItems += $contentItem['quantity'];
	        }

	    	$pickupTime = strtotime("+30 hours");
	    	$workFinishTime = $warehouse['closing_hours'];
	        if ($workFinishTime && $pickupTime > strtotime($workFinishTime)) {
	            $pickupTime = strtotime('tomorrow 08:00');
	        }
	        $deliveryCity    = $package['destination']['city'] ?? '';
        	$deliveryAddress = $package['destination']['address'] ?? '';

        	$WooGogetWc = new WooGogetWc();
        	$WooGogetWc->save_customer_lat_lng();
        	$CustomerLatLng = $WooGogetWc->get_customer_delivery_lat_lng();

			$deliveryLat = $CustomerLatLng['lat'];
			$deliveryLng = $CustomerLatLng['lng'];

        	// $Geo = new WooGogetGeo();
        	// __pre($Geo->getLatLng($deliveryAddress));
        
        	// __pre($post_data);

	    	$data = [
			    "pickup" => [
			        "name" => "Woo Goget Order",
			        "location" => $warehouse['address'],
			        "location_lat" => $warehouse['address_lat'],
			        "location_long" => $warehouse['address_lng'],
			        "parking" => false,
			        "start_at" => date('Y-m-d H:i', $pickupTime)
			    ],
			    "dropoff" => [
			        [
			            "location" => $deliveryAddress,
			            "location_lat" => $deliveryLat,
			            "location_long" => $deliveryLng
			        ]
			    ],
			    "ride_id" => 1,
			    "bulky" => false,
			    "guarantee" => false,
			    "num_of_items" => $totalItems,
			    "flexi" => false,
			    "route" => false
			];

			$data = apply_filters('goget_shipping_data', $data, $warehouse, $package, $this);

			//__pre($data);

	    	// $weight = 0;
	     //    $cost = 0;
	     //    $country = $package["destination"]["country"];

	     //    foreach ( $package['contents'] as $item_id => $values ) 
	     //    { 
	     //        $_product = $values['data']; 
	     //        $weight = $weight + $_product->get_weight() * $values['quantity']; 
	     //    }

	     //    $weight = wc_get_weight( $weight, 'kg' );

			$api = WooGoget()->getApi();
			$fee = $api->checkFee($data);
			//__pre($api);

			if(!$api->isError()) {
				$rate = [
		            'id'    => $this->id,
		            'label' => $this->title,
		            'cost'  => $fee,
		        ];
				$this->add_rate($rate);
	            do_action('woocommerce_' . $this->id . '_shipping_add_rate', $this, $rate);
			}
	    }
	}

endif; // class_exists check
