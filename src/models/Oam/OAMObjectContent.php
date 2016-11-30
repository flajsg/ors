<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Object content
 *
 * It contains different type of contents that OAMObject can have (zacs, vpcs, tocs, facts, ...)
 */

class OAMObjectContent extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'code', 'value', 'status'
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'code';
    
}