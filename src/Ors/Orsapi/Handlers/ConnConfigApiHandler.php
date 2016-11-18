<?php namespace Ors\Orsapi\Handlers;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;
use Ors\Orsapi\Interfaces\ConnConfigApiInterface;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Traits\JsonErrorHandlerTrait;
use Ors\Support\Traits\CurlPost;

/**
 * This is ORS API Handler for Connection Config API.
 *  
 * @author Gregor Flajs
 *
 */
class ConnConfigApiHandler extends BaseHandler implements ConnConfigApiInterface {
	
	use CurlPost;
	use JsonErrorHandlerTrait;
	
	/**
	 * Api Url (read from config)
	 * @var string
	 */
	private $api_url;
	
	public function __construct(){
	    $this->api_url = Config::get('orsapi::connconfig.api_url');
	}
	
	/**
	 * @see \ORS\Api\Handlers\OrsApiBaseHandler::setApiHeader()
	 */
	protected function setApiHeader($header) {}
	
	/**
	 * @see \ORS\Api\Handlers\OrsApiBaseHandler::setRqid()
	 */
	protected function setRqid($response) {
	    $this->rqid = !empty($response['request-id']) ? $response['request-id'] : '';
	}
	
	/**
	 * Prepare API header info
	 */
	private function _makeHeader() {
	    $this->header['auth-key'] = Config::get('orsapi::connconfig.auth_key');
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::listConnections()
	 */
	public function listConnections() {
		$this->_makeHeader();
		
		$request = $this->header;
		$request['action'] = 'list-connections';
		
		// make api call
		$response = $this->JSONCurlPost($this->api_url, json_encode($request));
		$response = json_decode($response, true);
		
		// debug
		Common::ppreDebug( $request, 'request');
		Common::ppreDebug( $response, 'response');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// Load results
		$list = new Collection();
		
		if (empty($response['connection-list']))
			return $connections;
		
		foreach ($response['connection-list'] as $data) {
		    $list->push(new \Ors\Orsapi\ConnConfig\Connection(Common::_dashToUnderscore($data)));
		}
		
		return $list;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::mapTocsToConnections()
	 */
	public function mapTocsToConnections(array $tocs) {
		$this->_makeHeader();
		
		$request = $this->header;
		$request['action'] = 'map-tocs-to-connections';
		$request['tocs'] = $tocs;
		
		// make api call
		$response = $this->JSONCurlPost($this->api_url, json_encode($request));
		$response = json_decode($response, true);
		
		// debug
		Common::ppreDebug( $request, 'request');
		Common::ppreDebug( $response, 'response');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// Load results
		$list = new Collection();
		
		if (empty($response['toc-map']))
		    return $list;
		
		foreach ($response['toc-map'] as $toc => $data) {
	    	$list->push(new \Ors\Orsapi\ConnConfig\ConnectionTocMap(Common::_dashToUnderscore($data)+array('toc' => $toc)));
		}
		
		return $list;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::assignTocToConnection()
	 */
	public function assignTocToConnection($tocs) {
		$this->_makeHeader();
		
		$request = $this->header;
		$request['action'] = 'assign-toc-to-connection';
		
		foreach ($tocs as $data)
			$request['tocs'][$data->toc] = array(
				'connection' => $data->connection,
				'group' => $data->group,
			);
		
		// make api call
		$response = $this->JSONCurlPost($this->api_url, json_encode($request));
		$response = json_decode($response, true);
		
		// debug
		Common::ppreDebug( $request, 'request');
		Common::ppreDebug( $response, 'response');
		//Common::ppre( $request, 'request');
		//Common::ppre( $response, 'response');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		return $response['status'] == 'success';
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::describeConnection()
	 */
	public function describeConnection($connection) {
		$this->_makeHeader();
		
		$request = $this->header;
		$request['action'] = 'describe-connection';
		$request['connection'] = $connection;
		
		// make api call
		$response = $this->JSONCurlPost($this->api_url, json_encode($request));
		$response = json_decode($response, true);
		
		// debug
		Common::ppreDebug( $request, 'request');
		Common::ppreDebug( $response, 'response');
		//Common::ppre( $request, 'request');
		//Common::ppre( $response, 'response');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// Load results
		$list = new Collection();
		
		if (empty($response['configuration']))
		    return $list;
		
		foreach ($response['configuration'] as $name => $data) {
		    $list->push(new \Ors\Orsapi\ConnConfig\ConnectionDescription(Common::_dashToUnderscore($data)+array('name' => $name)));
		}
		
		return $list;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::getConfiguration()
	 */
	public function getConfiguration($connection, $agid = null) {
	    $this->_makeHeader();
	
	    $request = $this->header;
	    $request['action'] = 'get-configuration';
	    $request['connection'] = $connection;
	    if (!empty($agid)) $request['agency-id'] = $agid;
	
	    // make api call
	    $response = $this->JSONCurlPost($this->api_url, json_encode($request));
	    $response = json_decode($response, true);
	
	    // debug
	    Common::ppreDebug( $request, 'request');
	    Common::ppreDebug( $response, 'response');
	    //Common::ppre( $request, 'request');
	    //Common::ppre( $response, 'response');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	    
	    $data = array();
	
	    if (!empty($response['configuration'])) {
	    	$data = array(
	    	    'configuration' => $response['configuration'],
	    	);
	    }
	    
	    $data['active'] = $response['active'];
	
	    $res = new \Ors\Orsapi\ConnConfig\ConnectionConfiguration($data);
	
	    return $res;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ConnConfigApiInterface::setConfiguration()
	 */
	public function setConfiguration($configuration, $connection, $agid = null) {
	    $this->_makeHeader();
	
	    $request = $this->header;
	    $request['action'] = 'set-configuration';
	    $request['connection'] = $connection;
	    if (!empty($agid)) $request['agency-id'] = $agid;
	
	    if (is_array($configuration)) {
	    	foreach ($configuration as $name => $value)
	    	    $request['configuration'][$name] = $value;
	    }
	    else {
		    foreach ($configuration->configuration as $name => $value)
		        $request['configuration'][$name] = $value;
	    }
	
	    // make api call
	    $response = $this->JSONCurlPost($this->api_url, json_encode($request));
	    $response = json_decode($response, true);
	
	    // debug
	    Common::ppreDebug( $request, 'request');
	    Common::ppreDebug( $response, 'response');
	    //Common::ppre( $request, 'request');
	    //Common::ppre( $response, 'response');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    return $response['status'] == 'success';
	}
}