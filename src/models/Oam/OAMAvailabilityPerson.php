<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Availability Person
 *
 * Person (traveler) info from availability response
 */

class OAMAvailabilityPerson extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'id', 'typ', 'sur', 'pre', 'tvp', 'age',
	];
   
    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        $array['person_title_sm'] = $this->person_title_sm;
        return $array;
    }
}