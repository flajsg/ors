<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for Passenger API handler
 * 
 * @author Gregor Flajs
 *
 */
interface PassengerApiInterface {

	/**
	 * Add new passenger
	 *
	 * @param array $passenger
	 *
	 * @return bool|int
	 * 		on success, a passenger ID is returned or false if api call failed
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function add($passenger);
	
	/**
	 * Edit existing passenger
	 *
	 * @param array $passenger
	 *
	 * @return bool
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function update($passenger);
	
	/**
	 * Quick search for passengers
	 *
	 * @param string $term
	 * 		search term (keywords)
	 * @param array $options
	 * 		see documentation for all available options
	 *
	 * @return Collection|\Ors\Orsapi\Orm\ORMPassenger[]
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function search($term, $options = array());
	
	/**
	 * Get All passengers
	 *
	 * @param array $options
	 * 		see documentation for all available options
	 *
	 * @return Collection|\Ors\Orsapi\Orm\ORMPassenger[]
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function all($options = array());
	
	/**
	 * Quick search for passengers
	 *
	 * @param array $ids
	 * 		passengers ids
	 *
	 * @return Collection|\Ors\Orsapi\Orm\ORMPassenger[]
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function findIds($ids);
	
	/**
	 * Find a passenger by id
	 *
	 * @param int $id
	 * 		passenger id
	 *
	 * @return \Ors\Orsapi\Orm\ORMPassenger
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function find($id);
	
	/**
	 * Delete a passenger by id (or multiple ids)
	 *
	 * @param array|string $id
	 * 		a array of ids or a comma seperated string of ids
	 *
	 * @return bool
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function delete($ids);
	
	/**
	 * Link passengers
	 *
	 * @param int $id
	 * 		original passenger id for which we are creating links
	 * @param array|string $linked_ids
	 * 		a array of ids or a comma seperated string of ids
	 * @param array $options
	 * 		see documentation for all available options
	 *
	 * @return bool
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function link($id, $linked_ids, $options = array());
	
	/**
	 * Un-Delete a passenger
	 *
	 * @param array|string $id
	 * 		a array of ids or a comma seperated string of ids
	 *
	 * @return bool
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function undelete($ids);
	
	/**
	 * Un-Link passengers
	 *
	 * @param int $id
	 * 		original passenger id
	 * @param array|string $linked_ids
	 * 		a array of ids or a comma seperated string of ids
	 * @param array $options
	 * 		see documentation for all available options
	 *
	 * @return bool
	 * @throws \Ors\Orsapi\OrsApiException
	*/
	public function unlink($linked_ids, $options = array());
}