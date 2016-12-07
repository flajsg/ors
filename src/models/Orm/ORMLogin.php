<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Config;

/**
 * Login model form ORM.
 * 
 * This model holds information about who is doing the booking.
 * 
 * If userid is missing (not provided by API) then this model will use Auth::user()->id instead.
 * If ibeid is missing (not provided by API) then this model will use an ID of first user() subaccount.
 *  
 * @author Gregor Flajs
 *
 */
class ORMLogin extends Eloquent {
	
	protected $fillable = ['agn', 'userid', 'usernm', 'userem'];

	public function __construct($attributes = array()) {
		parent::__construct($attributes);
	}

	/**
	 * one-to-one relation (User)
	 * @return HasOne|Relation|User
	 */
	public function user() {
		return $this->hasOne ( Config::get('orsapi::auth_model'), 'id', 'userid' );
	}
}