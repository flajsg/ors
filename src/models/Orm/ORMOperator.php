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
    	'ctype_id', 'act', 'toc', 'hsc', 'agt',
    	
    	// extended for Orm
    	'bkc', 'psn', 'knd', 'pgc', 'mst', 'mfz', 'exp', 'prc', 'pr2', 'clt', 'bst', 'actc', 'rmk',

    	// dates
    	'created_at', 'modified_at',
	];

    /*
     * ACCESSORS
     */
    /*
    public function getResellerNameAttribute() {
    	if ($this->isResellerAgt())
    		return $this->agt()->resellerAgt->reseller->name;
    	return '';
    }
    
    public function isResellerAgt() {
    	return false;
    	//return $this->agt() && $this->agt()->isResellerAgt();
    }
    */
    
    /**
     * Return TRUE if this is a booking. If this is not yet a booking then return false.
     * @return boolean
     */
    public function isBooking() {
    	return !empty($this->attributes['bkc']);
    }
    
    /**
     * This is Agt object (agency number) 
     * @return Agt
     * 
     */
   /* public function agt() {
    	return $this->subaccount()->findAgtByToc($this->toc);
    }*/
    
    /**
     * @return Subaccount
     */
    /*public function subaccount() {
    	return Subaccount::find($this->ibeid);
    }*/
    
}