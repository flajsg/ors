<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Info
 *
 * This is a wrapper model for all object info data
 */

class OAMInfo extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'toc', 'gid', 'htc',
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'toc';

    /**
     * Object info
     * @var OAMObjectInfo
     */
    public $object;
    
    /**
     * Object ratings
     * @var OAMRating
     */
    public $ratings;
    
    /**
     * Object characteristics
     * @var Collection|OAMMultiFact[]
     */
    public $characteristics;
}