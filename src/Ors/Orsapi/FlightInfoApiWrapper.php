<?php namespace Ors\Orsapi;


class FlightInfoApiWrapper extends SearchApiWrapper {
	
	/**
	 * @return \Ors\Orsapi\Handlers\FlightInfoHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	public function flightInfo($params) {
	    return $this->handler()->flightInfo($params);
	}
	
}