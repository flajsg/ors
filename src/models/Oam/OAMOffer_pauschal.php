<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;

/**
 * OAMOffer model implementation for content type pauschal
 * 
 * @author Gregor Flajs
 *
 */
class OAMOffer_pauschal extends OAMOffer_hotel {
	
	/**
	 * Cart Offer Details attribute.
	 * This is used to display Offer details in Cart list view.
	 * @return string
	 */
	public function getCartOfferDetailsAttribute() {
	    $out = array();
	    $out []= sprintf("%s - %s (%s)", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']), $this->attributes['tdc']);
	    $out []= sprintf("<small><span >%s</span>|<span title='%s'>%s</span>|<span title='%s'>%s</span>|<span title='%s'>%s</span>|<span title='%s'>%s</span></small>", 
	    		$this->attributes['toc'], 
	    		$this->attributes['zan']
	    			. (!empty($this->attributes['ztx']) ? ', '.$this->attributes['ztx'] : '') 
		    		. (!empty($this->attributes['ltx']) ? ', '.$this->attributes['ltx'] : '') 
		    		. (!empty($this->attributes['atx']) ? ', '.$this->attributes['atx'] : ''), 
	    		$this->attributes['zac'], 
	    		$this->attributes['vpn']
	    			. (!empty($this->attributes['itx']) ? ', '.$this->attributes['itx'] : ''), 
	    		$this->attributes['vpc'],
	    		$this->attributes['ahn'], 
	    		$this->attributes['ahc'],
	    		$this->attributes['zhn'], 
	    		$this->attributes['zhc']
	    );
	
	    return implode('<br>', $out);
	}

	/**
	 * AhnAhc Attribute.
	 * This is a concatination of ahn and ahc attributes (departure airport).
	 * @return string
	 */
	public function getAhnAhcAttribute() {
	    return "{$this->attributes['ahn']} ({$this->attributes['ahc']})";
	}
	
	/**
	 * ZhnZhc Attribute.
	 * This is a concatination of zhn and zhc attributes (arrival airport).
	 * @return string
	 */
	public function getZhnZhcAttribute() {
		return "{$this->attributes['zhn']} ({$this->attributes['zhc']})";
	}
}