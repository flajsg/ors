<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;

/**
 * ORS API Model: Availability
 *
 * This is a model for a ORS availability check.
 */

class OAMAvailability extends Eloquent {
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
		
		$this->response = null;
		$this->operator = null;
		$this->object = null;
		$this->offer = null;
		$this->flightInfo = null;
		$this->persons = new Collection();
		$this->extras = new Collection();
		$this->services = new Collection();
	}
	
	/**
	 * Constructor from array
	 * @param array|json $data
	 * 		response,offer,operator
	 * @return \OAM\OAMAvailability
	 */
	public static function withArray($data) {
		
		// check if $data is json
		if (Common::isJson($data))
		    $data = json_decode($data, true);
		
	    $instance = new self($data);
	    
	    
	    // Add Response
	    $instance->response = new OAMAvailabilityResponse($data['response']);
	    
	    
	    // Offer model
	    $offer_class = "Ors\Orsapi\Oam\OAMOffer_{$data['ctype_id']}"; 
	    
	    
	    if (class_exists($offer_class))
	    	$instance->offer = new $offer_class($data['offer']);
	    else
	    	$instance->offer = new OAMOffer($data['offer']);
	    
	    // Object model
	    $object_class = "Ors\Orsapi\Oam\OAMObject_{$data['ctype_id']}";
	    if (class_exists($object_class))
		    $instance->object = new $object_class(!empty($data['object']) ? $data['object'] : $data['offer']);
	    else
		    $instance->object = new OAMObject(!empty($data['object']) ? $data['object'] : $data['offer']);

	    // Add Services
	    if (!empty($data['services']))
	    	foreach ($data['services'] as $p)
	        	$instance->services->push(new OAMAvailabilityService($p));
	     
	    // Add Operator
	    if (!empty($data['operator']))
	    	$instance->operator = new OAMAvailabilityOperator($data['operator']);
	    
	    // Add Flight Info
	    if (!empty($data['flightInfo']))
	        $instance->flightInfo = OAMFlightInfo::withArray($data['flightInfo']);
	     
	    // Add Persons
	    if (!empty($data['persons']))
	    	foreach ($data['persons'] as $p)
	    		$instance->persons->push(new OAMAvailabilityPerson($p));
	    
	    return $instance;
	}

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'ctype_id', 'hsc', 'toc', 'info'
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'hsc';

    /**
     * Response info
     * @var OAMAvailabilityResponse
     */
    public $response;
    
    /**
     * Object
     * @var OAMObject
     */
    public $object;
    
    /**
     * Offer info
     * @var OAMOffer
     */
    public $offer;
    
    /**
     * Operator info
     * @var OAMAvailabilityOperator
     */
    public $operator;
    
    /**
     * Persons
     * @var Collection|OAMAvailabilityPerson[]
     */
    public $persons;
    
    /**
     * Extras
     * @var Collection|OAMAvailabilityExtras[]
     */
    public $extras;
    
    /**
     * Services
     * @var Collection|OAMAvailabilityService[]
     */
    public $services;
    
    /**
     * FlightInfo
     * @var OAMFlightInfo
     */
    public $flightInfo;
    
    
    /**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        if (!empty($this->response))
        	$array['response'] = $this->response->toArray();
        
        if (!empty($this->operator))
        	$array['operator'] = $this->operator->toArray();
        
        if (!empty($this->object))
        	$array['object'] = $this->object->toArray();
        
        if (!empty($this->services))
        	$array['services'] = $this->services->toArray();
        
        if (!empty($this->offer))
        	$array['offer'] = $this->offer->toArray();
        
        if (!empty($this->flightInfo))
        	$array['flightInfo'] = $this->flightInfo->toArray();
        
        $array['persons'] = $this->persons->toArray();
        return $array;
    }
    
    /**
     * Serialize object to array and prepare it for ORM mask.
     * 
     * @return array
     */
    public function toArrayForORM(){
    	$array = $this->toArray();
    	unset($array['response']['ttp_check']);
		unset($array['response']['ppc_check']);
		unset($array['flightInfo']);
		return $array;
    }
}