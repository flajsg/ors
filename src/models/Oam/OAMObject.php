<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;

/**
 * ORS API Model: Object
 *
 * This is a model for a ORS Object (hotel, trip, ...) that has a unique gid (giata id). Data are providet from OrsApi call.
 */

class OAMObject extends OAMObjectAbstract {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	// basic object info
    	'ctype_id', 'htn', 'gid', 'pic', 'stc', 
    	
    	// destination info
    	'hon', 'oid', 'rgc', 'rgn', 'rgg', 'rggn', 
    	
    	// special attributes for trips offerDetails request
    	'toc',
    	
    	// rating
    	'ovr', 'emf', 'cnt',
    	
    	// geo
    	'geo', 'lat', 'lon',
    			
    	// room info
    	'pax', 'rooms', 'bedrooms',
    			
    	// price
    	'ppc',
	];

    /**
     * Primary key
     * @var int
     */
    protected $primaryKey = 'gid';
    
    /**
     * Object vpcs (Service types)
     * @var Collection|OAMObjectContent[]
     */
    public $vpcs;
    
    /**
     * Object zacs (Room types)
     * @var Collection|OAMObjectContent[]
     */
    public $zacs;
        
    /**
     * Object tocs (Tour operators)
     * @var Collection|OAMObjectContent[]
     */
    public $tocs;
            
    /**
     * Object tdcs (Durations)
     * @var Collection|OAMObjectContent[]
     */
    public $tdcs;
    
    /**
     * Object subs (Transfer type)
     * @var Collection|OAMObjectContent[]
     */
    public $subs;
    
    /**
     * Object sids (Roundtrip entry points)
     * @var Collection|OAMObjectContent[]
     */
    public $sids;
    
    /**
     * Object vnds (Departure dates)
     * @var Collection|OAMObjectContent[]
     */
    public $vnds;
    
    /**
     * Object facts
     * @var Collection|OAMObjectContent[]
     */
    public $facts;

    /**
     * Object offers (trips)
     * @var Collection|OAMOffer[]
     */
    public $offers;
    
    /**
     * Some initializations is needed
     * @param array $attributes
     */
    public function __construct($attributes = array()) {
    	parent::__construct($attributes);
    	
    	$this->vpcs = new Collection();
    	$this->zacs = new Collection();
    	$this->tocs = new Collection();
    	$this->tdcs = new Collection();
    	$this->subs = new Collection();
    	$this->sids = new Collection();
    	$this->vnds = new Collection();
    	$this->facts = new Collection();
    	$this->offers = new Collection();
    }
    
    /* ==================================================== *
     * ABSTRACT methods (Implementation) - Start
     * ==================================================== */
    
    /**
     * Cart Offer Name attribute.
     * This is used to display Offer Name in Cart list view.
     * @return string
     */
    public function getCartOfferNameAttribute() {
    	return null;
    }
    
    /* ==================================================== *
     * ABSTRACT methods (Implementation) - End
     * ==================================================== */
    
    /**
     * Htn attribute.
     * This accessor cleans category from object name.
     *
     * @example "Hotel Delfin 3*" is transformed to "Hotel Delfin"
     * @return string
     */
    public function getHtnAttribute() {
        return Common::removeObjectCategory($this->attributes['htn']);
    }
    
    /**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        if (!empty($this->vpcs))
	        $array['vpcs'] = $this->vpcs->toArray();
        
        if (!empty($this->zacs))
        	$array['zacs'] = $this->zacs->toArray();
        
        if (!empty($this->tocs))
        	$array['tocs'] = $this->tocs->toArray();
        
        if (!empty($this->tdcs))
        	$array['tdcs'] = $this->tdcs->toArray();
        
        if (!empty($this->subs))
        	$array['subs'] = $this->subs->toArray();
        
        if (!empty($this->sids))
        	$array['sids'] = $this->sids->toArray();
        
        if (!empty($this->vnds))
        	$array['vnds'] = $this->vnds->toArray();
        
        if (!empty($this->facts))
        	$array['facts'] = $this->facts->toArray();
        
        if (!empty($this->offers))
        	$array['offers'] = $this->offers->toArray();
        return $array;
    }
}