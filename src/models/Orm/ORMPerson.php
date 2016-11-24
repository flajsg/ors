<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Ors\Support\Common;

/**
 * ORM Person class (for person lines)
 *
 * @author Gregor Flajs
 *
 */
class ORMPerson extends Eloquent {
	
	/*
	 * [deleted] => 
	 * [api-added] => 1
	 * [agency-id] => 10000
     * [last-modified] => 03.03.2016 15:43:40
	 */
	
	protected $fillable = ['id', 'psnid', 'typ', 'sur', 'pre', 'age', 'tvp', 'eml', 'mob', 'tel', 'str', 'cty', 'zip', 'cny', 'agency_id', 'is_deleted', 'api_added', 'last_modified', 'is_agency'];
	
	protected $primaryKey = 'id';
	
	public function __construct($attributes = array()) {
		if (empty($attributes['id']))
		    $attributes['id'] = Common::makeUniqueHash();
		
	    parent::__construct($attributes);
	}
	
	/*
	public function account(){
		if (!empty($this->agency_id))
			return Account::find($this->agency_id);
		return null;
	}
	*/
	
	/*
	 * MUTATORS
	 */
	
	/**
	 * Make sure typ is uppercase
	 * @param string $value
	 */
	public function setTypAttribute($value) {
		$this->attributes['typ'] = strtoupper($value);
	}
	
	/**
	 * Id attribute mutator.
	 * Set id as unique hash if it is empty.
	 * @param unknown $value
	 */
	public function setIdAttribute($value) {
		if (!empty($value))
			$this->attributes['id'] = $value;
		else
			$this->attributes['id'] = Common::makeUniqueHash();
	}
	
	/**
	 * is_agency attribute mutator.
	 * 
	 * We set default is_agency attribute value to 0.
	 * If customer is agency then is_agency = 1.
	 * 
	 * @param int $value
	 */
	public function setIsAgencyAttribute($value) {
		if (empty($value)) $this->attributes['is_agency'] = 0;
		else $this->attributes['is_agency'] = $value;
	}
	
	/**
	 * Change date to dmY
	 * @param string $value
	 */
	public function setAgeAttribute($value) {
		if (is_numeric($value) && strlen($value) <= 3)
			$this->attributes['age'] = $value;
		else
	    	$this->attributes['age'] = Common::date($value, 'dmY');
	}
	
	/**
	 * Change last_modified to "Y-m-d H:i:s"
	 * @param string $value
	 */
	public function setLastModifiedAttribute($value) {
	    $this->attributes['last_modified'] = Common::dateTime($value, 'Y-m-d H:i:s');
	}
	
	/*
	 * ACCESSORS
	 */
	
	/*public function getIdAttribute() {
		if (!empty($this->id))
			return $this->id;
		return Common::makeUniqueHash();
	}*/
	
	/**
	 * Readable birthdate.
	 * If age is not date then an age is returned 
	 * @return string|int
	 */
	public function getAgeHumanAttribute() {
		if (is_numeric($this->attributes['age']) && strlen($this->attributes['age']) <= 3)
			return $this->attributes['age'];
	    return Common::date($this->attributes['age']);
	}
	
	/**
	 * Return Age in years.
	 * So if person has a birth-date for age attribute, then this method will calculate the age in years.
	 * @return int
	 */
	public function getAgeRealAttribute() {
		if (is_numeric($this->attributes['age']) && strlen($this->attributes['age']) <= 3)
			return $this->attributes['age'];
		if (!empty($this->attributes['age']))
			return Common::date2age($this->attributes['age']);
		return null;
	}
	
	/**
	 * Full name (last name + name)
	 * @return string
	 */
	public function getFullNameAttribute() {
	    return "{$this->attributes['sur']} {$this->attributes['pre']}";
	}
	
	/**
	 * Human readable last_modified datetime
	 * @param string $value
	 */
	public function getLastModifiedHumanAttribute() {
	    return Common::dateTime($this->attributes['last_modified']);
	}
	
	/*
	 * HELPERS
	 */
	
	/**
	 * Return true if a person is adult
	 * @return bool
	 */
	public function isAdult() {
		$age = $this->AgeReal;
		
		if (!empty($this->age) && $age < 18)
			return false;
		
		return true;
	}
	
	/**
	 * Return true if a person is child
	 * @return bool
	 */
	public function isChild() {
	    return !$this->isAdult();
	}
	
	/*
	 * STATICS
	 */
	
	/**
	 * Transform persons collection for ORS API a bit.
	 * 
	 * - This fixes person->id to sequence numbers [1,2,3...]
	 * 
	 * @param Collection|ORMPerson[] $persons
	 * @return Collection|ORMPerson[]
	 */
	public static function transformForApi($persons) {
		return $persons->map(function($psn, $key) {
			$psn->id = $key+1;
			return $psn; 
		});
	}
}