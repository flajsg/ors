<?php

return array(

	/*
	 * API url 
	 */
	'api_url' => 'http://ors.si/orsxml2-dev/testing/public/passengers/',
		
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
	 * - test_master_key: test agency master key 
	 */
	'test_user_id' => '',
	'test_agency_id' => '',
	'test_master_key' => '',
);
?>