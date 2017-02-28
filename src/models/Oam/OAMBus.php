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
	 * Organize seats into x,y coordinate system.
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
	
	/**
	 * Organize seats into y,x coordinate system
	 * 
	 * @return array
	 */
	public function getSeats2YCoordinatesAttribute() {
		if ($this->seats->isEmpty()) return array();
		
		$seats = $this->seats;
		$seats_coords = array();
		
		foreach ($seats as $seat) $seats_coords[$seat->y][$seat->x] = $seat;
		
		return $seats_coords;
	}
	
	/*
	 * HELPERS
	 */
	
	/**
	 * Return bus seat by x,y coordinate.
	 * 
	 * @param int $x
	 * @param int $y
	 * @return OAMBusSeat
	 */
	public function seatByCoordinate($x, $y) {
		$seat = $this->seats->filter(function($item) use($x, $y) {
			return $item->x == $x && $item->y == $y;
		});
		
		return $seat->first();
	}
	
	/**
	 * Max Y coordinate.
	 * @return int
	 */
	public function maxRows() {
		return $this->seats->reduce(function($carry, $item){
			return $item->y > $carry ? $item->y : $carry; 
		}, 0);
	}
	
	/**
	 * Max X coordinate.
	 * @return int
	 */
	public function maxColls() {
		return $this->seats->reduce(function($carry, $item){
			return $item->x > $carry ? $item->x : $carry; 
		}, 0);
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