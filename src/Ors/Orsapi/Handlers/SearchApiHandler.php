<?php namespace Ors\Orsapi\Handlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Ors\Support\Common;
use Ors\Orsapi\Interfaces\ITAG_SearchApiInterface;
use Ors\Orsapi\Interfaces\SearchApiInterface;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Oam\OAMAuth;
use Ors\Orsapi\Oam\OAMFilter;
use Ors\Orsapi\Oam\OAMSort;
use Ors\Orsapi\Oam\OAMFilterVal;
use Ors\Orsapi\Oam\OAMObject;
use Ors\Orsapi\Oam\OAMObjectContent;
use Ors\Orsapi\Oam\OAMObjecFact;
use Ors\Orsapi\Oam\OAMRegion;
use Ors\Orsapi\Oam\OAMRegionGroup;
use Ors\Orsapi\Oam\OAMOffer;
use Ors\Orsapi\Oam\OAMAvailability;
use Ors\Orsapi\Oam\OAMAvailabilityOperator;
use Ors\Orsapi\Oam\OAMAvailabilityResponse;
use Ors\Orsapi\Oam\OAMAvailabilityPerson;
use Ors\Orsapi\Oam\OAMAvailabilityExtras;
use Ors\Orsapi\Oam\OAMAvailabilityService;


/**
 * 
 * This is ORS API Soap hander. Class works with ORS SOAP API and returns needed data.
 * 
 * @author Gregor Flajs
 *
 */
class SearchApiHandler extends SoapApiBaseHandler implements ITAG_SearchApiInterface, SearchApiInterface {
	
	/**
	 * Objects per page
	 * @var int
	 */
	protected $objects_per_page;
	
	/**
	 * Offers per page
	 * @var int
	 */
	protected $trips_per_page;
	
	/**
	 * A collection of OAM filters
	 * @var Collection|\Ors\Orsapi\Oam\OAMFilter[]
	 */
	protected $filters;
	
	/**
	 * A collection of OAM sorting objects
	 * @var Collection|\Ors\Orsapi\Oam\OAMSort[]
	 */
	protected $sorts;
	
	/**
	 * Construct Soap client object and set api language

	 * @param OAMAuth $auth
	 * 		orm api auth. credentials
	 */
	public function __construct(OAMAuth $auth = null){
		parent::__construct($auth);
				
		$this->objects_per_page = 30;
		$this->trips_per_page = 50;
		
	}
	
	public function setObjectsPerPage($num) {
		$this->objects_per_page = $num;
	}
	
	public function setTripsPerPage($num) {
		$this->trips_per_page = $num;
	}
	
	/**
	 * Return max objects per page
	 * @return int
	 */
	public function getObjectsPerPage() {
	    return $this->objects_per_page;
	}
	
