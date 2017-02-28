<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;

/**
 * Bus model (for ORS Roundtrips)
 *
 * Model consist of bus id,name, and bus seats.
 * 
 * @author Gregor Flajs
 */
class OAMBus extends Eloquent {
	
	protected $fillable = ['id', 'name', 'seats'];
	
	/**
	 * Bus seats
	 * @var Collection
	 */
	
	public function __construct($attributes = array()) {
	    parent::__construct($attributes);
	}
	
	/**
	 * Bus seats
	 * @return OAMBusSeat|Collection
	 */
	public function getSeatsAttribute() {
	    if (empty($this->attributes['seats'])) return new Collection();
	
	    $seats = new Collection();
	    
	    foreach ($this->attributes['seats'] as $seat)
	    	$seats->push(new OAMBusSeat($seat));
	    
	    return $seats;
	}
	
	/**
	 * Organize seats into two dimensional array (x,y).
	 * 
	 * @return array
	 */
	public function getSeats2CoordinatesAttribute() {
		if ($this->seats->isEmpty()) return array();
		
		$seats = $this->seats;
		$seats_coords = array();
		
		foreach ($seats as $seat) $seats_coords[$seat->x][$seat->y] = $seat;
		
		return $seats_coords;
	}
} 

/**
 * Bus seat model. 
 * 
 * Consists of:
 * - x,y coordinats, 
 * - seat number, 
 * - free status (is seat available or not),
 * - price (what is a surcharge for this seat).
 * 
 * @author Gregor Flajs
 *
 */
class OAMBusSeat extends Eloquent {
	
	protected $fillable = ['x', 'y', 'seat', 'free', 'price'];
	
	/**
	 * Return true if seat is available
	 * @return boolean
	 */
	public function isFree(){
		return (bool)$this->free;
	}
	
}