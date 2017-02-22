<?php namespace Ors\Orsapi\Handlers; 

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
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
class OrmApiHandler extends SoapApiBaseHandler implements OrmApiInterface {
	
	/**
	 * Construct Soap client object and set api language
	 * 
	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
		parent::__construct($auth);
		
	    $this->_makeApiAuth(Config::get('orsapi::orm.api_url'));
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
	    Common::ppreDebug( $response['post_url'], 'url');
	    Common::ppreDebug( $params->__toArray(), 'search_params');
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
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
	
	    $o_model = ORM::withApiResponse($response['admin']);
	
	    return $o_model;
	}
}