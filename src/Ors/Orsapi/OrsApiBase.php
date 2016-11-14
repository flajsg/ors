<?php namespace Ors\Orsapi;

/**
 * ORS API base class.
 * 
 * Extend this class when you are doing implementation classes or wrappers.
 * 
 * @author Gregor Flajs
 *
 */
class OrsApiBase {
	
	/**
	 * Ors Api Handler
	 * @var \Ors\Orsap\Handlers\BaseHandler
	 */
	protected $oa_handler;
	
	/**
	 * Instance contructor
	 * @param \Ors\Orsap\Handlers\BaseHandler $oa_handler
	 */
	public function __construct($oa_handler) {
		$this->oa_handler = $oa_handler;
	}
	
	/**
	 * Ors Api Handler object
	 * @return \Ors\Orsap\Handlers\BaseHandler
	 */
	public function handler() {
		return $this->oa_handler;
	}
	
	/**
	 * Return API response header
	 * @return \OAM\OAMHeader
	 */
	public function header() {
		return $this->handler()->getApiHeader();
	}
	
	/**
	 * Set different api handler
	 * @param \Ors\Orsap\Handlers\BaseHandler $oa_handler
	 */
	public function setHandler($oa_handler) {
		$this->oa_handler = $oa_handler;
	}
}