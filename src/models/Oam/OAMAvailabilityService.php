<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Availability Service class (for service lines)
 * 
 * @author Gregor Flajs
 *
 */
class OAMAvailabilityService extends Eloquent {
	
	protected $fillable = ['id', 'mrk', 'typ', 'cod', 'opt', 'op2', 'alc', 'cnt', 'vnd', 'bsd', 'agn', 'sst', 'scp'];
	
	protected $primaryKey = 'id';
	
	public function __construct($attributes = array()) {
		if (empty($attributes['id']))
			$attributes['id'] = Common::makeUniqueHash();
		
		parent::__construct($attributes);
	}

	/*
	 * MUTATORS
	 */

	
	/**
	 * Typ must be all upper case
	 * @param string $value
	 */
	public function setTypAttribute($value) {
	    $this->attributes['typ'] = strtoupper($value);
	}
	
	/**
	 * Change date to dmY
	 * @param string $value
	 */
	public function setVndAttribute($value) {
		$this->attributes['vnd'] = Common::date($value, 'dmY');
	}
	
	/**
	 * Change date to dmY
	 * @param string $value
	 */
	public function setBsdAttribute($value) {
		$this->attributes['bsd'] = Common::date($value, 'dmY');
	}
	
}