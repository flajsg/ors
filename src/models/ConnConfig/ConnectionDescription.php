<?php namespace Ors\Orsapi\ConnConfig; 

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Connection description model.
 * 
 * This model contains information of all fields that can be set for a specific connection 
 * 
 * @author Gregor Flajs
 *
 */
class ConnectionDescription extends Eloquent {
	
	protected $fillable = ['name', 'type', 'desc'];
	
	protected $values = [];
	
	protected $primaryKey = 'name';
	
	public function __construct($attributes = array()) {
	    parent::__construct($attributes);
	
	    if (!empty($attributes['values'])) $this->setValues($attributes['values']);
	}
	
	/**
	 * Set values list
	 * @param array $tocs
	 */
	public function setValues(array $values) {
	    $this->values = !empty($values) ? $values : array();
	}
	
	/**
	 * Return values list
	 * @return array
	 */
	public function getValuesAttribute() {
	    return $this->values;
	}
	
	public function toArray() {
	    $array = parent::toArray();
	    $array['values'] = $this->values;
	    return $array;
	}
}