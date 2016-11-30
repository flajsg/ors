<?php 
return array(

	/*
	 * Auth user model (defaul: User).
	 * Set other model name here, that handles User authentications.
	 */
	'auth_model' => '\User',

	/*
	 * Auth method (defaul: Auth::user()).
	 * A method to get authenticated user information. 
	 * Usually this is Auth::user(), but if you have your own auth method, you can put it here.
	 * This is used to send user credentials via API. 
	 */
	'auth_method' => Auth::user() ? Auth::user() : null,
)
?>