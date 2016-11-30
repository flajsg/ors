<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;

/**
 * ORS API Model: FlightInfo
 *
 * This is a model for a ORS Flight Information that Touroperator provides.
 * Flight Info can be described in details using flight time information or in the text format as info lines.
 * 
 * @author Gregor Flajs
 */

class OAMFlightInfo extends Eloquent {
	
	public function __construct($attributes = array()) {
	    parent::__construct($attributes);
	
	    $this->response = null;
	    $this->times = new Collection();
	}
	
	/**
	 * Constructor from array
	 * @param array|json $data
	 * 		response,offer,operator
	 * @return \OAM\OAMFlightInfo
	 */
	public static function withArray($data) {
	
	    // check if $data is json
	    if (Common::isJson($data))
	        $data = json_decode($data, true);
	    
	    $instance = new self($data);
	    
	    // Add Response
	    $instance->response = new OAMFlightInfoResponse($data['response']);
	     
	    // Add Times
	    if (!empty($data['times']))
	    foreach ($data['times'] as $t)
	        $instance->times->push(new OAMFlightInfoTime($t));
	
	    return $instance;
	}

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'hsc', 'ahc', 'zhc', 'info', 'toc'
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'hsc';
    
    /**
     * Flight Times
     * @var Collection|OAMFlightInfoTime[]
     */
    public $times;
    
    /**
     * FlightInfo response data
     * @var OAMFlightInfoResponse
     */
    public $response;
    
    /**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        
        if (!empty($this->response))
        	$array['response'] = $this->response->toArray();
        
       	$array['times'] = $this->times->toArray();
       	
        return $array;
    }
}