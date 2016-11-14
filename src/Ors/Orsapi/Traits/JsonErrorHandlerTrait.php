<?php namespace Ors\Orsapi\Traits;

use Ors\Orsapi\OrsApiException;

/**
 * Use this trait when you are using ORS Json APIs, for error handling
 * 
 * @author Gregor Flajs
 *
 */
trait JsonErrorHandlerTrait {
	
	protected function _error($response) {
	    if (empty($response) || $response == -1)
	        throw new OrsApiException('No response', 0, null, '');
	    if (empty($response['status']))
	        throw new OrsApiException('No status in response', 0, null, '');
		if ($response['status'] == 'error') {
			
			// Good Uros is using different error responses for 2 different Json APIs. Good work! ;)
			if (!empty($response['error'])) {
				$code = !is_numeric($response['error']['code']) ? 0 : $response['error']['code'];
				throw new OrsApiException($response['error']['message'], $code, null, $response['request-id']);
			}
			else {
				$code = !is_numeric($response['data']['code']) ? 0 : $response['data']['code'];
				throw new OrsApiException($response['data']['message'], $code, null, $response['request-id']);
			}
	        
	    }
	}
	
}