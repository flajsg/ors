<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: MultiFactItem
 * 
 * This is a single multi fact item
 */

class OAMMultiFactItem extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['code', 'type', 'value', 'text', 'units'];
	
	/**
	 * Primary key
	 * @var string
	 */
	protected $primaryKey = 'code';
	
	/**
	 * Display attribute
	 * Depending on type & value attributes, this accessor returns formed HTML ready to display.
	 * For example, type=book will return a checkbox icon
	 * @return string
	 */
	public function getDisplayAttribute() {
		switch ($this->attributes['type']) {
			case 'bool':
				return $this->attributes['value'] == 'true' || $this->attributes['value'] == '1' ? "<i class='glyphicons glyphicons-check'></i>" : "<i class='glyphicons glyphicons-unchecked'></i>";
			default:
				
				if (preg_match('/^dist\_/', $this->attributes['code'])) {
					return $this->attributes['value'] . ' ' .$this->attributes['units'];
				}
				if (preg_match('/^sports\_/', $this->attributes['code'])) {
					return $this->attributes['value'] > 0 ? "<i class='glyphicons glyphicons-check'></i>" : "<i class='glyphicons glyphicons-unchecked'></i>";
				}
				
				return $this->attributes['value'];
		}
	}
}