<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;


/**
 * ORMConfirmheader model.
 * 
 * Holds booking confirmation header information.
 * 
 * @author Gregor Flajs
 *
 */
class ORMConfirmheader extends Eloquent {
	
	protected $fillable = ['tlf', 'fax', 'cpy', 'cfm', 'ctp', 'sst'];
	
}