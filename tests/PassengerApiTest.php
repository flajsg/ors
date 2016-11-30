<?php
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\PassengerApiWrapper;
use Ors\Orsapi\Handlers\PassengerApiHandler;


define('SEARCH_DEBUG', true);

/**
 * Unit test for testing Passenger API calls.
 * 
 * @author Gregor Flajs
 *
 */
class PassengerApiTest extends TestCase 
{
	private $user_id;
	private $agency_id;
	private $master_key;
	
	/**
	 * \Ors\Orsapi\PassengerApiWrapper
	 * @var unknown
	 */
	private $api;
	
	/**
	 * Test passenger id
	 * @var int
	 */
	private $test_pid;
	
	/**
	 * Set API test credentials
	 */
	public function setUp() {
		parent::setUp();
		
		$this->login();
		$this->api = $this->getApi();
		$this->createTestPassenger();
	}
	
	public function tearDown() {
		parent::tearDown();
		$this->deleteTestPassenger();
	}
		
	public function login() {
		$this->user_id = Config::get('orsapi::passenger.test_user_id');
		$auth_model = Config::get('orsapi::auth_model');
		if (class_exists($auth_model))
			$user = $auth_model::find($this->user_id);
		else
			$user = User::find($this->user_id);
		$this->be($user);
		
		if (!$this->agency_id) {
			$this->agency_id = Config::get('orsapi::passenger.test_agency_id');
			$this->master_key = Config::get('orsapi::passenger.test_master_key');
		}
	}
	
	/**
	 * Return instance of api wrapper and login a test user.
	 * 
	 * @return \Ors\Orsapi\PassengerApiWrapper
	 */
	public function getApi() {
		return new PassengerApiWrapper(new PassengerApiHandler($this->agency_id, $this->master_key));
	}
	
	public function createTestPassenger() {
		try {
			$this->test_pid = $this->api->add(array(
		        'agency_id' => $this->agency_id,
		        'sex' => 'H',
		        'first_name' => '__Unit1234',
		        'last_name' => 'Test',
		        'email' => 'gregor.flajs@ors.si',
		    ));
			
		    $this->assertGreaterThan(0, (int)$this->test_pid);
		    
		} catch (\Ors\Orsapi\OrsApiException $e) {
		    echo $e;
		}
	}
	
	public function deleteTestPassenger() {
		try {
		    $this->assertTrue($this->api->delete($this->test_pid));
		} catch (\Ors\Orsapi\OrsApiException $e) {
		    echo $e;
		}
	}
	
	
	/**
	 * Test *getall*
	 */
    public function testAll() {
    	try {
    		$list = $this->api->all();
    		$this->assertTrue(!$list->isEmpty());
    	} catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }
    }
    
    /**
     * Test *add* and *getpassengersbyid* and *remove*
     */
    public function testAdd() {
    	try {
    	    $passenger = $this->api->find($this->test_pid);
    	    $this->assertSame('__Unit1234', $passenger->first_name);
    	} catch (\Ors\Orsapi\OrsApiException $e) {
    	    echo $e;
    	}
    }
    
    /**
     * Test *quicksearch*
     */
    public function testSearch() {
        try {
            $list = $this->api->search("__Unit1234");
            $this->assertTrue(!$list->isEmpty());
        } catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }
    }
    
    /**
     * Test *edit*
     */
    public function testUpdate() {
        try {
            $this->assertTrue($this->api->update(array(
            	'id' => $this->test_pid,
            	'email' => 'gregorflajs@ors.si',
            )));
            
            $passenger = $this->api->find($this->test_pid);
            $this->assertSame('gregorflajs@ors.si', $passenger->email);
        } catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }
    }
    
    /**
     * Test *undelete*
     */
    public function testUndelete() {
        try {
        	$this->deleteTestPassenger();
            $this->assertTrue($this->api->undelete($this->test_pid));
            $passenger = $this->api->find($this->test_pid);
            $this->assertFalse($passenger->deleted);
        } catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }
    }
    
    /**
     * Test *link* and *unlink*
     */
    public function testLinkUnlink() {
    	
    	try {
    	    $this->linked_pid = $this->api->add(array(
    	        'agency_id' => $this->agency_id,
    	        'sex' => 'H',
    	        'first_name' => '__UnitLink',
    	        'last_name' => 'Test',
    	    ));
    	
            $this->assertTrue($this->api->link($this->test_pid, [$this->linked_pid]));
            $this->assertTrue($this->api->unlink([$this->linked_pid]));
            $this->assertTrue($this->api->delete([$this->linked_pid]));
        } catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }
    }
}