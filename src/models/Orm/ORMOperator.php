<?php namespace Ors\Orsapi\Orm;

use Ors\Orsapi\Oam\OAMAvailabilityOperator;

/**
 * ORM Operator
 *
 * This id operator info.
 */

class ORMOperator extends OAMAvailabilityOperator {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	// same as availability
    	'ctype_id', 'act', 'toc', 'hsc', 'agt', 'ibeid', 'knd',
    	
    	// extended for Orm
    	'bkc', 'psn', 'pgc', 'mst', 'mfz', 'exp', 'prc', 'pr2', 'clt', 'bst', 'actc', 'rmk',

    	// dates
    	'created_at', 'modified_at',
	];

    /**
     * Return TRUE if this is a booking. If this is not yet a booking then return false.
     * @return boolean
     */
    public function isBooking() {
    	return !empty($this->attributes['bkc']);
    }
    
}