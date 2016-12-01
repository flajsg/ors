<?php namespace Ors\Orsapi\Handlers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use SoapClient;
use Ors\Support\Common;
use Ors\Support\SmartSearchParameters;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\ReservationsApiInterface;
use Ors\Orsapi\Oam\OAMHeader;
use Ors\Orsapi\Orm\ORM;
use Ors\Orsapi\Bookings\BookingHistory;
use Ors\Orsapi\Bookings\BookingTotals;
use Ors\Orsapi\Oam\OAMAuth;

/**
 *
 * This is ORS API Bookings hander. 
 * 
 * We use the old orsapi soap wrapper for bookings search (latter we will implement a new handler width WSDL file from the new ORSXML framework.
 * But for now this should be it.
 *
 * @author Gregor Flajs
 *
 */
class ReservationsApiHandler extends BaseHandler implements ReservationsApiInterface {
	
	const ORSXML_SOAP_LOCATION = 'http://www.ors.si/orsxml-soap-api/orsxml_soap.php';
	const ORSXML_SOAP_URI = 'http://www.ors.si/orsxml-soap-api';
	
	/**
	 * Mapping our filter names to API filter names
	 * @var array
	 */
	private $filters_map = array(
		'status' => 'status',
		'gid' => 'gid',
		'htn' => 'obj_name',
		'hon' => 'obj_city',
		'rgn' => 'obj_region',
		'vnd' => 'start',
		'bsd' => 'end',
		'prc' => 'remote_book_id',
		'bkc' => 'book_id',
		'toc' => 'toc',
		'user_id' => 'booker_id',
		'booker' => 'booker',
		'booked_at' => 'book_date',
		'price' => 'price',
		'account_id' => 'agid',
		'ibeid' => 'ibeid',
		'typ' => 'type',
		'psn_name' => 'passenger_name',
		'psn_email' => 'passenger_email',
		'dirty' => 'dirty',
	);
	
	/**
	 * Mapping our operators to API operators
	 * @var array
	 */
	private $operators_map = array(
		'=' => 'is',
		'!=' => 'is not',
		'date<' => 'before',
		'date>' => 'after',
		'date>=' => 'after or at',
		'date<=' => 'before or at',
		'<' => 'lower',
		'>' => 'greater',
		'>=' => 'greater or equal',
		'<=' => 'lower or equal',
		'like' => 'like',
		'!like' => 'not like',
		'like%' => 'starts with',
		'!like%' => 'not starts with',
		'%like' => 'ends with',
		'!%like' => 'not ends with',
	);

	/**
	 * Start attribute for Limits
	 * @var int
	 */
	protected $limits_start;
	
	/**
	 * Number of objects (bookings) per page
	 * @var int
	 */
	protected $objects_per_page;
	
	/**
	 * Total bookings
	 * @var int
	 */
	protected $bookings_count;
	
	/**
	 * Construct Soap client object and set api language
	 *
	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
		
		$this->setLang();
		
		// soap client object
		$this->orsSoapClient = new SoapClient(null, array(
		    'location' => self::ORSXML_SOAP_LOCATION,
		    'uri'      => self::ORSXML_SOAP_URI,
		    'trace'    => 1)
		);
		
		$this->limits_start = 0;
		
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
	 * Setter: limits_start
	 * @param int $start
	 */
	public function setLimitsStart($start) {
	    $this->limits_start = $start;
	}
	
	/**
	 * Setter: objects_per_page
	 * @param int $objects_per_page
	 */
	public function setObjectsPerPage($objects_per_page){
	    $this->objects_per_page = $objects_per_page;
	}
	
	/**
	 * Getter: bookings_count
	 * @return int
	 */
	public function getBookingsCount() {
	    return $this->bookings_count;
	}
	
