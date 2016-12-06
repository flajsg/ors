<?php namespace Ors\Orsapi\Traits;

use Ors\Orsapi\OrsApiException;

/**
 * Use this trait when you are using ORS SOAP APIs, for error handling
 * 
 * @author Gregor Flajs
 *
 */
trait SoapErrorHandlerTrait {
	
	/**
	 * @see \ORS\Api\Handlers\OrsApiBaseHandler::_error()
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
}