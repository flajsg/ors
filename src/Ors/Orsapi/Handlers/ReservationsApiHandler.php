<?php namespace Ors\Orsapi\Handlers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
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
class ReservationsApiHandler extends SoapApiBaseHandler implements ReservationsApiInterface {

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
		parent::__construct($auth);
		
		$this->setLang();
		
		$this->_makeApiAuth(Config::get('orsapi::reservations.api_url'));
		
		$this->limits_start = 0;
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
	 * @see \Ors\Orsapi\Interfaces\ReservationsApiInterface::search()
	 */
	public function search($params, $filters, $search = array()) {
		$params = $this->toSmartParams($params);
	    $this->_makeHeader($params);
	
	    $search = array(
	        'search' => array('@attributes' => $search, 'group' => array()),
	        'limits' => array('@attributes' => array('start' => $this->limits_start, 'count' => $this->objects_per_page))
	    );

	    // add filters
	    if (!empty($filters)) $search['search']['group'] = $filters;
	    
	    // add sorts
	    if ($params->find('sort')->value) {
	    	list($col, $dir) = explode('|', $params->find('sort')->value);
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
		$params = $this->toSmartParams($params);
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
		$params = $this->toSmartParams($params);
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
		$params = $this->toSmartParams($params);
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