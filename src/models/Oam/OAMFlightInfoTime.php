<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Ors\Support\Common;

/**
 * ORS API Model: FlightInfoTime
 *
 * This is model for detailed Flight Information. 
 * From this you can get the exact flight arrival/departure times, flight number and carriers. 
 * 
 * @author Gregor Flajs
 */

class OAMFlightInfoTime extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = [
		'id', 'fla', 'ahc', 'ahn', 'zhc', 'zhn', 'flc', 'fln', 'dpd', 'dpt', 'ard', 'art', 'logo'
	];
	
	/**
	 * Primary key
	 * @var string
	 */
	protected $primaryKey = 'id';
	
	/**
	 * Dpt attribute (departure time) - formated
	 * @return string
	 */
	public function getDptAttribute() {
		return Common::toTime($this->attributes['dpt']);
	}
	
	/**
	 * art attribute (arrival time) - formated
	 * @return string
	 */
	public function getArtAttribute() {
		return Common::toTime($this->attributes['art']);
	}
}