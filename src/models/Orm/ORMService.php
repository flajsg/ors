<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Ors\Support\Common;
use Ors\Orsapi\Facades\PassengerApi;
use Ors\Orsapi\Oam\OAMAvailabilityService;

/**
 * ORM Service class (for service lines)
 * 
 * @author Gregor Flajs
 *
 */
class ORMService extends OAMAvailabilityService {
	
	protected $fillable = ['id', 'mrk', 'typ', 'cod', 'opt', 'op2', 'alc', 'cnt', 'vnd', 'bsd', 'agn', 'sst', 'scp', 'userid'];
	
	protected $primaryKey = 'id';
	
	/**
	 * Offer info (depending on service).
	 * @var ORMOffer
	 */
	public $offer;
	
	/**
	 * User responsible for data inside ORM
	 * We need this info so we can get Account passengers that are connected to this service.
	 * @var User
	 */
	public $user;
	
	public function __construct($attributes = array()) {
		if (empty($attributes['id']))
			$attributes['id'] = Common::makeUniqueHash();
		
		parent::__construct($attributes);
		
		$this->offer = new ORMOffer();
		$this->createUser($attributes);
	}

	/**
	 * Create owner (User).
	 *
	 * @param array $attributes
	 * 		data must be inside $attributes[userid]
	 */
	public function createUser($attributes) {
		$auth_model = Config::get('orsapi::auth_model');
		
	    if (!empty($attributes['userid']))
	        $this->user = $auth_model::find($attributes['userid']);
	    else
	        $this->user = null;
	}
		
	/*
	 * ACCESSORS
	 */
	
	public function getVndHumanAttribute() {
		return Common::date($this->attributes['vnd']);
	}
	
	public function getBsdHumanAttribute() {
		return Common::date($this->attributes['bsd']);
	}
	
	/**
	 * Vnd and Bsd output
	 * @return string
	 */
	public function getVndBsdAttribute() {
		$out = '';
	
		if ($this->vnd_human && $this->bsd_human)
			$out .= $this->vnd_human . '<i class="ml5 mr5 fa fa-angle-right"></i>' .$this->bsd_human;
		elseif ($this->vnd_human)
			$out .= $this->vnd_human;
		elseif ($this->bsd_human)
			$out .= $this->bsd_human;
		 
		return $out;	
	}
	
	/**
	 * Agn attribute.
	 * 
	 * Agn must be empty if there are no real persons on this service.
	 * You can only attach real passengers to service.
	 * 
	 * @return string
	 */
	/*public function getAgnAttribute() {
		if ($this->Persons->isEmpty())
			return '';
		return $this->attributes['agn'];
	}*/
	
	/**
	 * Return a list of persons for this service (depending on agn attribute).
	 * 
	 * @return Collection|ORMPassenger[]
	 */
	public function getPersonsAttribute() {
		if (!empty($this->attributes['agn']) && $this->user) {
		    $agn = array_filter( Common::extrim($this->attributes['agn']) );
		    $find_persons = array_filter(array_unique($agn), function($a){return $a >= 1000;});
		    $persons = PassengerApi::setAgencyKey($this->user->account_id, '', $this->user->account->orsapi_master_key)->findIds($find_persons);
		    return $persons;
		}
		else
		    return new Collection;
	}
	
	/*
	 * HELPERS
	 */
	
	/**
	 * Return true if service has now typ, cod and opt
	 * @return bool
	 */
	public function hasNoData() {
		if (empty($this->attributes['typ']) && empty($this->attributes['cod']) && empty($this->attributes['opt']))
			return true;
		return false;
	}
	
	public function toArray() {
		$array = parent::toArray();
		$array['vnd_human'] = $this->vnd_human;
		$array['bsd_human'] = $this->bsd_human;
		return $array;
	}
	
	/*
	 * STATICS
	 */
	

	/**
	 * This function changes ang attribute in services to a passengers sequence ids (instead of psnid)
	 *
	 *
	 * @param array|ORMService[] $services
	 * @param array|ORMPerson[] $persons
	 * 		persons on ORM mask with already changed Ids to sequence numbers
	 * @return array|ORMService[]
	 */
	public static function agnToSequence($services, $persons) {
		return array_map(function($srv) use ($persons) {
			if (empty($srv['agn'])) return $srv;
			
			$agn = $psns = array();
			foreach ($persons as $psn) $psns[$psn['psnid']] = $psn['id']; 

			foreach (Common::extrim($srv['agn']) as $psnid) {
			    if (!empty($psns[$psnid]))
			        $agn[] = $psns[$psnid];
			}
			
			$srv['agn'] = implode(',', $agn);
			return $srv; 
		}, $services);
	}
	
	/**
	 * This function changes ang attribute in services to a passengers psnid-s (instead of sequence number ids)
	 *
	 *
	 * @param array|ORMService[] $services
	 * @param array|ORMPerson[] $persons
	 * 		persons on ORM mask with already changed Ids to sequence numbers
	 * @return array|ORMService[]
	 */
	public static function agnToPsnid($services, $persons) {
		if (empty($services) || !is_array($services) || !is_array($persons))
			return array();
		return array_map(function($srv) use ($persons) {
			if (empty($srv['agn'])) return $srv;
			
			$agn = $psns = array();
			foreach ($persons as $psn) $psns[$psn['id']] = $psn['psnid'];
			
			foreach (self::parseAgn($srv['agn']) as $psnid) {
				if (!empty($psns[$psnid]))
				    $agn[] = $psns[$psnid];
			}
			
			$srv['agn'] = implode(',', $agn);
			return $srv;
		}, $services);
	}
	
	/**
	 * Parse service agn attribute as it is set from API response to passenger sequence ids
	 * 
	 * @example 
	 * 		$string '1-2' = array(1,2)
	 * 		$string '1,3-5' = array(1,3,4,5) 
	 * 
	 * @param string $string
	 * @return array
	 */
	public static function parseAgn($string) {
	    if (empty($string)) return array();
	    
	    
	    list($string) = explode("/", $string); // Dumb traffics
	    
	    $results = array();
	    foreach (explode(",", $string) as $entry)
	    {
	        if (!strpos($entry, "-")) $entry .= "-$entry";
	        list($start, $end) = explode("-", $entry);
	        if ((!is_numeric($start)) || (!is_numeric($end)))
	            continue;
	        if ($start > $end) { $tmp = $end; $end = $start; $start = $tmp; }
	        for ($i = $start; $i <= $end; $i++)
	        {
	            $results[] = $i;
	        }
	    }
	    return $results;
	}
}