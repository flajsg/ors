<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Ors\Support\Common;


/**
 * ORS API Model: Cart
 *
 * This model holds information for Smart Cart.
 */
class OAMCart extends OAMAvailability{
	
	/**
	 * Constructor from array
	 * @param array|json $data
	 * 		response,offer,operator
	 * @return \OAM\OAMCart
	 */
	public static function withArray($data) {
		
		$instance = parent::withArray($data);

		// check if $data is json
		if (Common::isJson($data))
		    $data = json_decode($data, true);
		
	    // Object model
	    $object_class = "OAM\OAMObject_{$data['ctype_id']}";
	    
	    if (class_exists($object_class))
		    $instance->object = new $object_class( (!empty($data['object']) ? $data['object'] : $data['offer']) );
	    else
		    $instance->object = new OAMObject( (!empty($data['object']) ? $data['object'] : $data['offer']) );
	    
	    return $instance;
	}

	/**
	 * Override toArray to add additional attributes
	 * @return array
	 */
	public function toArray() {
		$array = parent::toArray();
		unset($array['response']['ttp_check']);
		unset($array['response']['ppc_check']);
		return $array;
	}
	
}