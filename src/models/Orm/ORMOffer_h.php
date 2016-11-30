<?php namespace Ors\Orsapi\Orm;

use Ors\Support\Common;

/**
 * ORMOffer model implementation for content type hotel (typ=H)
 * 
 * @author Gregor Flajs
 *
 */
class ORMOffer_h extends ORMOffer {

	/**
	 * FullName attribute.
	 * 
	 * Combine full_name  with htn + stc and hon.
	 * 
	 * @return string
	 */
	public function getFullNameAttribute() {
	    $out = $this->attributes['htn'];
	    if ($this->attributes['stc']) $out .= ' '.$this->attributes['stc'].'*';
	    if ($this->attributes['hon']) $out .= sprintf(' (%s)', $this->attributes['hon']);
	    return $out;
	}
	
	/**
	 * Destination attribute.
	 * Return destination combining of rgn and rggn
	 * @return string
	 */
	public function getDestinationAttribute() {
		$dest = array();
	    if (!empty($this->attributes['rggn']))
	        $dest[]=$this->attributes['rggn'];
	    if (!empty($this->attributes['rgn']))
	        $dest[]=$this->attributes['rgn'];
	    return implode('/',$dest);
	}

	/**
	 * Remove category from htn.
	 * @example "Hotel Delfin 3*" is transformed to "Hotel Delfin"
	 * @return string
	 */
	public function getHtnAttribute() {
	    return Common::removeObjectCategory($this->attributes['htn']);
	}
	
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
	 * Details Attribute.
	 * This returns offer details in a string
	 * @return string
	 */
	public function getDetailsAttribute() {
		$out = array();
		$out []= sprintf("%s - %s (%s)", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']), $this->attributes['tdc']);
		$out []= sprintf("<small><span >%s</span> | <span title='%s'>%s</span> | <span title='%s'>%s</span></small>",
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
}