	/**
	 * Return max trips per page
	 * @return int
	 */
	public function getTripsPerPage() {
	    return $this->trips_per_page;
	}

	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::getFilters()
	 */
	public function getFilters() {
	    return $this->filters;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::getSorts()
	 */
	public function getSorts() {
	    return $this->sorts;
	}
	
	/**
	 * Override SoapHeaderTrait::__makeHeader() to add some additional header info.
	 * @param unknown $params
	 */
	protected function _makeHeader($params) {
		$params = $this->toSmartParams($params);
		parent::_makeHeader($params);
		
		// get unique request id (or create it if not exists)
		if ($params->has('uniqid'))
			$uniqid = $params->find('uniqid')->value;
		else
			$uniqid = Common::makeUniqueHash();
		
		// session id (uniqid)
		$auth_method = Config::get('orsapi::auth_method');
		if ($auth_method)
			$this->header['sid'] = 'smr_'.$auth_method->id.'_'.$uniqid;
		else
			$this->header['sid'] = 'smr_'.$uniqid;
		$this->sid = $this->header['sid'];
		
		// set prefered toc (tocp)
		//if (!empty($params->find('toc')->value))
		//foreach (Common::extrim($params->find('toc')->value) as $toc)
		//    $this->header['tocp'] = strtoupper(trim($toc));
		
		$this->header['limits'] = "p:{$this->objects_per_page},t:{$this->trips_per_page}";
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::regions()
	 */
	public function regions($ctype_id, $params) {
		$params = $this->toSmartParams($params);
		
		$this->_makeHeader($params);
		
		// make api call
		$call = "orsxml_{$ctype_id}_api_call";
		$response = $this->orsSoapClient->$call( 'regions', $params->__toArray(), $this->header );
		
		// debug xmlReq
		Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
		
		// check for error
		$this->_error($response);
		
		// set request id (rqid)
		$this->setRqid($response);
		
		// set header
		$this->setApiHeader($response['header']);
		
		// debug
		Common::ppreDebug( $this->header, 'header');
		//Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		//Common::ppreDebug( $response['xmlReq'], 'xmlReq-raw');
		//Common::ppreDebug( $response, 'Response');
		
	
		// create and return a collection
		$collection = new Collection();
		
		foreach ($response['regions'] as $item) {
			$rg_model = new OAMRegionGroup($item);
			$rg_model->regions = new Collection();
			
			foreach ($item['offer'] as $region) {
				$rg_model->regions->push(new OAMRegion($region));
			}
			
			$collection->push($rg_model);
		};
		
		return $collection;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::objects()
	 */
	public function objects($ctype_id, $params) {
		$params = $this->toSmartParams($params);
		
		$this->_makeHeader($params);
		
		// make api call
		$call = "orsxml_{$ctype_id}_api_call";
		
		$response = $this->orsSoapClient->$call( 'offers', $params->__toArray(), $this->header );
		
		// debug xmlReq
		Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
		//Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
		//Common::ppreDebug( $response, 'Response');
	
		// check for error
		$this->_error($response);

		// set request id (rqid)
		$this->setRqid($response);
		
		// set header
		$this->setApiHeader($response['header']);
		
		// debug header
		Common::ppreDebug( $this->header, 'header');
		
		// create and return a collection
		$collection = new Collection();
		
		$object_class = "Ors\Orsapi\Oam\OAMObject_{$ctype_id}";
		
		foreach ($response['offers'] as $item) {
			
			if (class_exists($object_class))
		    	$o_model = new $object_class($item+array('ctype_id' => $ctype_id));
			else
		    	$o_model = new OAMObject($item);
			
		    $this->_addObjectContent($item, 'vpcs', $o_model);
		    $this->_addObjectContent($item, 'zacs', $o_model);
		    $this->_addObjectContent($item, 'tocs', $o_model);
		    
		    $o_model->facts = new Collection();
		    if (!empty($item['facts']))
		    foreach ($item['facts'] as $fct) {
		        $o_model->facts->push(new OAMObjecFact($fct));
		    }
		    
		    $collection->push($o_model);
		};
		
		// set filters
		$this->setFilters($response);
		
		// set sorts
		$this->setSorts($response);
		
		return $collection;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::object()
	 */
	public function object($ctype_id, $params) {
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_{$ctype_id}_api_call";
	    $response = $this->orsSoapClient->$call( 'trips', $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    //Common::ppreDebug( $response, 'Response');
	
	    // check for error
	    $this->_error($response);

	    // set request id (rqid)
	    $this->setRqid($response);
	     
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	
	    // Object model
	    $object_class = "Ors\Orsapi\Oam\OAMObject_{$ctype_id}";
	    if (class_exists($object_class))
	    	$o_model = new $object_class($response['offer']+array('ctype_id' => $ctype_id));
	    else
	    	$o_model = new OAMObject($response['offer']+array('ctype_id' => $ctype_id));
	    
	    // Parse offer facts string
	    if (!empty($response['offer']['fcts'])) {
	    	$o_model->facts = new Collection();
	    	$facts = explode(',', $response['offer']['fcts']);
	    	foreach ($facts as $fact) {
	    		list($code, $status) = explode('=', $fact);
	    		$o_model->facts->push(new OAMObjecFact(array('code' => $code, 'status' => $status)));
	    	}
	    }
	    
	    // create and return a collection
	    $o_model->offers = new Collection();
	
	    $offer_class = "Ors\Orsapi\Oam\OAMOffer_{$ctype_id}";
	    foreach ($response['trips'] as $item) {
	    	
	    	if (class_exists($offer_class))
	        	$o_model->offers->push(new $offer_class($item+array('ctype_id' => $ctype_id)));
	    	else
	        	$o_model->offers->push(new OAMOffer($item+array('ctype_id' => $ctype_id)));
	    };
	
	    // set filters
	    $this->setFilters($response);
	
	    // set sorts
	    $this->setSorts($response);
	
	    return $o_model;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::units()
	 */
	public function units($ctype_id, $params) {
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_{$ctype_id}_api_call";
	    $response = $this->orsSoapClient->$call( 'units', $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    //Common::ppreDebug( $response, 'Response');
	    //Common::ppreDebug( $response['tocs'][0]['rooms'], 'Response');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	    
	    // Object model
	    $object_class = "Ors\Orsapi\Oam\OAMObject_{$ctype_id}";
	    if (class_exists($object_class))
	        $o_model = new $object_class($response['offer']+array('ctype_id' => $ctype_id));
	    else
	        $o_model = new OAMObject($response['offer']+array('ctype_id' => $ctype_id));
	     
	    // create and return a collection
	    $o_model->offers = new Collection();

	    $offer_class = "Ors\Orsapi\Oam\OAMOffer_{$ctype_id}";
	    $toc_first = $response['tocs'][0]; 
	    foreach ($toc_first['rooms'] as $item) {
	
	        if (class_exists($offer_class))
	            $o_model->offers->push(new $offer_class($item['info']+array('ctype_id' => $ctype_id, 'toc' => $toc_first['info']['toc'], 'hsc' => $item['info']['code'])));
	        else
	            $o_model->offers->push(new OAMOffer($item['info']+array('ctype_id' => $ctype_id, 'toc' => $toc_first['info']['toc'], 'hsc' => $item['info']['code'])));
	    };
	
	    // set filters
	    $this->setFilters($response);
	
	    // set sorts
	    $this->setSorts($response);
	
	    return $o_model;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::availability()
	 */
	public function availability($ctype_id, $params) {
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_{$ctype_id}_api_call";
	    $response = $this->orsSoapClient->$call( 'check', $params->__toArray(), $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    Common::ppreDebug( htmlspecialchars($response['xmlRes']), 'xmlRes');
	    //Common::ppreDebug( $response, 'Response');
	
	    // check for error
	    $this->_error($response);

	    // set request id (rqid)
	    $this->setRqid($response);
	     
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	
	    $response['offer']['ctype_id'] = $ctype_id;
	    
	    // Availability model
	    $a_model = new OAMAvailability(array('hsc' => $params->find('hsc')->value, 'toc' => $params->find('toc')->value, 'info' => $response['info'], 'ctype_id' => $ctype_id));

	    // add Response
	    $a_model->response = new OAMAvailabilityResponse($response['response']+array('old_ppc' => $params->find('old_ppc')->value));
	    
	    // add Operator
	    $a_model->operator = new OAMAvailabilityOperator($response['operator']+array('ibeid' => $params->find('ibeid')->value));
	    
	    // add Object
	    $object_class = "Ors\Orsapi\Oam\OAMObject_{$ctype_id}";
	    if (class_exists($object_class))
	    	$a_model->object = new $object_class($response['offer']+array('ctype_id' => $ctype_id));
	    else
	    	$a_model->object = new OAMObject($response['offer']+array('ctype_id' => $ctype_id));
	    
	    // add Offer
	    $offer_class = "Ors\Orsapi\Oam\OAMOffer_{$ctype_id}";
	    if (class_exists($offer_class))
	    	$a_model->offer = new $offer_class($response['offer']+array('ctype_id' => $ctype_id));
	    else
	    	$a_model->offer = new OAMOffer($response['offer']+array('ctype_id' => $ctype_id));

	    // add Services
	    $a_model->services = new Collection();
	    if (!empty($response['services']))
	    foreach ($response['services'] as $item) {
	        $a_model->services->push(new OAMAvailabilityService($item));
	    };
	     
	    // add Persons
	    $a_model->persons = new Collection();
	    foreach ($response['travellers'] as $item) {
	        $a_model->persons->push(new OAMAvailabilityPerson($item));
	    };
	    
	    // add extras
	    $a_model->extras = new Collection();
	    if (!empty($response['extras'])) {
	    	foreach ($response['extras'] as $item) {
	    		$a_model->extras->push(new OAMAvailabilityExtras($item['info']));
	    	}
	    }
	
	    return $a_model;
	}
	
	/**
	 * A helped function that adds a collection of OAMObjectContent objects to $o_model.
	 * This can be a list of:
	 * - used touroperators (tocs), 
	 * - room types used in this object (zacs)
	 * - service types used in this object (vpcs)
	 * 
	 * @param array $item
	 * @param string $code
	 * @param Ors\Orsapi\Oam\OAMObject $o_model
	 */
	protected function _addObjectContent($item, $code, &$o_model) {
		$o_model->{$code} = new Collection();
		
		if (!empty($item[$code]))
		foreach ($item[$code] as $tag => $val) {
			$o_model->{$code}->push(new OAMObjectContent(array('code' => $tag, 'value' => $val)));
		}
	}

	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::setFilters()
	 */
	public function setFilters($response) {
		$this->filters = new Collection();
		
		foreach ($response['filters'] as $f_code => $filter) {
		    $f_model = new OAMFilter(array('code' => $f_code, 'selected' => $filter['selected']));
		    $f_model->values = new Collection();
		     
		    if (!empty($filter['val']) && is_array($filter['val'])) {
		    	
		        foreach ($filter['val'] as $fv_code => $fv_value) {
		        	if ($f_code == 'ovr') {
		        		$rating = Common::percent2rating($fv_code);
		        		$fv_code = Common::rating2percent($rating);;
		        		$fv_value = $rating;
		        	}
		            $f_model->values->push(new OAMFilterVal(array('code' => $fv_code, 'value' => $fv_value, 'name' => $f_code)));
		            $f_model->values = $f_model->values->unique();
		        }
		    }
		    
		    $this->filters->push($f_model);
		}
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::setSorts()
	 */
	public function setSorts($response) {
		$this->sorts = new Collection();
		
		if (!empty($response['sorts']) && is_array($response['sorts']))
		foreach ($response['sorts'] as $s_code => $s_val) {
		    $s_model = new OAMSort(array('code' => $s_code, 'direction' => $s_val));
		    $this->sorts->push($s_model);
		}
	}
	
}