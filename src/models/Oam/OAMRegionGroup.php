<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: RegionGroup
 * 
 * This is a model for each region group. Data are providet from OrsApi call.
 */

class OAMRegionGroup extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['name', 'rgc', 'ppc', 'region'];
	
	/**
	 * Primary key
	 * @var int
	 */
	protected $primaryKey = 'rgc';
	
	/**
	 * A list of regions for this group
	 * @var Collection|OAMRegion[]
	 */
	public $regions;
	
	/**
	 * Name attribute (Region group name)
	 * @return string
	 */
	public function getNameAttribute() {
		return $this->attributes['region'];
	}
	
	/**
	 * Override toArray to add additional attributes
	 * @return array
	 */
	public function toArray(){
		$array = parent::toArray();
		$array['regions'] = $this->regions->toArray();
		$array['name'] = $this->name;
		return $array; 
	}
}