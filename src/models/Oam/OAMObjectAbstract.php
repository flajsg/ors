<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * This is an Abstract class for OAMObject models.
 * 
 * Each content type can have different implementations of OAMObject class, 
 * therefore we must define a common interface so we don't get lost in all of the accessors.
 * 
 * @author Gregor Flajs
 *
 */
abstract class OAMObjectAbstract extends Eloquent {
	
	/**
	 * Cart Offer Name attribute.
	 * This is used to display Offer Name in Cart list view.
	 * @return string
	 */
	abstract public function getCartOfferNameAttribute();
	
	/**
	 * Merge model attributes (insert missing values)
	 * @param array $attributes
	 */
	public function mergeAttributes($attributes = array()) {
	    $this->attributes = array_merge($this->attributes, $attributes);
	}
}