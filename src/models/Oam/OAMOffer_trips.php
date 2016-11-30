<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;

/**
 * OAMOffer model implementation for content type trips
 * 
 * @author Gregor Flajs
 *
 */
class OAMOffer_trips extends OAMOffer_hotel {

	/**
	 * Cart Offer Details attribute.
	 * This is used to display Offer details in Cart list view.
	 * @return string
	 */
	public function getCartOfferDetailsAttribute() {
	    $out = array();
	    $out []= sprintf("%s - %s (%s)", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']), $this->attributes['tdc']);
	    $out []= sprintf("<small><span >%s</span>|<span>%s</span>|<span>%s</span>", 
	    		$this->attributes['toc'], 
	    		$this->attributes['sin'], 
	    		$this->attributes['sub_name']
	    );
	
	    return implode('<br>', $out);
	}
}