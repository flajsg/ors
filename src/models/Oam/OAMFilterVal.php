<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Filter value
 *
 * This is a model for OAMFilter values.
 */

class OAMFilterVal extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	// Filter id code (what is sent through api)
    	'code', 
    	
    	// Filter value (what user can see)
    	'value', 
    	
    	// Filter name (toc, tdc, zac, vpc, vnd, ...)
    	'name',
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'code';
    
}