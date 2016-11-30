<?php namespace Ors\Orsapi\Bookings;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Model for booking totals
 *
 * Statistics received from booking_>totals api.
 *
 * @author Gregor Flajs
 *
 */
class BookingTotals extends Eloquent {
	
	protected $fillable = ['passenger_count', 'total_price', 'bookings_count', 'services_count', 'matched_services', 'matched_service_price', 'matched_service_passengers'];
	
	/**
	 * Return true if model has information about matched services totals.
	 * @return boolean
	 */
	public function hasExtraTotals() {
		return $this->attributes['services_count'] != $this->attributes['matched_services']; 
		return isset($this->attributes['matched_services']) || isset($this->attributes['matched_service_price']) || isset($this->attributes['matched_service_passengers']);
	}
}