<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Region
 * 
 * This is a model for each region. Data are providet from OrsApi call.
 */

class OAMRegion extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['name', 'rgc', 'ppc', 'rgn'];
	
	/**
	 * Name attribute (Region name)
	 * @return string
	 */
	public function getNameAttribute() {
	    return $this->attributes['rgn'];
	}
	
	/**
	 * Primary key
	 * @var int
	 */
	protected $primaryKey = 'rgc';
}