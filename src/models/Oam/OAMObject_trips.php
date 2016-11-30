<?php namespace Ors\Orsapi\Oam;

/**
 * OAMObject model implementation for content type trips
 * 
 * @author Gregor Flajs
 *
 */
class OAMObject_trips extends OAMObject_hotel {
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - Start
	* ==================================================== */
	
	/**
	 * Cart Offer Name attribute.
	 * This is used to display Offer Name in Cart list view.
	 * @return string
	 */
	public function getCartOfferNameAttribute() {
	    $out = $this->attributes['htn'];
	    return $out;
	}
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - End
	 * ==================================================== */
}