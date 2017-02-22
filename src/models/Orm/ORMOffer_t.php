<?php namespace Ors\Orsapi\Orm;

use Ors\Support\Common;

/**
 * ORMOffer model implementation for content type trips (typ=T)
 * 
 * @author Gregor Flajs
 *
 */
class ORMOffer_t extends ORMOffer {

	/**
	 * Details Attribute.
	 * This returns offer details in a string
	 * @return string
	 */
	public function getDetailsAttribute() {
		$out = array();
		
		if ($this->attributes['vnd'] && $this->attributes['bsd']) {
		    $date = sprintf("%s - %s", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']));
		    if ( $this->attributes['tdc']) $date .= sprintf(" (%s)", $this->attributes['tdc']);
		    $out []= $date;
		}
		
		$details = array();
		
		// Show toc only if zac and vpc is also available.
		// This is so that details is empty if there is no info about zac/vpc and we can use service->details instead.
		if ($this->attributes['toc']) {
		    $details[]=sprintf("<span >%s</span>", $this->attributes['toc']);
		}
		
		if ($this->attributes['ztx'] || $this->attributes['ltx'] || $this->attributes['atx'])
	        $details[]=sprintf("<span >%s</span>",
	              (!empty($this->attributes['ztx']) ?      $this->attributes['ztx'] : '')
	            . (!empty($this->attributes['ltx']) ? ', '.$this->attributes['ltx'] : '')
	            . (!empty($this->attributes['atx']) ? ', '.$this->attributes['atx'] : '')
	        );
		    	
		if ($this->attributes['itx'])
		    $details[]=sprintf("<span >%s</span>",
		        (!empty($this->attributes['itx']) ? $this->attributes['itx'] : '')
		    );
		    	
	    if ($details)
	        $out []= implode(' | ', $details);
		
		return implode('<br>', $out);
	}
}