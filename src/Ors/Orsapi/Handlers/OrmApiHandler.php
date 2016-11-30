<?php namespace Ors\Orsapi\Handlers; 

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\OrmApiInterface;
use Ors\Orsapi\Oam\OAMHeader;
use Ors\Support\Common;
use Ors\Support\SmartSearchParameters;
use Ors\Orsapi\Orm\ORM;
use Ors\Orsapi\Oam\OAMAuth;
use SoapClient;

/**
 * This is ORS API Handler for ORM API.
 *
 * @author Gregor Flajs
 *
 */
class OrmApiHandler extends BaseHandler implements OrmApiInterface {
	
	const ORSXML_SOAP_LOCATION = 'http://www.ors.si/orsxml-soap-api/orsxml_soap.php';
	const ORSXML_SOAP_URI = 'http://www.ors.si/orsxml-soap-api';
	
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
	 * Construct Soap client object and set api language
	 * 
	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
		
	    // api lang
	    $this->setLang();
	
	    // soap client object
	    $this->orsSoapClient = new SoapClient(null, array(
	        'location' => self::ORSXML_SOAP_LOCATION,
	        'uri'      => self::ORSXML_SOAP_URI,
	        'trace'    => 1)
	    );
	    
	    if ($auth) $this->setAuthLogin($auth);
	}
	
	/**
	 * @see \Ors\Orsapi\Handlers\BaseHandler::setRqid()
	 */
	protected function setRqid($response) {
	    $this->rqid = $response['rqid'];
	}
	
	/**
	 * @see \Ors\Orsapi\Handlers\BaseHandler::setApiHeader()
	 */
	protected function setApiHeader($header) {
	    $this->api_header = new OAMHeader($header);
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
	 * Prepare SOAP header from search parameters
	 * @param SmartSearchParameters $params
	 */
	protected function _makeHeader($params, $ctype_id = null) {
	
	    // get account info from ibeid (Currently only if TEST mode is enabled)
        $this->header['agid'] = $this->agid;
        $this->header['ibeid'] = $this->ibeid;
        $this->header['lang'] = $this->getLang();
        	
        // set login credentials
        if ($this->master_key) $this->header['master_key'] = $this->master_key;
        
        if ($this->usr)  $this->header['usr'] = $this->usr;
        if ($this->pass) $this->header['pass'] = $this->pass;
	
	    // set debup options (debug_opts)
	    if (!empty($params->getCrsf('debug_opts')->value))
	        $this->header['debug_opts'] = $params->getCrsf('debug_opts')->value;
	    
	    // client IP
	    $this->header['cip'] = Request::getClientIp();
	}
	
	/**
	 * @see \Ors\Orsapi\Handlers\BaseHandler::_error()
	 */
	protected function _error($response) {
	    if ( (isset($response['errorNr']) && $response['errorNr'] != '') || !empty($response['error'])) {
	        $code = !is_numeric($response['errorNr']) ? 0 : $response['errorNr'];
	        $rqid = !empty($response['rqid']) ? $response['rqid'] : '';
	        $error = !empty($response['error']) ? $response['error'] : '';
	        $this->setRqid(array('rqid' => $rqid));
	        throw new OrsApiException($error, $code, null, $rqid);
	    }
	    if (empty($response) || $response == -1 || empty($response['xmlReq']) || empty($response['header']))
	        throw new OrsApiException('No API response', 0, null, '');
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\OrmApiInterface::orm()
	 */
	public function orm($params, $orm) {
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_orm_api_call";
	    $response = $this->orsSoapClient->$call( 'orm', $params->__toArray()+$orm->toApiArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( $params->__toArray(), 'search_params');
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    Common::ppreDebug( $response, 'Response');
	    //Common::ppreDebug( $orm->user->toArray(), 'Orm old');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	
	    // ORM model
	    //if (empty($response['admin']['login']['userid'])))
	    //$response['admin']['login']['userid'] = $orm->userid;
	    //$response['admin']['operator']['ibeid'] = $params->getCrsf('ibeid')->value;
	
	    $o_model = ORM::withApiResponse($response['admin']);
	
	    return $o_model;
	}
}