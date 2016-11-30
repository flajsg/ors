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
	
	protected $fillable = ['agn', 'userid', 'usernm', 'userem', 'ibeid'];

	public function __construct($attributes = array()) {
		/*
		// Sometimes only userid is returned inside $attributes, so we must find the other information from User
		if (empty($attributes['usernm']) && !empty($attributes['userid'])) {
			$user = User::find($attributes['userid']);
			if ($user) {
				$attributes['usernm'] = $user->name;
				$attributes['userem'] = $user->email;
				$attributes['agn'] = $user->account->name;
			}
		}
		
		// if userid is missing than simply use Auth::user->id
		if (empty($attributes['userid']))
			$attributes['userid'] = Auth::user()->id;*/
		
		parent::__construct($attributes);
	}

	/*public static function withAuthUser() {
		$attributes = array(
			'userid' => Auth::user()->id,
			'usernm' => Auth::user()->name,
			'userem' => Auth::user()->email,
			'agn' => Auth::user()->account->name,
			'ibeid' => Auth::user()->subaccounts->first()->id,
		);
		return new self($attributes);
	}*/
	
	/**
	 * ibeid accessor.
	 * Return User first subaccount id if 'ibeid' attribute is not set.
	 * If user 
	 * @return int
	 */
	/*public function getIbeidAttribute() {
		if (!empty($this->attributes['ibeid'])) return $this->attributes['ibeid'];
		
		if ($this->user) {
			return $this->user->subaccounts->first()->id;
		}
	}*/
	
	/**
	 * one-to-one relation (User)
	 * @return HasOne|Relation|User
	 */
	public function user() {
		return $this->hasOne ( Config::get('orsapi::auth_model'), 'id', 'userid' );
	}
	
	/**
	 * one-to-one relation (Subaccount)
	 * @return HasOne|Relation|Subaccount
	 */
	/*public function subaccount() {
		return $this->hasOne ( 'Subaccount', 'id', 'ibeid' );
	}*/
	
}