<?php
namespace ConnectPX\WooGoget;

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooGoget_Util
 *
 * Util Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WooGogetUtil') ) :

class WooGogetUtil {

	/** @var array The plugin options array. */
	var $options = array();
	
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

	/**
	 * get_nested_value
	 *
	 * Get nested value from array
	 *
	 * @since	1.0.0
	 *
	 * @param	$array data array
	 * @param	$search_array keys to search in data
	 * @param	$default_value default value
	 * @return	data value
	 */	
	public static function get_nested_value($array, $search_array, $default = null) {
	    if( !is_array($array) ) 
	        return $default;

	    if( ! is_array($search_array) ) 
	        return (isset($array[ $search_array ])) ? $array[ $search_array ] : $default;;

	    $found_value = null;
	    foreach($search_array as $search) {
            if(is_array($array) && array_key_exists($search, $array)) {
                $found_value = $array[ $search ];
                $array = $found_value;
            } else {
                $found_value = null;
                break;
            }
        }

	    return ($found_value) ? $found_value : $default;
	}

	/**
	 * get_data
	 *
	 * Get value from array
	 *
	 * @since	1.0.0
	 *
	 * @param	$data: data array, $key: data key, $default: default value
	 * @return	data value
	 */	
	public static function get_data($data, $key, $default = null) {
		return self::get_nested_value($data, $key, $default);
	}

	/**
	 * request
	 *
	 * Get value from php request
	 *
	 * @since	1.0.0
	 *
	 * @param $key: data key, $default: default value
	 * @return	data value
	 */	
	public static function request($key, $default = null) {
		return self::get_data($_REQUEST, $key, $default);
	}

	/**
	 * is_checked
	 *
	 * Checked checkbox
	 *
	 * @since	1.0.0
	 *
	 * @param	$value: checked value, $compare: checkbox value to compare
	 * @return	checked attribute
	 */	
	public static function is_checked($value, $compare = 1) {
		return ($value == $compare) ? 'checked="checked"' : '';
	}

	/**
	 * select_options
	 *
	 * Array to select options
	 *
	 * @since	1.0.0
	 *
	 * @param	$rows: array values, $selected_option: selected option, $use_key: usey keys for values
	 * @return	checked attribute
	 */	
	public static function select_options($rows, $selected_option = null, $empty_lable = "", $use_key = true) {
	    if( !is_array($rows) ) return;

	    $options = "";

	    // Selected value to array for multiple values
	    if($selected_option && !is_array($selected_option)) {
	        $selected_option = array($selected_option);
	    }

	    // Empty label
	    if( $empty_lable != "" ) {
	        $options .= "<option value=\"\">{$empty_lable}</option>";
	    }

	    // Creaye options from array
	    foreach ($rows as $key => $value) {
	        $value_item = ($use_key) ? $key : $value;
	        $selected = (!empty($selected_option) && in_array($value_item, $selected_option)) ? 'selected="selected"' : "";

	        $options .= "<option value=\"{$value_item}\" {$selected}>{$value}</option>";
	    }
	    return $options;
	}
}

endif; // class_exists check
