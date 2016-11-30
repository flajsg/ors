<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Availability Response
 *
 * Response info from availability response
 */

class OAMAvailabilityResponse extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'mid', 'ttp', 'ppc', 'ppct', 'txt', 'status', 'old_ppc', 'ttp_check', 'ppc_check'
	];

    /**
     * Return TRUE if offer is bookable, or FALSE if not (offer is not available, or some other error).
     * Offer is bookable when status is either 1 or 2.
     * @return boolean
     */
    public function isBookable() {
    	return in_array($this->attributes['status'], array(1,2));
    }
    
    /**
     * Return TRUE if offer is free (status B)
     * @return boolean
     */
    public function isFree() {
    	return $this->attributes['status'] == 1;
    }
    
    /**
     * Return TRUE if offer is on request (status RQ)
     * @return boolean
     */
    public function isOnRequest() {
    	return $this->attributes['status'] == 2;
    }
    
	/**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        $array['is_free'] = $this->isFree();
        $array['is_bookable'] = $this->isBookable();
        $array['is_on_request'] = $this->isOnRequest();
        return $array;
    }
}