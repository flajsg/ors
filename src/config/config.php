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
	
	/*
	 * You can put here your agency ID or pur a function call that will
	 * return auth agency id.
	 */
	'agency_id' => '',
	
	/*
	 * You can put here your agency master_key or pur a function call that will
	 * return auth agency master_key.
	 */
	'master_key' => '',
	
	/*
	 * TEST ENVIRONMENT VALUES (used for unit testing):
	 * - test_user_id: test user id
	 * - test_agency_id: test agency id
	 * - test_ibeid: test ibeid (branch office or other ORS chanel)
	 * - test_master_key: test agency master key
	 */
	'test_user_id' => '',
	'test_agency_id' => '',
	'test_ibeid' => '',
	'test_master_key' => '',
)
?>