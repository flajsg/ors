<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for handlers that will return Object info (description, media, geo, weather,...)
 *  
 * @author Gregor Flajs
 */
interface ObjectInfoApiInterface {

	/**
	 * Return Object info
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		search parameters
	 *
	 * @return \Ors\Orsapi\Oam\OAMInfo
	 */
	public function info($params);
	
	/**
	 * Return Object info per selected touroperator
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		search parameters
	 *
	 * @return \Ors\Orsapi\Oam\OAMObjectInfo
	*/
	public function infoToc($params);
	
}