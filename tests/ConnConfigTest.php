<?php
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\ConnConfigApiWrapper;
use Ors\Orsapi\Handlers\ConnConfigApiHandler;

define('SEARCH_DEBUG', true);

/**
 * Unit test for testing Connection Config API calls.
 * 
 * @author Gregor Flajs
 *
 */
class ConnConfigTest extends TestCase
{
	
	/**
	 * @return \Ors\Orsapi\ConnConfigApiWrapper
	 */
	public function getInstance() {
		return new ConnConfigApiWrapper(new ConnConfigApiHandler());
	}
	
    public function testListConnections() {
    	
    	$api = $this->getInstance();
    	
    	try {
        	$connections = $api->listConnections();
        	$this->assertTrue(!$connections->isEmpty());
		} catch (\Ors\Orsapi\OrsApiException $e) {
            echo $e;
        }	
    }
    
    public function testMapTocsToConnections() {
    	
    	$api = $this->getInstance();
    	
    	try {
    		$tocs = $api->mapTocsToConnections(['5VF', 'FTI', 'ODP', 'SONH', 'PALM']);
    		$this->assertTrue(!$tocs->isEmpty());
    		//$this->assertSame('sellit', $tocs->find('ODP')->connection);
    		$this->assertSame('old-ors', $tocs->find('FTI')->connection);
    	} catch (\Ors\Orsapi\OrsApiException $e) {
    		echo $e;
    	}
    }
    
    public function testAssignTocToConnection() {
    	
    	$api = $this->getInstance();
    	
    	try {
    		$tocs_org = $api->mapTocsToConnections(['ODP'])->first();
    		
    		// Set different connection and group
    		$res = $api->assignTocToConnection(array(
				new \Ors\Orsapi\ConnConfig\ConectionTocMap(array('toc' => 'ODP', 'connection' => 'old-ors', 'group' => 10))
    		));
    		
    		$tocs_new = $api->mapTocsToConnections(['odp'])->first();
    		
    		$this->assertTrue($res);
    		$this->assertSame(10, $tocs_new->group);
    		$this->assertSame('old-ors', $tocs_new->connection);
    		
    		// Reset toc to original value
    		$res = $api->assignTocToConnection(array(
    		    new \Ors\Orsapi\ConnConfig\ConectionTocMap(array('toc' => 'ODP', 'connection' => $tocs_org->connection, 'group' => $tocs_org->group))
    		));
    		
    		$tocs_new = $api->mapTocsToConnections(['ODP'])->first();
    		
    		$this->assertTrue($res);
    		$this->assertSame(1, $tocs_new->group);
    		$this->assertSame('sellit', $tocs_new->connection);
    		
    	} catch (\Ors\Orsapi\OrsApiException $e) {
    		echo $e;
    	}
    }
    
    public function testDescribeConnection() {
    	
    	$api = $this->getInstance();
    	
    	try {
    	    $descs = $api->describeConnection('phobs');
    	    
    	    $this->assertTrue(!$descs->isEmpty());
    	    
    	    $user_desc = $descs->find('user');
    	    $this->assertSame('string', $user_desc->type);
    	    
   	    } catch (\Ors\Orsapi\OrsApiException $e) {
   	        echo $e;
   	    }
    	
    }
    
    public function testGetConfiguration() {
    	
    	$api = $this->getInstance();
    	
    	try {
    	    $conf = $api->getConfiguration('phobs');
    	    
    	    $this->assertTrue(!empty($conf));
    	    
    	    $conf = $api->getConfiguration('phobs', 5880);
    	    
    	    $this->assertSame('palmasixml', $conf->configuration['user']);
    	    
   	    } catch (\Ors\Orsapi\OrsApiException $e) {
   	        echo $e;
   	    }
    	
    }
    
    public function testSetConfiguration() {
    	
    	$api = $this->getInstance();
    	
    	try {
    		$org_conf = $api->getConfiguration('phobs');
    		
    		// set new language for phobs and test it
    	    $res = $api->setConfiguration(['language' => 'si'], 'phobs');
    	    $new_conf = $api->getConfiguration('phobs');
    	    
    	    $this->assertTrue($res);
    	    $this->assertSame('si', $new_conf->configuration['language']);
    	    
    	    // set back old language for phobs
    	    $res = $api->setConfiguration(['language' => $org_conf->configuration['language']], 'phobs');
    	    $new_conf = $api->getConfiguration('phobs');
    	    
    	    $this->assertTrue($res);
    	    $this->assertSame('en', $new_conf->configuration['language']);
    	    
    	    
    	    $org_conf = $api->getConfiguration('phobs', 6);
    	    
    		// set new language for phobs-ors and test it
    	    $res = $api->setConfiguration(['language' => 'en'], 'phobs', 6);
    	    $new_conf = $api->getConfiguration('phobs', 6);
    	    
    	    $this->assertTrue($res);
    	    $this->assertSame('en', $new_conf->configuration['language']);
    	    
    	    // set back old language for phobs-ors
    	    $res = $api->setConfiguration(['language' => $org_conf->configuration['language']], 'phobs', 6);
    	    $new_conf = $api->getConfiguration('phobs', 6);
    	    
    	    $this->assertTrue($res);
    	    $this->assertSame('si', $new_conf->configuration['language']);
    	    
   	    } catch (\Ors\Orsapi\OrsApiException $e) {
   	        echo $e;
   	    }
    	
    }
}