	/**
	 * Prepare SOAP header from search parameters
	 * @param SmartSearchParameters $params
	 */
	protected function _makeHeader($params) {
	    
		// get account info from ibeid (Currently only if TEST mode is enabled)
		$this->header['agid'] = $this->agid;
		$this->header['ibeid'] = $this->ibeid;
		$this->header['lang'] = $this->getLang();
		 
		// set login credentials
		if ($this->master_key) $this->header['master_key'] = $this->master_key;
		
		if ($this->usr)  $this->header['usr'] = $this->usr;
		if ($this->pass) $this->header['pass'] = $this->pass;
		
        $this->header['lang'] = $this->getLang();
	
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
	        //Common::ppreDebug( array('errorNr' => $response['errorNr'], 'error' => $response['error']), 'Error');
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
	 * @see \Ors\Orsapi\Interfaces\ReservationsApiInterface::search()
	 */
	public function search($params, $filters, $search = array()) {
	
	    $this->_makeHeader($params);
	
	    $search = array(
	        'search' => array('@attributes' => $search, 'group' => array()),
	        'limits' => array('@attributes' => array('start' => $this->limits_start, 'count' => $this->objects_per_page))
	    );

	    // add filters
	    if (!empty($filters)) $search['search']['group'] = $filters;
	    
	    // add sorts
	    if ($params->getCrsf('sort')->value) {
	    	list($col, $dir) = explode('|', $params->getCrsf('sort')->value);
	    	$search['sort']['item'] = array('@attributes' => array('name' => $col, 'order' => $dir));
	    }
	    
	    // make api call
	    $call = "orsxml_bookings_api_call";
	    $response = $this->orsSoapClient->$call( 'search', $search, $this->header );
	
	    // debug xmlReq
	    if (!empty($response['xmlReq'])) Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    if (!empty($response['xmlRes'])) Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug
	    Common::ppreDebug( $this->header, 'header');
	    //Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
	    //Common::ppreDebug( $response['xmlReq'], 'xmlReq-raw');
	    //Common::ppreDebug( $response, 'Response');
	
	
	    // create and return a collection
	    $collection = new Collection();
	
	    foreach ($response['bookings'] as $item) {
	    	if (!empty($item))
	        	$collection->push(ORM::withApiResponse($item['admin']));
	    };
	    
	    $this->bookings_count = !empty($response['count']) ? $response['count'] : 0;
	
	    //Common::ppreDebug($collection->toArray(), 'Bookings');
	    
	    return $collection;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ReservationsApiInterface::totals()
	 */
	public function totals($params, $filters, $search = array()) {
	
	    $this->_makeHeader($params);
	
	    $search = array(
	        'search' => array('@attributes' => $search, 'group' => array()),
	        'limits' => array('@attributes' => array('start' => $this->limits_start, 'count' => $this->objects_per_page))
	    );

	    // add filters
	    if (!empty($filters)) $search['search']['group'] = $filters;
	    
	    
	    // make api call
	    $call = "orsxml_bookings_api_call";
	    $response = $this->orsSoapClient->$call( 'totals', $search, $this->header );
	
	    // debug xmlReq
	    if (!empty($response['xmlReq'])) Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    if (!empty($response['xmlRes'])) Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
	    	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug
	    Common::ppreDebug( $this->header, 'header');
	    //Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
	    //Common::ppreDebug( $response['xmlReq'], 'xmlReq-raw');
	    //Common::ppreDebug( $response, 'Response');
	
	    $totals = new BookingTotals(Common::_dashToUnderscore($response['totals']));
	    
	    return $totals;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ReservationsApiInterface::history()
	 */
	public function history($params, $bookings) {
		$this->_makeHeader($params);
		
		$search = array('booking' => array());
		
		foreach ($bookings as $booking_id) {
			$search ['booking'] []= array('@attributes' => array('id' => $booking_id));
		}
		 
		// make api call
		$call = "orsxml_bookings_api_call";
		$response = $this->orsSoapClient->$call( 'history', $search, $this->header );
		
		// debug xmlReq
		if (!empty($response['xmlReq'])) Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
		if (!empty($response['xmlRes'])) Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// set header
		$this->setApiHeader($response['header']);
		
		// debug
		Common::ppreDebug( $this->header, 'header');
		//Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		//Common::ppreDebug( $response['xmlReq'], 'xmlReq-raw');
		//Common::ppreDebug( $response, 'Response');
		
		$bookings = new Collection();
		
		foreach ($response['bookings'] as $booking)
			$bookings->push(BookingHistory::withApiResponse($booking));
		 
		return $bookings;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\ReservationsApiInterface::chown()
	 */
	public function chown($params, $owner, $filters, $search = array()){

		$this->_makeHeader($params);
		
		$search = array(
		    'search' => array('@attributes' => $search, 'group' => array()),
		    'limits' => array('@attributes' => array('start' => $this->limits_start, 'count' => $this->objects_per_page)),
			'owner' => $owner
		);
		
		// add filters
		if (!empty($filters)) $search['search']['group'] = $filters;
		 
		// make api call
		$call = "orsxml_bookings_api_call";
		$response = $this->orsSoapClient->$call( 'chown', $search, $this->header );
		
		// debug xmlReq
		if (!empty($response['xmlReq'])) Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
		if (!empty($response['xmlRes'])) Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// set header
		$this->setApiHeader($response['header']);
		
		// debug
		Common::ppreDebug( $this->header, 'header');
		//Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		//Common::ppreDebug( $response['xmlReq'], 'xmlReq-raw');
		//Common::ppreDebug( $response, 'Response');
		 
		$this->bookings_count = !empty($response['count']) ? $response['count'] : 0;
		
		return $this->bookings_count;
	}
} 