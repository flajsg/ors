<?php namespace Ors\Orsapi\ConnConfig; 

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Connection configuration model.
 * 
 * This model contains configuration values for a specific connection. 
 * Configuration information are stored as a tag-value pairs inside $configuration property.
 * 
 * @author Gregor Flajs
 *
 */
class ConnectionConfiguration extends Eloquent {
	
	protected $fillable = ['active'];
	
	/**
	 * Configurations (tag-values : field_name-value)
	 * @var array
	 */
	protected $configuration = [];
	
	protected $primaryKey = 'name';
	
	public function __construct($attributes = array()) {
	    parent::__construct($attributes);
	
	    if (!empty($attributes['configuration'])) $this->setConfiguration($attributes['configuration']);
	}
	
	/**
	 * Set tocs list
	 * @param array $tocs
	 */
	public function setConfiguration(array $configuration) {
	    $this->configuration = !empty($configuration) ? $configuration : array();
	}
	
	/**
	 * Return configuration list
	 * @return array
	 */
	public function getconfigurationAttribute() {
	    return $this->configuration;
	}
	
	public function toArray() {
	    $array = parent::toArray();
	    $array['configuration'] = $this->configuration;
	    return $array;
	}
}