<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;
use Ors\Support\SmartTab;
use Ors\Orsapi\Facades\PassengerApi;
use Ors\Orsapi\Facades\ReservationsApi;

/**
 * ORM Class.
 * 
 * This class holds everything together.
 * 
 * @author Gregor Flajs
 *
 */
class ORM extends Eloquent {
	
	protected $fillable = [
		// Other
		'info',
	
		// booking
		'prc', 'bkc',
		
		// admin
		'act', 'customer',
				
		// login
		'userid', 'agid'
	];
	
	/**
	 * User responsible for data inside ORM
	 * @var User
	 */
	public $user;
	
	/**
	 * ORM services
	 * @var Collection|ORMService
	 */
	public $services;
	
	/**
	 * ORM persons
	 * @var Collection|ORMPerson
	 */
	public $persons;
	
	/**
	 * Operator
	 * @var ORMOperator
	 */
	public $operator;
	
	/**
	 * Response
	 * @var ORMResponse
	 */
	public $response;
	
	/**
	 * Login
	 * @var ORMLogin
	 */
	public $login;
	
	/**
	 * Offers (depending on service). Offer[id] is a sequence id of coresponding service.
	 * @var Collection|ORMOffer
	 */
	public $offers;
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
		
		$this->createLogin($attributes);
		$this->createPersons($attributes);
		$this->createOperator($attributes);
		$this->createResponse($attributes);
		$this->createServices($attributes);
		$this->createOffers($attributes);
	}
	
	/**
	 * Creates ORM model from ORS API response array.
	 * 
	 * $response can came from orm api response or from reservations api, 
	 * where each booking is an orm response.
	 * 
	 * The problem is, that when response is from reservations api, 
	 * all attributes are encapsulated inside @attributes array, 
	 * so we must use IF statements to check where are the data.
	 * 
	 * <code>
	 * // example of orm api response
	 * $response = array('admin' => (
	 * 		'attributes' => array(),
	 * 		'login' => array(),
	 * 		'result' => array(),
	 * 		'operator' => array(),
	 * 		'customer' => array(),
	 * 		'services' => array([]),
	 * 		'travellers' => array([]),
	 * 		'offers' => array([]),
	 * ))
	 * 
	 * // example of reservations api (single booking) response
	 * $response = array('admin' => (
	 * 		'login' => array('@attributes' => [] ),
	 * 		'result' => array('@attributes' => [] ),
	 * 		'operator' => array('@attributes' => [] ),
	 * 		'customer' => array('@attributes' => [] ),
	 * 		'services' => array('service' => [] ),
	 * 		'travellers' => array('traveller' => [] ),
	 * 		'offers' => array('offer' => [] ),
	 * ))
	 * <code> 
	 * 
	 * @param $this
	 */
	public static function withApiResponse($response) {
		$admin = $response;
		
		$attributes = array(
			'login' 	=> !empty($admin['login']['@attributes']) ? $admin['login']['@attributes'] :$admin['login'],
			'response' 	=> !empty($admin['result']['@attributes']) ? $admin['result']['@attributes'] : $admin['result'],
			'operator' 	=> !empty($admin['operator']['@attributes']) ? $admin['operator']['@attributes'] : $admin['operator'],
			'services' 	=> !empty($admin['services']['service']) ? $admin['services']['service'] : $admin['services'],
			'persons'  	=> !empty($admin['travellers']['traveller']) ? $admin['travellers']['traveller'] : $admin['travellers'],
			'offers'  	=> !empty($admin['offers']['offer']) ? $admin['offers']['offer'] : $admin['offers'],
			//'offers'  	=> Common::padZeroArray($admin['offers']),
			'info' 		=> !empty($admin['info']['line']) ? $admin['info']['line'] : $admin['info'],
		);
		
		// Fix AGN
		$attributes['services'] = ORMService::agnToPsnid($attributes['services'], $attributes['persons']);
		
		// Customer
		$customer = !empty($admin['customer']['@attributes']) ? $admin['customer']['@attributes'] : $admin['customer'];
		
		if (!empty($customer['psnid']))
			$attributes['customer'] = $customer['psnid'];
		else
			$attributes['customer'] = 0;
		
		return new self($attributes);
	}
	
	/**
	 * Create ORM services.
	 * 
	 * @param array $attributes
	 * 		data must be inside $attributes[services]
	 */
	public function createServices($attributes) {
		$this->services = new Collection();
		
		if (!empty($attributes['services'])) {
			
			foreach ($attributes['services'] as $srv)
				if (!empty($srv)) {
					$srv = !empty($srv['@attributes']) ? $srv['@attributes'] : $srv;
					$srv['toc'] = $this->operator->toc;
					$this->services->push(new ORMService($srv+array('userid' => $this->user->id)));
				}
		}
	}
	
	/**
	 * Create ORM offers.
	 *
	 * @param array $attributes
	 * 		data must be inside $attributes[offers]
	 */
	public function createOffers($attributes) {
		$this->offers = new Collection();
		
		if (!empty($attributes['offers'])) {

			$attributes['offers'] = Common::padZeroArray($attributes['offers']);

			foreach ($attributes['offers'] as $offer) {
				
				if (!empty($offer)) {
				    $attributes = !empty($offer['@attributes']) ? $offer['@attributes'] : $offer;
				    $attributes['toc'] = $this->operator->toc;
				    
				    $srv = $this->services->find($attributes['id']);
				    $class = "Ors\Orsapi\Orm\ORMOffer_".strtolower($srv['typ']);
				
				    if (class_exists($class))
				        $offer = new $class($attributes);
				    else
				        $offer = new \Ors\Orsapi\Orm\ORMOffer($attributes);
				    
				    $this->offers->push($offer);
				    $srv->offer = $offer;
				}

			}
		}
	}
	
	/**
	 * Create ORM persons.
	 * 
	 * @param array $attributes
	 * 		data must be inside $attributes[persons]
	 */
	public function createPersons($attributes) {
	    $this->persons = new Collection();
	
	    if (!empty($attributes['persons'])) {
	    	
	    	foreach ($attributes['persons'] as $psn)
	    		if (!empty($psn)) {
	    			$psn = !empty($psn['@attributes']) ? $psn['@attributes'] : $psn;
	        		$this->persons->push(new ORMPerson($psn+array('userid' => $this->user->id)));
	    		}
	    		
    		$this->reloadPersons();
		}
	}
	
	/**
	 * Create ORM operator.
	 * 
	 * @param array $attributes
	 * 		data must be inside $attributes[operator]
	 */
	public function createOperator($attributes) {
	    if (!empty($attributes['operator'])) {
	    	
	    	if (empty($attributes['operator']['psn']))
	    		$attributes['operator']['psn'] = $this->persons->count();
	    	
	    	//if (empty($attributes['operator']['bst']) && !empty($attributes['hsc']))
	    	//	$attributes['operator']['bst'] = $attributes['hsc'];
	    	
	    	if (empty($attributes['operator']['act']) && !empty($attributes['act']))
	    		$attributes['operator']['act'] = $attributes['act'];
	    	
        	$this->operator = new ORMOperator($attributes['operator']);
	    }else
        	$this->operator = new ORMOperator();
	    
	}
	
	/**
	 * Create ORM response.
	 * 
	 * @param array $attributes
	 * 		data must be inside $attributes[response]
	 */
	public function createResponse($attributes) {
	    if (!empty($attributes['response']))
        	$this->response = new ORMResponse($attributes['response']);
	    else
        	$this->response = new ORMResponse();
	}
	
	/**
	 * Create ORM login.
	 * 
	 * @param array $attributes
	 * 		data must be inside $attributes[login]
	 */
	public function createLogin($attributes) {
		
		if (!empty($attributes['login']))
			$this->login = new ORMLogin($attributes['login']);
		//else
			//$this->login = ORMLogin::withAuthUser();
		
		$this->user = $this->login->user;
	}
	
	/**
	 * Add Availability extras to services.
	 * 
	 * Extras can be an array of OAMAvailabilityExtras attributes or collection of OAMAvailabilityExtras objects.
	 * 
	 * @param array|OAMAvailabilityExtras[] $extras
	 */
	public function addExtrasAsServices($extras) {
		
		foreach ($extras as $e) {
			
			$this->services->push(new ORMService(array(
				'typ' => 'EX',
				'cod' => $e['id'],
				'vnd' => $e['dateFrom'],
				'bsd' => $e['dateTo'],
			)));
		}
		
	}
	
	/*
	 * VALIDATION
	 */
	
	/**
	 * Validation rules when posting ORM mask.
	 * 
	 * @return array
	 */
	public static function rules(){
		return array(
			'act' => 'required',
			'customer' => 'required',
		);
	}
	
	/*
	 * MUTATORS
	 */
	
	/**
	 * ACT must be uppercase 
	 * @param string $value
	 */
	public function setActAttribute($value) {
		$this->attributes['act'] = strtoupper($value);
	}
	
	/**
	 * Default customer is 0 which means Account holder.
	 * @param int $value
	 */
	public function setCustomerAttribute($value) {
	    if (!empty($value))
	        $this->attributes['customer'] = $value;
	    else
	    	$this->attributes['customer'] = 0;
	}
	
	/*
	 * ACCESSORs
	 */
	
	/**
	 * Get default ACT
	 * @return string
	 */
	public function getActAttribute() {
		if (empty($this->attributes['act']) && $this->operator->isBooking())
	    	return 'D';
		if (empty($this->attributes['act']))
	    	return 'BA';	
		return strtoupper($this->attributes['act']);
	}

	/**
	 * Implode info lines with new lines to a string 
	 * @return string
	 */
	public function getInfoStrAttribute() {
		if (!empty($this->attributes['info']) && is_array($this->attributes['info']))
			return implode("\n", $this->attributes['info']);
		return '';
	}
	
	/**
	 * Customer as ORMPerson object
	 * @return ORMPerson|null
	 * 		null is returned if there is no customer set (or incase of exception)
	 */
	public function getCustomerPsnAttribute() {
		if (empty($this->customer)) {
			return new ORMPerson(array(
				'sur' => $this->user->account->name,
				'eml' => $this->user->account->email,
				'tel' => $this->user->account->tel,
				'cty' => $this->user->account->city,
				'zip' => $this->user->account->zip,
				'cny' => $this->user->account->country->countryCode,
				'str' => $this->user->account->address,
				'is_agency' => 1,
			));
		} elseif ($this->customer==1 && $this->login->subaccount) {
			return new ORMPerson(array(
			    'sur' => $this->login->subaccount->name,
			    'eml' => $this->login->subaccount->email,
			    'tel' => $this->login->subaccount->tel,
			    'cty' => $this->login->subaccount->city,
			    'zip' => $this->login->subaccount->zip,
			    'cny' => $this->login->subaccount->country->countryCode,
			    'str' => $this->login->subaccount->address,
			    'is_agency' => 1,
			));
		}
		
		if (!empty($this->user)) {
			return PassengerApi::setAgencyKey($this->user->account_id, '', $this->user->account->orsapi_master_key)->find($this->customer)->OrmPerson;
		}
		return null;
	}
	
	/**
	 * Return SmartTab object with action "load from bkc", so we can load ORM from bkc (internal booking nr.) attribute.
	 * 
	 * As extra_params, filters with bkc is also set and search_params with ibeid.
	 * 
	 * @return SmartTab
	 */
	public function getSmartTabAttribute() {
		$stab = new SmartTab('orm', $this->operator->bkc);
		$stab->setIcon('glyphicons glyphicons-book')->setColorClass('bg-alert light')->setAction('search_load_mask_orm_bkc');
		$stab->with('search_params', array('ibeid' => $this->login->ibeid));
		$stab->with('filters', ReservationsApi::makeSimpleFiltersGroup('book_id', array($this->operator->bkc))); 
			
		return $stab;
	}
	
	/*
	 * HELPERS
	 */
	
	/**
	 * Return a number of adults
	 * @return int
	 */
	public function adults() {
		$st = 0;
		foreach ($this->persons as $pax)
			if ($pax->isAdult()) $st++;
		return $st;
	}
	
	/**
	 * Return a number of children
	 * @return int
	 */
	public function children() {
		$st = 0;
		foreach ($this->persons as $pax)
			if ($pax->isChild()) $st++;
		return $st;
	}
	
	/**
	 * Join persons by their prices and typ (adult/child).
	 * 
	 * Output array should look something like this
	 * 
	 * <code>
	 * // Output...
	 * array(
	 *   'adult' => array(
	 *   	100 => ORSPerson[],
	 *   ),
	 *   'child' => array(
	 *   	50 => ORMPerson[],
	 *   	0 => ORMPerson[]
	 *   ) 
	 * )
	 * </code>
	 * 
	 * @return array
	 */
	public function aggregatePersonsPrices() {
		$array = array('adult' => array(), 'child' => array());
		foreach ($this->persons as $pax) {
			if ($pax->isAdult())
				$array['adult'][$pax->tvp][] = $pax;
			else
				$array['child'][$pax->AgeReal][] = $pax;
		}
		
		krsort($array['adult']);
		krsort($array['child']);
		return $array;
	}
	
	/**
	 * Checks if any one of the persons is a fake. 
	 * A person is fake if it has no person id (psnid).
	 * @return boolean
	 */
	public function hasFakePersons() {
		foreach ($this->persons as $psn)
			if (empty($psn->psnid)) return true;
		return false;
	}
	
	/**
	 * This method goes through persons list and reload them from database.
	 * 
	 * Use this method when you are opening saved ORM object and you want to display fresh data.
	 *  
	 */
	public function reloadPersons() {
		if ($this->user) {
			
			// Patch: Let's make this faster by sending all ids ;)
			$ids = array();
			foreach ($this->persons as $psn)
				if (!empty($psn->psnid)) $ids []= $psn->psnid;
				
			if ($ids) {
				$passengers = PassengerApi::setAgencyKey($this->user->account_id, '', $this->user->account->orsapi_master_key)->findIds($ids);
			}
			
			$this->persons = $this->persons->map(function($psn, $key) use($passengers) {
				if (!empty($psn->psnid)) {
				    $p = $passengers->find($psn->psnid)->OrmPerson;
				    $p->tvp = $psn->tvp;
				    if (!empty($p)) 
				    	return $p;
				    return new ORMPerson();
				}
				return $psn;
			});
		}
	}
	
	public function toArray() {
		$array = parent::toArray();
		$array['login'] = $this->login->toArray();
		$array['operator'] = $this->operator->toArray();
		$array['response'] = $this->response->toArray();
		$array['services'] = $this->services->toArray();
		$array['persons']  = $this->persons->toArray();
		$array['offers']   = $this->offers->toArray();
		return $array;
	}
	
	/**
	 * This method returns array that will be used for ORM API call
	 * 
	 * <code>
	 * array('admin' => array(
	 * 	 'attributes' => ['typ' => 'through'],
	 *   'login' => array('urs' => '', 'agn' => '', 'userid' => 1232, 'usernm' => 'Gregor Flajs'),
     *   'operator' => array('typ' => '', 'act' => 'BA', 'knd' => '', 'toc' => 'SONH', 'psn' => 2),
     *   'services' => array(['id' => '1', 'typ' => 'H', 'cod' => 'DIAMANT 2016', 'opt' => '2210,HP', 'vnd' => '21042016', 'bsd' => '22042016']),
     *   'customer' => array('sur' => '', 'pre' => '', 'eml' => '', 'tel' => ''),
     *   'travellers' => array(
     *       ['id' => 1, 'psnid' => '-1', 'typ' => 'H', 'sur' => 'GREGOR', 'pre' => 'FLAJS'],
     *       ['id' => 2, 'psnid' => '-1', 'typ' => 'H', 'sur' => 'JURE', 'pre' => 'ARNUŠ'],
     *   )
     * ))
     * </code>
     * 
	 * @return array
	 */
	public function toApiArray() {
		
		$array = array('admin' => array('attributes' => ['typ' => 'thrugh']));
		$array['admin']['login'] 	  = $this->login->toArray();
		//$array['admin']['login'] 	  = ORMLogin::withAuthUser()->toArray();
		$array['admin']['operator']   = $this->operator->toArray();
		$array['admin']['customer']   = empty($this->customer_psn) ? array() : $this->customer_psn->toArray();
		$array['admin']['travellers'] = ORMPerson::transformForApi($this->persons)->toArray();
		$array['admin']['services']   = $this->services->toArray();
		
		// Fix AGN
		$array['admin']['services'] = ORMService::agnToSequence($array['admin']['services'], $array['admin']['travellers']);
		
		
		return $array;
	}
}