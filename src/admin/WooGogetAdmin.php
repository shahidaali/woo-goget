<?php
namespace ConnectPX\WooGoget\Admin;

use ConnectPX\WooGoget\WooGoget;
use ConnectPX\WooGoget\WooGogetUtil;

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

		// Settings
		$settings = add_submenu_page( $slug,
			__( 'Settings', 'goget' ),
			__( 'Setting', 'goget' ),
			'manage_options', 
			'goget-settings',
			array( $this, 'woo_goget_admin_settings_page' ) 
		);

		// Settings
		$order_form = add_submenu_page( $slug . '_private',
			__( 'Order Delivery', 'goget' ),
			__( 'Order Delivery', 'goget' ),
			'manage_options', 
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
	    include_once( WOO_GOGET_PATH . 'admin/templates/settings.php');
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
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Save submitted form
	    $messages = $this->settings_page_save();

		// Include admin settings page
	    include_once( WOO_GOGET_PATH . 'admin/templates/settings.php');
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
}

endif; // class_exists check
