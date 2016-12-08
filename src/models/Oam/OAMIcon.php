<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Icon
 *
 * This is a model icons (with icon class and title)
 */

class OAMIcon extends Eloquent {

	/**
	 * Create instance from fact code.
	 * 
	 * @param string $code
	 * @return \Ors\Orsapi\Oam\OAMIcon
	 */
	public static function withCode($code) {
		switch ($code) {
			case 'air':
			    return new self(array('icon' => 'glyphicons glyphicons-snowflake', 'name' => $code));
			case 'wifi':
			    return new self(array('icon' => 'glyphicons glyphicons-wifi', 'name' => $code));
			case 'bea':
			case 'ben':
			    return new self(array('icon' => 'glyphicons glyphicons-beach_umbrella', 'name' => $code));
			case 'pol':
			case 'ipl':
			    return new self(array('icon' => 'glyphicons glyphicons-pool', 'name' => $code));
			case 'whc':
			    return new self(array('icon' => 'fa fa-wheelchair', 'name' => $code));
			case 'spt':
			case 'sws':
			case 'shb':
			case 'sgl':
			case 'srd':
			case 'sae':
			case 'sfr':
			case 'stn':
			case 'sdv':
			case 'sth':
			    return new self(array('icon' => 'glyphicons glyphicons-soccer_ball', 'name' => $code));
			case 'spa':
			case 'wel':
			case 'wms':
			case 'way':
			case 'wth':
			case 'wcu':
			case 'wsn':
			case 'wdt':
			case 'waa':
			case 'wbf':
			case 'wac':
			case 'wap':
			    return new self(array('icon' => 'glyphicons glyphicons-heart_empty', 'name' => $code));
			case 'pet':
			    return new self(array('icon' => 'glyphicons glyphicons-dog', 'name' => $code));
			case 'park':
			    return new self(array('icon' => 'glyphicons glyphicons-car', 'name' => $code));
			case 'chf':
			    return new self(array('icon' => 'fa fa-smile-o', 'name' => $code));
		
			default:
			    return new self(array('icon' => '', 'name' => $code));
		}
	}
	
    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'icon', 'name',
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'icon';
}