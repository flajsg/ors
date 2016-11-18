<?php namespace Ors\Orsapi\ConnConfig; 

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS Connection model.
 * 
 * Holds Connection information. 
 * 
 * @author gregor
 *
 */
class Connection extends Eloquent {

	protected $fillable = ['connection_id', 'connection_name', 'connection_desc'];
	
	protected $tocs = [];
	
	protected $primaryKey = 'connection_id';
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
		
		if (!empty($attributes['tocs'])) $this->setTocs($attributes['tocs']);
	}
	
	/**
	 * Return connection_id alias
	 * @return string
	 */
	public function getIdAttribute() {
		return $this->connection_id;
	}
	
	/**
	 * Set tocs list
	 * @param array $tocs
	 */
	public function setTocs(array $tocs) {
		$this->tocs = !empty($tocs) ? $tocs : array();
	}
	
	/**
	 * Return tocs list
	 * @return array
	 */
	public function getTocsAttribute() {
		return $this->tocs;
	}
	
	public function toArray() {
		$array = parent::toArray();
		$array['tocs'] = $this->Tocs;
		return $array;
	}
}