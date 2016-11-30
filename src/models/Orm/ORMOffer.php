<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Ors\Orsapi\Oam\OAMOffer;

/**
 * ORM offer class. Each Service is a representation of an ORMOffer.
 * 
 * @author Gregor Flajs
 *
 */
class ORMOffer extends OAMOffer {

	protected $primaryKey = 'id';
	
	/**
	 * Extended fillable attributes for ORMOffer
	 * @var array
	 */
	protected $fillable_extended = ['typ'];
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
		
		$this->fillable += $this->fillable_extended;
	}
	
	/**
	 * ctype_id attribute.
	 * 
	 * Depending on service[typ] this accessor will try to return a correct content type id.
	 * If ctype_id is not found then null is returned.
	 * 
	 * @return string
	 */
	public function getCtypeIdAttribute() {
		switch(strtoupper($this->typ)) {
			case 'H': return 'hotel';
			case 'F': return 'pauschal';
			case 'T': return 'trips';
			case 'EX': return 'extras';
		}
		return null;
	}
	
	/**
	 * FullName attribute.
	 * This accessor wil return a full name for this offer.
	 * 
	 * Extend this accessor in ORMOffer_[typ] classes.
	 * 
	 * This accessor is used when displaying offer name.
	 * @return string
	 */
	public function getFullNameAttribute() {
		return $this->attributes['htn'];
	}
}