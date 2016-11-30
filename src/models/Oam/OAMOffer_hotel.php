<?php namespace Ors\Orsapi\Oam;

use Ors\Support\Common;

/**
 * OAMOffer model implementation for content type hotel
 * 
 * @author Gregor Flajs
 *
 */
class OAMOffer_hotel extends OAMOffer {
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - Start
	 * ==================================================== */
		
	/**
	 * Details Attribute.
	 * This returns offer details, separated by /
	 * @return string
	 */
	public function getDetailsAttribute() {
	    $ret = array();
	     
	    if (!empty($this->attributes['ztx']))
	        $ret[]= $this->attributes['ztx'];
	     
	    if (!empty($this->attributes['atx']))
	        $ret[]= $this->attributes['atx'];
	     
	    if (!empty($this->attributes['ltx']))
	        $ret[]= $this->attributes['ltx'];
	     
	    if (!empty($this->attributes['itx']))
	        $ret[]= $this->attributes['itx'];
	
	    return implode(' / ', $ret);
	}
	
	/**
	 * Cart Offer Details attribute.
	 * This is used to display Offer details in Cart list view.
	 * @return string
	 */
	public function getCartOfferDetailsAttribute() {
	    $out = array();
	    $out []= sprintf("%s - %s (%s)", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']), $this->attributes['tdc']);
	    $out []= sprintf("<small><span >%s</span>|<span title='%s'>%s</span>|<span title='%s'>%s</span></small>", 
    		$this->attributes['toc'], 
    		$this->attributes['zan']
	    		. (!empty($this->attributes['ztx']) ? ', '.$this->attributes['ztx'] : '') 
	    		. (!empty($this->attributes['ltx']) ? ', '.$this->attributes['ltx'] : '') 
	    		. (!empty($this->attributes['atx']) ? ', '.$this->attributes['atx'] : ''), 
    		$this->attributes['zac']
	    		. (!empty($this->attributes['itx']) ? ', '.$this->attributes['itx'] : ''), 
    		$this->attributes['vpn'], 
    		$this->attributes['vpc']
	    );
	
	    return implode('<br>', $out);
	}
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - End
	 * ==================================================== */
	
}