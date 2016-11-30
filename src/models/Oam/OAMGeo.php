<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Geo
 *
 * This is a model is used for geo locations. 
 * It contains latitude an longitude coordinates, that can be used with google map api.
 */

class OAMGeo extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'lat', 'lon', 'title', 'content'
	];
    
}