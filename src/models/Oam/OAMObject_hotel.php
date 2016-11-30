<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Collection;
use Ors\Support\Common;

/**
 * OAMObject model implementation for content type hotel
 * 
 * @author Gregor Flajs
 *
 */
class OAMObject_hotel extends OAMObject {
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - Start
	 * ==================================================== */

	/**
	 * Cart Offer Name attribute.
	 * This is used to display Offer Name in Cart list view.
	 * @return string
	 */
	public function getCartOfferNameAttribute() {
		if (empty($this->attributes['htn']))
			return null;
		$out = $this->attributes['htn'];
		if (!empty($this->attributes['stc']))
			$out .= ' '.$this->attributes['stc'].'*';
		if (!empty($this->attributes['hon']))
			$out .= ' ('.$this->attributes['hon'].')';
		return $out;
	}
	
	/* ==================================================== *
	 * ABSTRACT methods (Implementation) - End
	 * ==================================================== */
	
	/**
	 * Stc attribute (category).
	 * Category is type double and if it is 0.0 then empty() still returns FALSE,
	 * so we must handle this here to remove category if it is 0.0 
	 * 
	 * @example "0.0" is transformed to ""
	 * @return string 
	 */
	public function getStcAttribute() {
		if ( empty($this->attributes['stc']) || floatval($this->attributes['stc']) == 0)
			return "";
		return $this->attributes['stc'];
	}
	
	
	/**
	 * OvrReal attribute.
	 * This accessor returns rating from 0-5 instead of percents.
	 * @return int|NULL
	 */
	public function getOvrRealAttribute() {
	    if (!empty($this->attributes['ovr'])) {
	        return Common::percent2rating($this->attributes['ovr']);
	    }
	    return null;
	}
	
	/**
	 * OvrFull attribute.
	 * Return full overall rating (ie: "4/5")
	 * @return string
	 */
	public function getOvrFullAttribute() {
	    if (!empty($this->OvrReal))
	        return sprintf("%d/5", $this->OvrReal);
	    return null;
	}
	
	/**
	 * OvrColor Attribute.
	 * Depending on OvrReal this accessor return a bootstrap css class for color.
	 * @return string|NULL
	 */
	public function getOvrColorAttribute() {
		return Common::ratingColor($this->OvrReal);
	}
	
	/**
	 * EmfFull attribute.
	 * Return full recommendation with 2 decimals and a percent sign
	 * @return string
	 */
	public function getEmfFullAttribute() {
	    if (!empty($this->attributes['emf']))
	        return sprintf("%.2f %%", $this->attributes['emf']);
	    return null;
	}
	
	/**
	 * DestinationFull attribute.
	 * Return full destination name with group/region/city
	 * @return string
	 */
	public function getDestinationFullAttribute() {
	    $dest = array();
	    if (!empty($this->attributes['rggn']))
	        $dest[]=$this->attributes['rggn'];
	    if (!empty($this->attributes['rgn']))
	        $dest[]=$this->attributes['rgn'];
	    if (!empty($this->attributes['hon']))
	        $dest[]=$this->attributes['hon'];
	    return implode('/',$dest);
	}
	
	/**
	 * DestinationSemi attribute.
	 * Return semi destination name with region/city
	 * @return string
	 */
	public function getDestinationSemiAttribute() {
	    $dest = array();
	    if (!empty($this->attributes['rgn']))
	        $dest[]=$this->attributes['rgn'];
	    if (!empty($this->attributes['hon']))
	        $dest[]=$this->attributes['hon'];
	    return implode('/',$dest);
	}
	
	/**
	 * @return Collection|OAMIcon[]
	 */
	public function getFactsIconsAttribute() {
	    $icons = new Collection();
	     
	    foreach ($this->facts as $f) {
	        if (!empty($f->icon))
	            $icons->push($f->icon);
	    }
	    return $icons->unique();
	}
	
}