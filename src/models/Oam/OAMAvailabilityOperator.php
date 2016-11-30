<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Availability Operator
 *
 * This are operator info from availability response
 */

class OAMAvailabilityOperator extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'ctype_id', 'act', 'toc', 'hsc', 'agt', 'ibeid'
	];
   
}