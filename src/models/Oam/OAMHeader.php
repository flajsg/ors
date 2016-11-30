<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Header
 *
 * This model has ORA API response header information.
 */

class OAMHeader extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'typ', 'offers', 'pages', 'perpage', 'offset', 'sid', 'rqid'
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'typ';
}