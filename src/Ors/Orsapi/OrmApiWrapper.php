<?php namespace Ors\Orsapi;

use Ors\Orsapi\OrsApiBase;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\OrmApiInterface;


/**
 * ORS API ORM implementation class (for handling bookings in ORS).
 * 
 * ORS API handler must implement OrsApi_orm_handler class.
 * 
 * @author Gregor Flajs
 *
 */
class OrmApiWrapper extends OrsApiBase {

	/**
	 * Create wrapper
	 * @param OrmApiInterface $oa_handler
	 * @throws OrsApiException
	 */
	public function __construct(OrmApiInterface $oa_handler) {
	    if ($oa_handler instanceof OrmApiInterface)
	        parent::__construct($oa_handler);
	    else
	        throw new OrsApiException('Invalid handler!');
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	/**
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setAgencyKey($agency, $ibeid=0, $master_key) {
	    return $this->handler()->setAgencyKey($agency, $ibeid, $master_key);
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setLogin($agency, $ibeid=0, $usr, $pass) {
	    return $this->handler()->setLogin($agency, $ibeid, $usr, $pass);
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\OrmApiHandler
	 */
	public function setAuthLogin($auth) {
	    return $this->handler()->setAuthLogin($auth);
	}
	
	public function orm($params, $orm) {
		return $this->handler()->orm($params, $orm);
	}
		
}