<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for Search API handlers.
 *  
 * @author Gregor Flajs
 *
 */
interface SearchApiInterface{

	/**
	 * Return collection of regions/groups.
	 * @param string $ctype_id
	 * 		content type id
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		smart search parameters object
	 * 
	 * @return Collection|\Ors\Orsapi\Oam\OAMRegionGroup[]
	 */
	public function regions($ctype_id, $params);
	
	/**
	 * Return collection of ORS Objects.
	 * @param string $ctype_id
	 * 		content type id
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		smart search parameters object
	 *
	 * @return Collection|\Ors\Orsapi\Oam\OAMObject[]
	 */
	public function objects($ctype_id, $params);
	
	/**
	 * Return single ORS Object with offers.
	 * @param string $ctype_id
	 * 		content type id
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		smart search parameters object
	 *
	 * @return \Ors\Orsapi\Oam\OAMObject
	 */
	public function object($ctype_id, $params);
	
	/**
	 * Return single ORS Object with all possible rooms.
	 * 
	 * This request is "special" because it is used only for dhotel content type.
	 * 
	 * @param string $ctype_id
	 * 		content type id
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		smart search parameters object
	 * 
	 * @return \Ors\Orsapi\Oam\OAMObject
	 */
	public function units($ctype_id, $params);
	
	/**
	 * Availability check
	 * @param string $ctype_id
	 * 		content type id
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		smart search parameters object
	 *
	 * @return \Ors\Orsapi\Oam\OAMAvailability
	 */
	public function availability($ctype_id, $params);
	
	/**
	 * Use this method to set OAM filters after request is made.
	 * This can only be called inside request methods. 
	 * Use getFilters() method to return available filters. 
	 * @param mixed $response
	 */
	public function setFilters($response);
	
	/**
	 * Return OAM filters collection
	 * @return Collection|\Ors\Orsapi\Oam\OAMFilter
	 */
	public function getFilters();
	
	/**
	 * Use this method to set OAM sorting information after request is made.
	 * This can only be called inside request methods. 
	 * Use getSorts() method to return results. 
	 * @param mixed $response
	 */
	public function setSorts($response);
	
	/**
	 * Return OAM sorts collection
	 * @return Collection|\Ors\Orsapi\Oam\OAMSort
	 */
	public function getSorts();
	
}