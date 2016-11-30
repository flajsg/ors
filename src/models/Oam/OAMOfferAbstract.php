<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * This is an Abstract class for OAMOffer models.
 * 
 * Each content type can have different implementations of OAMOffer class, 
 * therefore we must define a common interface so we don't get lost in all of the accessors.
 * 
 * @author Gregor Flajs
 *
 */
abstract class OAMOfferAbstract extends Eloquent {
	
	/*
	 * COMMON ACCESSORS (TRIPS)
	 */
	
	/**
	 * Details Attribute.
	 * This returns offer details, separated by "/". 
	 * Offer details are used in DataTable for trips.
	 * @return string
	 */
	abstract public function getDetailsAttribute();
	
	/*
	 * COMMON ACCESSORS (CART)
	 */
	
	/**
	 * Cart Offer Details attribute.
	 * This is used to display Offer details in Cart list view.
	 * @return string
	 */
	abstract public function getCartOfferDetailsAttribute();
}