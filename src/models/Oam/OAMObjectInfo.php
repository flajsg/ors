<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Ors\Support\Common;

/**
 * ORS API Model: Object info
 *
 * Object description, images, map coordinates, ...
 */
class OAMObjectInfo extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	// Touroperator
    	'toc', 'ton', 
    	
    	// Object info
    	'pic', 'htn', 'hon', 'gid', 'stc', 'lat', 'lon', 'htcs', 'ovr', 'emf',

    	// Catalog info
    	'kid', 'kn', 'ks', 'kw', 'catalog_start', 'catalog_end',
    	
    	// Description
    	'desc', 'toc_desc', 'htc_desc', 'lang', 
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'toc';

    /**
     * Object geo location
     * @var OAMGeo
     */
    public $geo;
    
    /**
     * Object pictures
     * @var Collection|OAMInfoPricture[]
     */
    public $pictures;
    
    
    /**
     * Htn attribute.
     * This accessor cleans category from object name.
     *
     * @example "Hotel Delfin 3*" is transformed to "Hotel Delfin"
     * @return string
     */
    public function getHtnAttribute() {
        return preg_replace('/\s+\d*\*+/', '', $this->attributes['htn']);
    }
    
    /**
     * HtcsStr attribute.
     * Implode htcs in string (comma separated) and return a string
     * @return string
     */
    public function getHtcsStrAttribute() {
    	return !empty($this->attributes['htcs']) ? implode(', ', $this->attributes['htcs']) : '';
    }
    
    /**
     * Lang attribute.
     * Some language mapping is handled in this accessor.
     * @return string
     */
    public function getLangAttribute() {
    	if (empty($this->attributes['lang'])) return '';
    	return strtolower($this->attributes['lang']) == 'si' ? 'sl' : $this->attributes['lang'];
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
    
}