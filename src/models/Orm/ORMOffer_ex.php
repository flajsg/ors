<?php namespace Ors\Orsapi\Orm;

use Ors\Support\Common;

/**
 * ORMOffer model implementation for content type extras (typ=EX)
 * 
 * @author Gregor Flajs
 *
 */
class ORMOffer_ex extends ORMOffer {

	/**
	 * Details Attribute.
	 * This returns offer details in a string
	 * @return string
	 */
	public function getDetailsAttribute() {
		$out = array();
		
		if (!empty($this->attributes['vnd']) && !empty($this->attributes['bsd']))
			$out []= sprintf("%s - %s", Common::date($this->attributes['vnd']), Common::date($this->attributes['bsd']));
		elseif (!empty($this->attributes['vnd']) && empty($this->attributes['bsd']))
			$out []= sprintf("%s", Common::date($this->attributes['vnd']));
		elseif (empty($this->attributes['vnd']) && !empty($this->attributes['bsd']))
			$out []= sprintf("%s", Common::date($this->attributes['bsd']));
		$out []= sprintf("<small><span >%s</span> ",
			$this->attributes['toc']
		);
		return implode('<br>', $out);
	}
}