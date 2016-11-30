<?php namespace Ors\Orsapi\Interfaces;


/**
 * Abstract class for basic ORS API calls.
 *  
 * @author Gregor Flajs
 *
 */
interface ReservationsApiInterface {

	/**
	 * Return bookings
	 *
	 * @param SmartSearchParameters $params
	 * @param array $filters
	 * @param array $search
	 * 		common search options array('op')
	 * @return Collection|\ORM\ORM[]
	 */
	public function search($params, $filters, $search = array());
	
	/**
	 * Return totals.
	 *
	 * This request returns some statistics of bookings.
	 * Like total number of bookings, total price, persons, services, ....
	 *
	 * @param SmartSearchParameters $params
	 * @param array $filters
	 * @param array $search
	 * 		common search options array('op')
	 * @return \Bookings\BookingTotals
	 */
	public function totals($params, $filters, $search = array());
	
	/**
	 * Return booking history (for one or more bookings at once).
	 *
	 * @param SmartSearchParameters $params
	 * @param array $bookings
	 * 		a list of booking ids to get history for
	 * @return Collection|\Bookings\BookingHistory[]
	 */
	public function history($params, $bookings);
	
	/**
	 * Changes owner (booking agent) on bookings to another agent.
	 * 
	 * This api uses same search query as "search" method to find bookings
	 * for wish to want to change the owner. 
	 *
	 * @param SmartSearchParameters $params
	 * @param int $owner
	 * 		new owner user id (new agent)
	 * @param array $filters
	 * @param array $search
	 * 		common search options array('op')
	 * @return int
	 * 		 On success number of affected bookings is returned. 
	 */
	public function chown($params, $owner, $filters, $search = array());
	
}
