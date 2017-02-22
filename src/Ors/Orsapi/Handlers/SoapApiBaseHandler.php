<?php namespace Ors\Orsapi\Handlers; 

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Ors\Orsapi\Traits\SoapErrorHandlerTrait;
use Ors\Orsapi\Oam\OAMAuth;
use Ors\Orsapi\Oam\OAMHeader;
use SoapClient;

class SoapApiBaseHandler extends BaseHandler {

	use SoapErrorHandlerTrait;
	
	/**
	 * API url
	 * @var string
	 */
	protected $api_url;
	
	/**
	 * Soap clien object
	 * @var SoapClient
	 */
	protected $orsSoapClient;
	
	/**
	 * ORS API Response array
	 * @var array
	 */
	protected $response;
	
	/**
	 * Session id used in last API request.
	 * Sid is only used in a few legacy calls and it will be deprecated soon.
	 * But since this base soap api hander is used for legacy calls we must include it.
	 * @var string
	 */
	protected $sid;
	
	/**
	 * Construct Soap client object and set api language
	
	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
		
		// set api language
		$this->setLang();
		
		// Use default search api url
		$this->_makeApiAuth(Config::get('orsapi::search.api_url'));
		
		if ($auth) $this->setAuthLogin($auth);
	}
	
	/**
	 * Create soap-client object with api-url
	 * @param string $api_url
	 */
	protected function _makeApiAuth($api_url) {
	    $this->api_url = $api_url;
	
	    // soap client object
	    $this->orsSoapClient = new SoapClient(null, array(
	        'location' => $this->api_url,
	        'uri'      => dirname($this->api_url),
	        'trace'    => 1)
	    );
	}

	/**
	 * Prepare SOAP header
	 * @param SmartSearchParameters $params
	 */
	protected function _makeHeader($params) {
		$params = $this->toSmartParams($params);
	
	    $this->header['agid'] = $this->agid;
	    
	    if (isset($this->ibeid) && !empty($this->ibeid)) {
	    	$this->header['ibeid'] = $this->ibeid;
	    }
	    else {
	    	if (!empty($params->find('ibeid')->value))
	    		$this->setIbeid($params->find('ibeid')->value);
	    }
	    
	    $this->header['lang'] = $this->getLang();
	
	    // set login credentials
	    if ($this->master_key) $this->header['master_key'] = $this->master_key;
	
	    if ($this->usr)  $this->header['usr'] = $this->usr;
	    if ($this->pass) $this->header['pass'] = $this->pass;
	
	    $this->header['lang'] = $this->getLang();
	
	    // set debup options (debug_opts)
	    if (!empty($params->find('debug_opts')->value))
	        $this->header['debug_opts'] = $params->find('debug_opts')->value;
	
	    // limits
	
	    // client IP
	    $this->header['cip'] = Request::getClientIp();
	}
	
	/**
	 * Return used session id
	 * @return string
	 */
	public function getSid() {
		return $this->sid;
	}
	
	/**
	 * @see \ORS\Api\Handlers\OrsApiBaseHandler::setRqid()
	 */
	protected function setRqid($response) {
	    $this->rqid = $response['rqid'];
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::setApiHeader()
	 */
	protected function setApiHeader($header) {
	    $this->api_header = new OAMHeader($header);
	}
}