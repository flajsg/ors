<?php namespace Ors\Orsapi\Handlers;

use Illuminate\Support\Facades\Lang;
use Ors\Support\SmartSearchParameters;
use Ors\Support\CRSFieldInterface;

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
	 * @var \Ors\Orsapi\Oam\OAMHeader
	 */
	protected $api_header;
	
	/**
	 * Agency id
	 * @var int
	 */
	protected $agid;
	
	/**
	 * Subacount id (branch office)
	 * @var int
	 */
	protected $ibeid;
	
	/**
	 * Agency master key (can be used without user/pass)
	 * @var int
	 */
	protected $master_key;
	
	/**
	 * API username (for use withour agency master key)
	 * @var string
	 */
	protected $usr;
	
	/**
	 * API password (for use withour agency master key)
	 * @var string
	 */
	protected $pass;
	
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
	 * @return \Ors\Orsapi\Oam\OAMHeader
	 */
	public function getApiHeader() { return $this->api_header; }
	
	/**
	 * Return API request header
	 * @return array
	 */
	public function getRequestHeader() { return $this->header; }

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
	 * Set agency id and master key (if you have one).
	 * If you don't have master key, then use setLogin() method.
	 *
	 * @param int $agid
	 * @param int $ibeid
	 * @param string $master_key
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setAgencyKey($agid, $ibeid=0, $master_key) {
	    $this->agid = $agid;
	    $this->ibeid = $ibeid;
	    $this->master_key = $master_key;
	    return $this;
	}
	
	/**
	 * Set api login credentials.
	 *
	 * @param int $agid
	 * @param int $ibeid
	 * @param string $usr
	 * @param string $pass
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setLogin($agid, $ibeid=0, $usr, $pass) {
	    $this->agid = $agid;
	    $this->ibeid = $ibeid;
	    $this->usr = $usr;
	    $this->pass = $pass;
	    return $this;
	}
	
	/**
	 * Set api login credentials.
	 *
	 * @param OAMAuth $auth
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setAuthLogin($auth) {
	    $this->agid = $auth->agid;
	    $this->ibeid = $auth->ibeid;
	    $this->master_key = $auth->master_key;
	    $this->usr = $auth->usr;
	    $this->pass = $auth->pass;
	    return $this;
	}
	
	/**
	 * Set different ibeid
	 * @param int $ibeid
	 * @return \Ors\Orsapi\Handlers\BaseHandler
	 */
	public function setIbeid($ibeid) {
		$this->ibeid = $ibeid;
		return $this;
	}
	
	/**
	 * Return instance of SmartSearchParameters.
	 * Use this method if you want to make sure $params are an instance of CRSFieldInterface.
	 *
	 * @param array $params
	 * @return \Ors\Support\SmartSearchParameters
	 */
	public static function toSmartParams($params) {
	    return $params instanceof CRSFieldInterface ? $params : SmartSearchParameters::withArray($params);
	}
	
	/**
	 * Set request id from Api response
	 * @param mixed $response
	 */
	abstract protected function setRqid($response);
	
	/**
	 * Use this method to set OAM api response header after request is made.
	 * This can only be called inside request methods. 
	 * Use getApiHeader() method to return results. 
	 * @param mixed $response
	 */
	abstract protected function setApiHeader($response);
	
	/**
	 * From Api response checks if there was an error and throw OrsApiException.
	 * @param mixed $response
	 * 		ors api response
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	abstract protected function _error($response);
}