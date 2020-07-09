<?php
namespace ConnectPX\WooGoget\Api;

use ConnectPX\WooGoget\WooGogetUtil;
use Curl\Curl;

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
if( ! class_exists('WooGogetApi') ) :

class WooGogetApi {

	const STAGING_URL   =   'https://staging-api.goget.my/api/public/v1/';
	const LIVE_URL   =   'https://api.goget.my/api/public/v1/';

	/** @var array API errors. */
	var $errors = array();

	/** @var array API response. */
	var $response = array();

	/** @var curl request object. */
	var $curl = null;

	/** @var bool check if staging. */
	var $is_sandbox = false;

	/** @var array The plugin options array. */
	var $config = array();

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
	function __construct($config = []) {
		if(empty($config['api_key'])) {
			$this->errors['missing_key'] = 'Missing api key';
		}

		$this->is_sandbox = !empty($config['is_sandbox']) ? true : false;
		$this->config = $config;
	}

	/**
	 * iniresetApit
	 *
	 * Reset api params for the next call
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function resetApi() {
		$this->response = [];
		$this->errors = [];
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
	public function requestApi($endpoint, $method = 'GET', $data = []) {
		if( $this->isError() )
			return false;

		$url = ($this->is_sandbox) ? self::STAGING_URL :  self::LIVE_URL;
		$url .= $endpoint;
		$method = strtolower($method);

        $curl = new Curl();

        $curl->setHeader('Authorization', 'Token token=' . $this->config['api_key']);
        $curl->setHeader('Accept', 'application/json');
        $curl->setHeader('Content-Type', 'application/json');

        $curl->{$method}($url, $data);
		$this->response = $curl->response;
		$this->curl = $curl;

		if(!$this->isResponseSuccess()) {
			$this->errors['api_error'] = $this->getResponseMessage();
		}
		else if ($curl->error) {
		    $this->errors['curl_error'] = 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
		}

		return $this->response;
    }

    /**
	 * getResponseData
	 *
	 * Get data from response
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function getResponseData($key = null, $default = null) {
		$data = [];
		if(!empty($this->response->data)) {
			$data = (array) $this->response->data;
		}

		if( $key ) {
			return WooGogetUtil::get_data($data, $key, $default);
		}

		return $data;
	}

	/**
	 * getResponseParam
	 *
	 * Get param from response
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function getResponseParam($key = null, $default = null) {
		$data = [];
		if(!empty($this->response)) {
			$data = (array) $this->response;
		}

		if( $key ) {
			return WooGogetUtil::get_data($data, $key, $default);
		}

		return $data;
	}

    /**
	 * isResponseSuccess
	 *
	 * Check if request is successfull
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function isResponseSuccess() {
		if(isset($this->response->success) && !$this->response->success) {
			return false;
		}

		return true;
	}

	/**
	 * getResponseMessage
	 *
	 * Get response info message
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function getResponseMessage() {
		if(!empty($this->response->info)) {
			return $this->response->info;
		}
	}

    /**
	 * isError
	 *
	 * Check if request has error
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function isError() {
		if(!empty($this->errors)) {
			return true;
		}

		return false;
	}

	/**
	 * errors
	 *
	 * Get errors array
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function errors() {
		return $this->errors;
	}

	/**
	 * error_str
	 *
	 * Return errors str
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function error_str() {
		return implode("\r\n", $this->errors);
	}

    /**
	 * getCredits
	 *
	 * Get credits from api
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function getCredits($data = []) {
		$this->requestApi('users/credits', 'get', $data);

		if(!$this->isError()) {
			return $this->getResponseData('credits_remaining', 0);
		}

		return false;
    }

    /**
	 * checkFee
	 *
	 * Check shipping fee
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function checkFee($data = []) {
		$this->requestApi('jobs/check_fee', 'post', json_encode($data));

		if(!$this->isError()) {
			return $this->getResponseParam('fee', 0);
		}

		return false;
    }

    /**
	 * createJob
	 *
	 * Create new Job
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function createJob($data = []) {
		$this->requestApi('jobs', 'post', json_encode($data));

		if(!$this->isError()) {
			return $this->getResponseData();
		}

		return false;
    }

    /**
	 * jobDetail
	 *
	 * Get job detail
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function jobDetail($job_id, $data = []) {
		$this->requestApi('jobs/' . $job_id, 'get', json_encode($data));

		if(!$this->isError()) {
			return $this->getResponseParam();
		}

		return false;
    }
}

endif; // class_exists check
