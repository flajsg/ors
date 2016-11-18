<?php namespace Ors\Orsapi; 

use Ors\Orsapi\OrsApiBase;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\ConnConfigApiInterface;
use Ors\Support\Common;

/**
 * This API exports methods needed to pass general or agency-specific configuration to a specific connection. 
 *
 * This is just a wrapper for API handler.
 * 
 * @author Gregor Flajs
 *
 */
class ConnConfigApiWrapper extends OrsApiBase {
	
	/**
	 * Create wrapper
	 * @param ConnConfigApiInterface $oa_handler
	 * @throws OrsApiException
	 */
	public function __construct(ConnConfigApiInterface $oa_handler) {
		if ($oa_handler instanceof ConnConfigApiInterface)
	    	parent::__construct($oa_handler);
		else
			throw new OrsApiException('Invalid handler!');
	}
	
	/**
	 * @return \Ors\Orsapi\Handlers\ConnConfigApiHandler
	 */
	public function handler() { return $this->oa_handler; }
	
	public function hello() {
		Common::ppre('Hi there!');
	}
	
	/**
	 * Return Connection object based on connection_id
	 * 
	 * @param int $connection_id
	 * @return \Ors\ConnConfig\Connection
	 */
	public function findConnection($connection_id) {
		return $this->handler()->listConnections()->find($connection_id);
	}
	
	public function listConnections() {
	    return $this->handler()->listConnections();
	}
	
	public function mapTocsToConnections($tocs) {
	    return $this->handler()->mapTocsToConnections($tocs);
	}
	
	/**
	 * Return mapped toc information
	 *
	 * @param int $connection_id
	 * @return \Ors\ConnConfig\ConnectionTocMap
	 */
	public function findMappedToc($toc) {
	    return $this->handler()->mapTocsToConnections([$toc])->first();
	}
	
	public function assignTocToConnection($tocs) {
	    return $this->handler()->assignTocToConnection($tocs);
	}
	
	public function describeConnection($connection) {
	    return $this->handler()->describeConnection($connection);
	}
	
	public function getConfiguration($connection, $agid = null) {
	    return $this->handler()->getConfiguration($connection, $agid);
	}
	
	public function setConfiguration($configuration, $connection, $agid = null) {
	    return $this->handler()->setConfiguration($configuration, $connection, $agid);
	}
}
