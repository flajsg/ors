<?php namespace Ors\Orsapi\Orm; 

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;
use Ors\Support\SmartAutocompleteInterface;
use Ors\Orsapi\Facades\PassengerApi;

/**
 * ORM Passenger model.
 * 
 * This model handles company passengers from ORS API. Each company can have its own database of passengers. 
 * 
 * @author Gregor Flajs
 *
 */
class ORMPassenger extends Eloquent implements SmartAutocompleteInterface {
	
	
	protected $fillable = ['id', 'agency_id', 'sex', 'first_name', 'last_name', 'birth_date', 'email', 'telephone', 'mobile_phone', 'city', 'street', 'zip_code', 'country', 
		'deleted', 'api_added', 'last_modified', 'merged_to', 'merged_from', 'read_only', 'booking_created'];
	
	protected $primaryKey = 'id';
	
	/**
	 * API handler wrapper
	 * @var \Ors\Orsapi\PassengerApiWrapper
	 */
	protected $api;

	/*
	 * VALIDATION
	 */
	
	public static function rules($id = 0) {
		return array(
			'email' => 'email',
			'sex' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
		);
	}
	
	/**
	 * Return passenger model of a person that this passenger is linked to (depending on merged_to attribute)
	 * 
	 * @return ORMPassenger
	 */
	public function linkedTo() {
		if (empty($this->merged_to))
			return null;
		return PassengerApi::find($this->merged_to);
	}
	
	/*
	 * MUTATORS
	 */
	
	/**
	 * merged_from default value is empty array()
	 * 
	 * @param array $value
	 */
	public function setMergedFromAttribute($value) {
		if (empty($value))
			$this->attributes['merged_from'] = array();
		else
			$this->attributes['merged_from'] = $value;
	}
	
	/*
	 * ACCESSORS
	 */
	
	/**
	 * ID default value is unique hash code
	 * @return string|id
	 * 		if is numeric then this is a valid passenger id, othervise it is a guid hash
	 */
	public function getIdAttribute() {
	    if (empty($this->attributes['id']))
	        return Common::makeUniqueHash();
	    return $this->attributes['id'];
	}
	
	/**
	 * Typ attribute (same as sex)
	 * @return string
	 */
	public function getTypAttribute() {
	    return $this->attributes['sex'];
	}
	
	/**
	 * Human readable birth date
	 * @return string
	 */
	public function getAgeHumanAttribute() {
	    return Common::date($this->attributes['birth_date']);
	}
	
	/**
	 * Age attribute (same as birth_date)
	 * @return string
	 */
	public function getAgeAttribute() {
	    return $this->attributes['birth_date'];
	}
	
	/**
	 * Deleted attribute
	 * @return boolean
	 */
	public function getDeletedAttribute() {
		return isset($this->attributes['deleted']) ? (bool)$this->attributes['deleted'] : false;;
	}
	
	/**
	 * Readonly attribute
	 * @return boolean
	 */
	public function getReadOnlyAttribute() {
		return isset($this->attributes['read_only']) ? (bool)$this->attributes['read_only'] : false;
	}
	
	/**
	 * ApiAdded attribute
	 * @return boolean
	 */
	public function getApiAddedAttribute() {
		return isset($this->attributes['api_added']) ? (bool)$this->attributes['api_added'] : false;
	}
	
	/**
	 * Map passenger attributes to ORMPerson attributes and return ORMPerson object
	 * @return \ORM\ORMPerson
	 */
	public function getOrmPersonAttribute() {
		return new ORMPerson(array(
			'id' => $this->id,
			'psnid' => $this->psnid,
			'agency_id' => $this->agency_id,
			'typ' => $this->sex,
			'pre' => $this->first_name,
			'sur' => $this->last_name,
			'age' => $this->birth_date,
			'eml' => $this->email,
			'tel' => $this->telephone,
			'mob' => $this->mobile_phone,
			'cty' => $this->city,
			'str' => $this->street,
			'zip' => $this->zip_code,
			'cny' => $this->country,
			'is_deleted' => $this->deleted, 
			'is_readonly' => $this->read_only, 
			'api_added' => $this->api_added, 
			'last_modified' => $this->last_modified
		));
	}
	
	/**
	 * @see \ORS\Helpers\SmartAutocompleteInterface::getSmartAutocompleteKeyAttribute()
	 */
	public function getSmartAutocompleteKeyAttribute(){
	    return $this->id;
	}
	
	/**
	 * @see \ORS\Helpers\SmartAutocompleteInterface::getSmartAutocompleteTitleAttribute()
	 */
	public function getSmartAutocompleteTitleAttribute(){
		$out = "{$this->typ_human} {$this->FullName}";
		if (!empty($this->city))
			$out .= " / <span class='text-muted'>{$this->city}</span>";
		if (!empty($this->email))
			$out .= " / <span class='text-muted'>{$this->email}</span>";
		if (!empty($this->age))
			$out .= " ({$this->age_human})";
		
	    return $out;
	}
	
	/**
	 * @see \ORS\Helpers\SmartAutocompleteInterface::getSmartAutocompleteIconAttribute()
	 */
	public function getSmartAutocompleteIconAttribute(){
		return 'fa fa-user';
	}
	
	/**
	 * @see \ORS\Helpers\SmartAutocompleteInterface::getSmartAutocompleteTabTitleAttribute()
	 */
	public function getSmartAutocompleteTabTitleAttribute(){return '';}
	
	/**
	 * Passenger full name (last_name + first_name)
	 * @return string
	 */
	public function getFullNameAttribute() {
		return "{$this->attributes['last_name']} {$this->attributes['first_name']}";
	}
	
	/**
	 * Return Address (street/city/zip/country
	 * @return string
	 */
	public function getAddressAttribute() {
		$out = '';
		if (!empty($this->attributes['street']))
			$out .= $this->attributes['street'];
		
		if (!empty($out))  $out .= ' / ';
		
		if (!empty($this->attributes['city']))
			$out .= $this->attributes['city'];
		
		if (!empty($this->attributes['zip_code']))
			$out .= ' ' .$this->attributes['zip_code'];
		
		if (!empty($this->attributes['country']))
			$out .= ' ('. $this->attributes['country'].')';
		return $out;
	}

	/**
	 * Psnid
	 * This is the same as the id
	 * @return int
	 */
	public function getPsnidAttribute() {
	    return $this->attributes['id'];
	}
	
	/**
	 * Traveller Id
	 * This is the same as the id
	 * @return int
	 */
	public function getTravelerIdAttribute() {
	    return $this->attributes['id'];
	}
	
	/*
	 * HELPERS 
	 */
	
	/**
	 * Return true if passenger is deleted
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->Deleted;
	}
	
	/**
	 * Return true if passenger is readonly
	 * @return boolean
	 */
	public function isReadonly() {
	    return $this->ReadOnly;
	}
	
	/**
	 * Return true if passenger is api-added
	 * @return boolean
	 */
	public function isApiAdded() {
	    return $this->ApiAdded;
	}
	
}