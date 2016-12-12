<?php namespace Ors\Orsapi; 

use Ors\Orsapi\OrsApiBase;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\PassengerApiInterface;
use Ors\Support\Common;

/**
 * PassengerAPI is an API you can use to access stored list of passengers in ORS for specific agency.  
 *
 * This is just a wrapper for API handler.
 * 
 * @author Gregor Flajs
 *
 */
class PassengerApiWrapper extends OrsApiBase {
	
	/**
	 * Create wrapper
	 * @param PassengerApiInterface $oa_handler
	 * @throws OrsApiException
	 */
	public function __construct(PassengerApiInterface $oa_handler) {
		if ($oa_handler instanceof PassengerApiInterface)
	    	parent::__construct($oa_handler);
		else
			throw new OrsApiException('Invalid handler!');
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\PassengerApiHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	public function add($passenger) {
	    return $this->handler()->add($passenger);
	}
	
	public function update($passenger) {
	    return $this->handler()->update($passenger);
	}
	
	public function search($term, $options = array()) {
	    return $this->handler()->search($term, $options);
	}
	
	public function all($options = array()) {
	    return $this->handler()->all($options);
	}
	
	public function findIds($ids) {
	    return $this->handler()->findIds($ids);
	}
	
	public function find($id) {
	    return $this->handler()->find($id);
	}
	
	public function delete($ids) {
	    return $this->handler()->delete($ids);
	}
	
	public function link($id, $linked_ids, $options = array()) {
	    return $this->handler()->link($id, $linked_ids, $options);
	}
	
	public function undelete($ids) {
	    return $this->handler()->undelete($ids);
	}
	
	public function unlink($linked_ids) {
	    return $this->handler()->unlink($linked_ids);
	}
}