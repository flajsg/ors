<?php namespace Ors\Orsapi;


class ObjectInfoApiWrapper extends SearchApiWrapper {
	
	/**
	 * @return \Ors\Orsapi\Handlers\ObjectInfoHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	public function info($params) {
	    return $this->handler()->info($params);
	}
	
	public function infoToc($params) {
	    return $this->handler()->infoToc($params);
	}
}