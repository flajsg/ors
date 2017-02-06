<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;


/**
 * ORMConfirmextra model.
 * 
 * Holds extra booking confirmation information.
 * 
 * @author Gregor Flajs
 *
 */
class ORMConfirmextra extends Eloquent {
	
	protected $fillable = ['ttp', 'act', 'toc', 'knd'];
	
}