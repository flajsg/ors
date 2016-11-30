<?php namespace Ors\Orsapi\Oam; 

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Auth model. Use this model to set API login credentials when calling any ORS API method.
 * 
 * @author Gregor Flajs
 *
 */
class OAMAuth extends Eloquent{
	
	protected $fillable = ['agid', 'ibeid', 'master_key', 'usr', 'pass'];
	
	
}