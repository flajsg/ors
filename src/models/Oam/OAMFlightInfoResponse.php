<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: flightInfo Response
 *
 * Response data from flight info request
 * 
 * @author Gregor Flajs
 */

class OAMFlightInfoResponse extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'mid', 'txt', 'status'
	];

    /**
     * Return TRUE if flight info is available.
     * @return boolean
     */
    public function isInfoAvailable() {
    	return $this->attributes['status'] == 1;
    }
    
}