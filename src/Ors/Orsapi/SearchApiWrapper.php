<?php namespace Ors\Orsapi;

use Ors\Orsapi\OrsApiBase;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\ITAG_SearchApiInterface;


/**
 * ORS API search wrapper class.
 * 
 * Search requests are requests that returns: regions, object-info, possible offers, flight-info, ...
 *
 * @author Gregor Flajs
 *
 */
class SearchApiWrapper extends OrsApiBase {
	
	/**
	 * Content type id
	 * @var string
	 */
	protected $ctype_id;
	
	/**
	 * Create wrapper
	 * 
	 * @param ITAG_SearchApiInterface $oa_handler
	 * 		A handler for ors search-api requests. 
	 * 
	 * 		Because you can have multiple handlers that parses ORS search requests, 
	 * 		it is a good think that all those handlers implement a simple "tag" interface "ITAG_SearchApiInterface". 
	 * 		This is how wrapper knows if you are using the correct handler.
	 * 
	 * @throws OrsApiException
	 */
	public function __construct($oa_handler) {
	    if ($oa_handler instanceof ITAG_SearchApiInterface)
	        parent::__construct($oa_handler);
	    else
	        throw new OrsApiException('Invalid handler!');
	}
	
	/**
	 * Set content type id
	 * @param string $ctype_id
	 * @return \Ors\Orsapi\SearchApiWrapper
	 */
	public function ctypeId($ctype_id) {
		$this->ctype_id = $ctype_id;
		return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\SearchApiHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	/**
	 * @return \Ors\Orsapi\SearchApiWrapper
	 */
	public function setLogin($agency, $ibeid=0, $usr, $pass) {
	    $this->handler()->setLogin($agency, $ibeid, $usr, $pass);
	    return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\SearchApiWrapper
	 */
	public function setAuthLogin($auth) {
	    $this->handler()->setAuthLogin($auth);
	    return $this;
	}
	
	/**
	 * @return \Ors\Orsapi\SearchApiWrapper
	 */
	public function setIbeid($ibeid) {
	    $this->handler()->setIbeid($ibeid);
	    return $this;
	}
}