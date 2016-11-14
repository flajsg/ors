<?php namespace Ors\Orsapi\Handlers;

use Illuminate\Support\Facades\Lang;

/**
 * Base ORS API Handler class.
 * 
 * This class implements common API handler methods that all api handlers can utilize.
 *  
 * @author Gregor Flajs
 *
 */
abstract class BaseHandler {
	
	/**
	 * Api request header (login information)
	 * @var array
	 */
	protected $header;
	
	/**
	 * Api language code
	 * @var string
	 */
	protected $lang;
	
	/**
	 * Request id from last API request
	 * @var string
	 */
	protected $rqid;
	
	/**
	 * API Response header information
	 * @var \OAM\OAMHeader
	 */
	protected $api_header;
	
	/**
	 * Return api language code
	 * @return string
	 */
	public function getLang() { return $this->lang; }
	
	/**
	 * Return request id (if possible). If not set, then return null.
	 * @return string|null
	 */
	public function getRqid() { return $this->rqid; }
	
	/**
	 * Return API response header
	 * @return \OAM\OAMHeader
	 */
	public function getApiHeader() { return $this->api_header; }

	/**
	 * Set API lang. 
	 * If $lang is empty then system language 'app.locale' is used.
	 *
	 * @param string $lang
	 */
	public function setLang($lang = null) {
		if (!empty($lang)) {
			$this->lang = $lang;
		}
		else {
			$this->lang = Lang::locale();
			if ($this->lang == 'sl')
			    $this->lang = 'si';
		}
	}
	
	/**
	 * Set request id from Api response
	 * @param mixed $response
	 */
	abstract protected function setRqid($response);
	
	/**
	 * Use this method to set OAM api response header after request is made.
	 * This can only be called inside request methods. 
	 * Use getApiHEader() method to return results. 
	 * @param mixed $response
	 */
	abstract protected function setApiHeader($response);
	
	/**
	 * From Api response checks if there was an error and thwors OrsApiException.
	 * @param mixed $response
	 * 		ors api response
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	abstract protected function _error($response);
}