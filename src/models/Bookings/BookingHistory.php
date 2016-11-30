<?php namespace Ors\Orsapi\Bookings;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;
use Ors\Orsapi\Orm\ORMResponse;

/**
 * Booking history model.
 * 
 * @author Gregor Flajs
 *
 */
class BookingHistory extends Eloquent {
	
	protected $fillable = ['id'];
	
	/**
	 * History entries
	 * @var Collection|BookingHistoryEntry[]
	 */
	public $entries;
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
		
		$this->entries = new Collection();
	}
	
	/**
	 * Creates BookingHistory model from ORS API response array.
	 * <code>
	 * $response = array(
	 * 		'@attributes' => (id)
	 * 		'entry => array(..entries..),
	 * ))
	 * <code>
	 *
	 * @param $this
	 */
	public static function withApiResponse($response) {
	    $attributes = array('id' => $response['@attributes']['id']);

	    $instance = new self($attributes);
	
	    if (!empty($response['entry'])) {
	    	foreach ($response['entry'] as $entry)
	    		$instance->entries->push(BookingHistoryEntry::withApiResponse($entry));
	    }
	    
	    return $instance;
	}
}

/**
 * Booking history entry model.
 * 
 * Model holds information about what happened, who executed the action, what was the status and when did it happened.
 * 
 * @author Gregor Flajs
 *
 */
class BookingHistoryEntry extends Eloquent {
	
	protected $fillable = ['action', 'rqid', 'message', 'type', 'entry_date', 'booker'];
	
	/**
	 * ORM response object
	 * @var \Ors\Orsapi\Orm\ORMResponse
	 */
	public $response;
	
	
	public function __construct($attributes = array()) {
	    parent::__construct($attributes);
	
	    $this->response = new ORMResponse();
	}
	
	/**
	 * Creates BookingHistoryEntry model from ORS API response array.
	 * <code>
	 * $entry = array(
	 * 		'@attributes' => (date)
	 * 		'booker' => array(),
	 * 		'status' => array(),
	 * 		'action => '',
	 * 		'type => '',
	 * 		'request-id => '',
	 * 		'message => '',
	 * ))
	 * <code>
	 *
	 * @param $this
	 */
	public static function withApiResponse($entry) {
	
	    $attributes = array(
	    	'action' => $entry['action'],
	    	'rqid' => $entry['request-id'],
	    	'message' => $entry['message'],
	    	'entry_date' => $entry['@attributes']['date'],
		);
	
	    if (!empty($entry['booker']['@attributes'])) {
	    	$attributes['booker']['id'] = $entry['booker']['@attributes']['id'];
	    	$attributes['booker']['email'] = $entry['booker']['@attributes']['email'];
	    	$attributes['booker']['name'] = $entry['booker']['@value'];
	    }
	    
	    $instance = new self($attributes);
	
	    $instance->response = new ORMResponse(array(
    		'mid' => $entry['status']['@attributes']['code'],
    		'severity' => $entry['status']['@attributes']['severity'],
    		'txt' => $entry['status']['@value'],
    		'sts' => $entry['type'],
	    ));
	    
	    return $instance;
	}
	
	/**
	 * BookerName accessor.
	 * Return booker name if exists or null if not.
	 * @return string
	 */
	public function getBookerNameAttribute() {
		if ($this->attributes['booker']['name']) return $this->attributes['booker']['name'];
		return null;
	}
	
	/**
	 * Return human readable entry date-time
	 * @return string
	 */
	public function getEntryDateDispAttribute() {
		if (strtotime($this->attributes['entry_date']) <= 0) return '';
		return Common::dateTime($this->attributes['entry_date']);
	}
}

