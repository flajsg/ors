<?php
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\SearchApiWrapper;
use Ors\Orsapi\TypHotelApiWrapper;
use Ors\Orsapi\Handlers\SearchApiHandler;
use Ors\Orsapi\Oam\OAMAuth;


define('SEARCH_DEBUG', true);

/**
 * Unit test for testing Search API calls.
 * 
 * @author Gregor Flajs
 *
 */
class SearchApiTest extends TestCase { 

	private $api;
	
	/**
	 * Set API test credentials
	 */
	public function setUp() {
	    parent::setUp();
	}
	
	public function tearDown() {
	    parent::tearDown();
	}
	
	/**
	 * Get basic API auth info.
	 * @return \Ors\Orsapi\Oam\OAMAuth
	 */
	public function getAuth() {
	    return new OAMAuth(array(
	        'agid' => Config::get('orsapi::test_agency_id'),
	        'ibeid' => Config::get('orsapi::test_ibeid'),
	        'master_key' => Config::get('orsapi::test_master_key')
	    ));
	}
	
	/**
	 * Return instance of api wrapper and login a test user.
	 *
	 * @return \Ors\Orsapi\PassengerApiWrapper
	 */
	public function getHotelApi() {
	    return new TypHotelApiWrapper(new SearchApiHandler($this->getAuth()));
	}
	
	/**
	 * Test **regions**
	 */
	public function testHotelRegions() {
		
		$api = $this->getHotelApi();
		
		try {
			$params = array(
			    'epc' => 2,
			    'vnd' => date('Y-m-d'),
			    'bsd' => date("Y-m-d", strtotime("+1 month")),
			    'tdc' => '3-3',
			    'uniqid' => '123456789',
			    //'rgcs' => 1108,
			    //'gid' => 6715,
			);
			
			$list = $api->regions($params);
			
			$this->assertTrue(!$list->isEmpty());
			
		}catch (\Ors\Orsapi\OrsApiException $e) {
			echo $e;
		}
	}
	
	/**
	 * Test **objects**
	 */
	public function testHotelObjects() {
	
	    $api = $this->getHotelApi();
	
	    try {
	        $params = array(
	            'epc' => 2,
	            'vnd' => date('Y-m-d'),
	            'bsd' => date("Y-m-d", strtotime("+3 months")),
	            'tdc' => '3-3',
	            'uniqid' => '123456789',
	            'rgcs' => 1108,
	            //'gid' => 6715,
	        );
	        	
	        $list = $api->objects($params);
	        	
	        $this->assertTrue(!$list->isEmpty());
	        	
	    }catch (\Ors\Orsapi\OrsApiException $e) {
	        echo $e;
	    }
	}
	
	/**
	 * Test **object**
	 */
	public function testHotelOffers() {
	
	    $api = $this->getHotelApi();
	
	    try {
	        $params = array(
	            'epc' => 2,
	            'vnd' => date('Y-m-d'),
	            'bsd' => date("Y-m-d", strtotime("+3 months")),
	            'tdc' => '3-3',
	            'uniqid' => '123456789',
	            'rgcs' => 1108,
	        );
	
	        $objects = $api->objects($params);
	        $list = $api->object($params+array('gid' => $objects->first()->gid));
	
	        $this->assertTrue(!$list->offers->isEmpty());
	
	    }catch (\Ors\Orsapi\OrsApiException $e) {
	        echo $e;
	    }
	}
	
	/**
	 * Test **availability**
	 */
	public function testAvailability() {
		
		$api = $this->getHotelApi();
		
		try {
		    $params = array(
		        'epc' => 2,
		        'vnd' => date('Y-m-d'),
		        'bsd' => date("Y-m-d", strtotime("+3 months")),
		        'tdc' => '3-3',
		        'uniqid' => '123456789',
		        'rgcs' => 1108,
		    );
		
		    $objects = $api->objects($params);
		    
		    $object = $api->object($params+array('gid' => $objects->first()->gid));
		    
		    $av = $api->availability($params+array('hsc' => $object->offers->first()->hsc, 'toc' => $object->offers->first()->toc));
		
		    $this->assertTrue($av);
		
		}catch (\Ors\Orsapi\OrsApiException $e) {
		    echo $e;
		}
	}
}