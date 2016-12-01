<?php namespace Ors\Orsapi\Handlers;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Ors\Support\Common;
use Ors\Orsapi\Interfaces\PassengerApiInterface;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Traits\JsonErrorHandlerTrait;
use Ors\Support\Traits\CurlPost;
use Ors\Orsapi\Orm\ORMPassenger;
use Ors\Orsapi\Oam\OAMAuth;

/**
 * This is ORS API Handler for Passenger API.
 *  
 * @author Gregor Flajs
 *
 */
class PassengerApiHandler extends BaseHandler implements PassengerApiInterface {
	
	use CurlPost;
	use JsonErrorHandlerTrait;
	
	/**
	 * Api Url (read from config)
	 * @var string
	 */
	private $api_url;
	
	/**
	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
	    $this->api_url = Config::get('orsapi::passenger.api_url');
	    $this->setLang();
	    
	    if ($auth) $this->setAuthLogin($auth);
	}
	
	/**
	 * @see \Ors\Orsapi\Handlers\BaseHandler::setApiHeader()
	 */
	protected function setApiHeader($header) {}
	
	/**
	 * @see \Ors\Orsapi\Handlers\BaseHandler::setRqid()
	 */
	protected function setRqid($response) {
	    $this->rqid = !empty($response['request-id']) ? $response['request-id'] : '';
	}
	
	/**
	 * Prepare API header info
	 */
	private function _makeHeader() {
	    $this->header['lang'] = $this->getLang();
	    $this->header['agency'] = $this->agid;
	    $this->header['master-key'] = $this->master_key;
	    $this->header['cip'] = Request::getClientIp();
	}
	
	/**
	 * Since 05.10.2016 some passenger attributes has moved into "metadata" (array) attribute.
	 * This function merges metadata with the rest of the passenger attributes, so we can easily create an object.
	 * 
	 * @param array $data
	 */
	protected function _mergeMetadata(&$data) {
		if (!empty($data['metadata']) && is_array($data['metadata']))
			$data = array_merge($data, $data['metadata']);
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::add()
	 */
	public function add($passenger) {
	
	    $this->_makeHeader();
	
	    $request = $this->header;
	    $request['action'] = 'add';
	    $request['data'] = Common::_underscoreToDash($passenger);
	
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
	    	
	    return $response['status'] == 'success' ? $response['data']['passenger-id'] : false;
	}
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::add()
	 */
	public function update($passenger) {
	
	    $this->_makeHeader();
	
	    $request = $this->header;
	    $request['action'] = 'edit';
	    $request['data'] = Common::_underscoreToDash($passenger);
	
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
	    	
	    return $response['status'] == 'success';
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::search()
	 */
	public function search($term, $options = array()) {
	    $this->_makeHeader();
	
	    if (strlen($term) < 3)
	        return new Collection();
	
	    $request = $this->header;
	    $request['action'] = 'quicksearch';
	    $request['data'] = ['query' => $term];
	
	    if (!empty($options) && is_array($options))
	        $request['data'] += $options;
	
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
	    $persons = new Collection();
	    foreach ($response['data'] as $data) {
	    	$this->_mergeMetadata($data);
	        $persons->push(new ORMPassenger(Common::_dashToUnderscore($data)));
	    }
	    	
	    return $persons->sortBy('last_name');
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::all()
	 */
	public function all($options = array()) {
	    $this->_makeHeader();
	
	    $request = $this->header;
	    $request['action'] = 'getall';
	    $request['data'] = [];
	
	    if (!empty($options) && is_array($options))
	        $request['data'] += $options;
	
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
	    $persons = new Collection();
	    foreach ($response['data'] as $data) {
	    	$this->_mergeMetadata($data);
	        $persons->push(new ORMPassenger(Common::_dashToUnderscore($data)));
	    }
	    	
	    return $persons->sortBy('last_name');
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::findIds()
	 */
	public function findIds($ids) {
	    $this->_makeHeader();
	
	    if (empty($ids))
	        return new Collection();
	     
	    $request = $this->header;
	    $request['action'] = 'getpassengersbyid';
	    $request['data'] = ['ids' => $ids];
	
	    // make api call
	    $response = $this->JSONCurlPost($this->api_url, json_encode($request));
	    $response = json_decode($response, true);
	
	    // debug
	    Common::ppreDebug( $request, 'request');
	    Common::ppreDebug( $response, 'response');
	
	    // check for error
	    try {
	        $this->_error($response);
	    }catch (\ORS\Exceptions\OrsApiException $e) {
	        return new Collection();
	    }
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // Load results
	    $persons = new Collection();
	    foreach ($response['data'] as $data) {
	    	$this->_mergeMetadata($data);
	        $persons->push(new ORMPassenger(Common::_dashToUnderscore($data)));
	    }
	
	    return $persons->sortBy('last_name');
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::find()
	 */
	public function find($id) {
	    return $this->findIds([$id])->first();
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::delete()
	 */
	public function delete($ids) {
	    $this->_makeHeader();
	
	    if (empty($ids))
	        return false;
	     
	    if (!is_array($ids))
	        $ids = Common::extrim($ids);
	
	    $request = $this->header;
	    $request['action'] = 'remove';
	    $request['data'] = ['ids' => $ids];
	
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
	
	    return $response['status'] == 'success';
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::link()
	 */
	public function link($id, $linked_ids, $options = array()) {
	    $this->_makeHeader();
	
	    if (empty($linked_ids) || empty($id))
	        return false;
	     
	    if (!is_array($linked_ids))
	        $linked_ids = Common::extrim($linked_ids);
	
	    $request = $this->header;
	    $request['action'] = 'link';
	    $request['data'] = ['dest' => $id, 'ids' => $linked_ids];
	     
	    if (!empty($options) && is_array($options))
	        $request['data'] += $options;
	
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
	
	    return $response['status'] == 'success';
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::undelete()
	 */
	public function undelete($ids) {
	    $this->_makeHeader();
	
	    if (empty($ids))
	        return false;
	     
	    if (!is_array($ids))
	        $ids = Common::extrim($ids);
	
	    $request = $this->header;
	    $request['action'] = 'undelete';
	    $request['data'] = ['ids' => $ids];
	
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
	
	    return $response['status'] == 'success';
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\PassengerApiInterface::unlink()
	 */
	public function unlink($linked_ids, $options = array()) {
	    $this->_makeHeader();
	
	    if (empty($linked_ids))
	        return false;
	     
	    if (!is_array($linked_ids))
	        $linked_ids = Common::extrim($linked_ids);
	
	    $request = $this->header;
	    $request['action'] = 'unlink';
	    $request['data'] = ['ids' => $linked_ids];
	
	    if (!empty($options) && is_array($options))
	        $request['data'] += $options;
	    
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
	
	    return $response['status'] == 'success';
	}
	
}