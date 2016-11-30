<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Info Picture
 *
 * This is a model contains information about an object picture (you receive from Info requests)
 */

class OAMInfoPicture extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'code', 'name', 'url', 'url_big'
	];

    /**
     * UrlBig attribute.
     * If big picture url exist then url_big is returned, othervise url attribute is returned.
     * @return string
     */
    public function getUrlBigAttribute() {
    	return !empty($this->attributes['url_big']) ? $this->attributes['url_big'] : $this->attributes['url'];
    }
}