<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Availability Extras item
 *
 * This is extra service that can be bookable with checked offer.
 */

class OAMAvailabilityExtras extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'id', 'opt', 'op2', 'name', 'price', 'ppc', 'price_type', 'min_age', 'max_age', 'included', 'persons', 'dateFrom', 'dateTo'
	];
   
    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'id';
    
}