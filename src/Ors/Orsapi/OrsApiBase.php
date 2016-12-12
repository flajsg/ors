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
	 * @var \Ors\Orsapi\Handlers\BaseHandler
	 */
	protected $oa_handler;
	
	/**
	 * Instance contructor
	 * @param \Ors\Orsapi\Handlers\BaseHandler $oa_handler
	 */
	public function __construct($oa_handler) {
		$this->oa_handler = $oa_handler;
	}
	
	/**
	 * Ors Api Handler object
	 * @return \Ors\Orsapi\Handlers\BaseHandler
	 */
	public function handler() {
		return $this->oa_handler;
	}
	
	/**
	 * Return API response header
	 * @return \Ors\Orsapi\Oam\OAMHeader
	 */
	public function header() {
		return $this->handler()->getApiHeader();
	}
	
	/**
	 * Set different api handler
	 * @param \Ors\Orsapi\Handlers\BaseHandler $oa_handler
	 */
	public function setHandler($oa_handler) {
		$this->oa_handler = $oa_handler;
	}
	
	/**
	 * @return \Ors\Orsapi\OrsApiBase
	 */
	public function setAgencyKey($agency, $ibeid=0, $master_key) {
	    $this->handler()->setAgencyKey($agency, $ibeid, $master_key);
	    return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\OrsApiBase
	 */
	public function setLogin($agency, $ibeid=0, $usr, $pass) {
	    $this->handler()->setLogin($agency, $ibeid, $usr, $pass);
	    return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\OrsApiBase
	 */
	public function setAuthLogin($auth) {
	    $this->handler()->setAuthLogin($auth);
	    return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\OrsApiBase
	 */
	public function setIbeid($ibeid) {
	    $this->handler()->setIbeid($ibeid);
	    return $this;
	}
	
}