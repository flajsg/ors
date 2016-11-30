<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;

/**
 * ORS API Model: Object Fact
 *
 * This model represents Object fact (main characteristic). 
 * It extends OAMObjectContent with additional attributes that represent icon and fact name.
 */

class OAMObjecFact extends OAMObjectContent {

	/**
	 * Icon Attribute.
	 * Different facts have different icon class (some of them may have the same icon or null value.
	 * @return OAMIcon|null
	 */
	public function getIconAttribute() {
		if (empty($this->attributes['status']))
			return null;
		return Common::factIcon($this->attributes['code']);
	}
    
	public function toArray() {
		$array = parent::toArray();
		$array['icon'] = $this->icon ? $this->icon->toArray() : null;
		return $array; 
	}
}