<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;

/**
 * ORS API Model: Offer (or trip)
 *
 * This a single offer of selected ORS Object.
 * Each offer is for specific date, room, service, ... and contains a price per adult person per whole period.
 */

class OAMOffer extends OAMOfferAbstract {

	
    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	// object info
    	'ctype_id', 'gid', 'rgc', 'oid',
    	
    	// touroperator
    	'toc', 'ton', 'tocpic',
    	
    	// offer
    	'htn', 'hon', 'stc', 'sub',  'zac', 'zan', 'vpc', 'vpn', 'htc', 'svc', 'vnd', 'bsd', 'tdc', 'ahc', 'ahn', 'zhc', 'zhn', 'sin', 'sid', 'status',
    	'stock', 'stock_txt',

    	// offer details
    	'aid', 'atx', 'iid', 'itx', 'lid', 'ltx', 'vid', 'vtx', 'zid', 'ztx',
    	
    	// prices
    	'ppc', 'cppc', 
    	
    	// extras
    	'alt', 'top',

    	// hash codes
    	'md5p', 'md5t', 'hsc', 'uhsc'
	];

    public function __construct($attributes = array()) {
    	
    	// This is used in some jQuery selectors, because hsc can consist of some invalid characters for jQuery selectors.
    	$attributes['uhsc'] = Common::makeUniqueHash();
    	
    	parent::__construct($attributes);
    }
    
    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'hsc';

    
    /**
     * Details Attribute.
     * This returns offer details, separated by /
     * @return string
     */
    public function getDetailsAttribute() {
        return 'N/A';
    }
    
    /*
     * CART ACCESSORS
     */
        
    /**
     * Cart Offer Details attribute.
     * This is used to display Offer details in Cart list view.
     * @return string
     */
    public function getCartOfferDetailsAttribute() {
        return Common::date($this->attributes['vnd']);
    }
}