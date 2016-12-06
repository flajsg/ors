<?php namespace Ors\Orsapi\Handlers;

use Ors\Orsapi\Interfaces\FlightInfoInterface;
use Ors\Orsapi\Interfaces\ITAG_SearchApiInterface;
use Ors\Orsapi\Oam\OAMFlightInfo;
use Ors\Orsapi\Oam\OAMFlightInfoTime;
use Ors\Orsapi\Oam\OAMFlightInfoResponse;
use Ors\Support\Common;

class FlightInfoHandler extends SoapApiBaseHandler implements ITAG_SearchApiInterface, FlightInfoInterface {
	
	/**
	 * @see \Ors\Orsapi\Interfaces\FlightInfoInterface::flightInfo()
	 */
	public function flightInfo($params) {
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_pauschal_api_call";
	    $response = $this->orsSoapClient->$call( 'flightInfo', $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    //Common::ppre( $params->__toArray(), 'Response');
	    //Common::ppre( $this->header, 'Response');
	    //Common::ppre( $response, 'Response');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	
	    // FlightInfo model
	    $f_model = new OAMFlightInfo(array('hsc' => $params->find('hsc')->value, 'toc' => $params->find('toc')->value, 'ahc' => $params->find('ahc')->value, 'zhc' => $params->find('zhc')->value, 'info' => $response['info']));
	
	    // add Response
	    $f_model->response = new OAMFlightInfoResponse($response['response']);
	    	
	    // add Times
	    $f_model->times = new Collection();
	    foreach ($response['times'] as $item) {
	        $f_model->times->push(new OAMFlightInfoTime($item));
	    };
	
	    return $f_model;
	}
	
}
