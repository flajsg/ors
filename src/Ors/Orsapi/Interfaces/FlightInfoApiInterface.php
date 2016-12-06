<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for handlers that will return Flight info (flight times, cariers, connected flights,...)
 *  
 * @author Gregor Flajs
 */
interface FlightInfoApiInterface {
	
	/**
	 * FlightInfo check
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		search parameters
	 *
	 * @return \Ors\Orsapi\Oam\OAMFlightInfo
	 */
	public function flightInfo($params);
	
}