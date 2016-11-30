<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Icon
 *
 * This is a model icons (with icon class and title)
 */

class OAMIcon extends Eloquent {

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