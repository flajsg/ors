<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Sort
 *
 * This is a model for a ORS Sorting
 */

class OAMSort extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'code', 'direction',
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * DirectionFull attribute.
     * Full direction name (ascending/descending) instead of (asc/desc)
     * @return string
     */
    public function getDirectionFullAttribute() {
    	return $this->attributes['direction'] == 'asc' ? 'ascending' : 'descending';
    }
}