<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for Connection configuration API handler.
 * 
 * @author Gregor Flajs
 *
 */
interface ConnConfigApiInterface {

	/**
	 * Lists all registered connections on ORSXML2 database and prints out their information and assigned tour operators. 
	 *
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#list-connections
	 *
	 * @return Collection|\Ors\Orsapi\Connection
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function listConnections();
	
	/**
	 * Given list of tour operator codes, maps to what connection and group each tour operator is assigned to. .
	 *
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#map-tocs-to-connections
	 *
	 * @param array $tocs
	 * 		a list of tocs for which we need information about connection they are mapped to.
	 *
	 * @return Collection|\Ors\Orsapi\ConnConfig\ConnectionTocMap
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function mapTocsToConnections(array $tocs);
	
	/**
	 * Maps tour operator and their groups to specific connection.
	 * 
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#assign-toc-to-connection
	 *  
	 * @param Collection|array|\Ors\Orsapi\ConnConfig\ConnectionTocMap $tocs
	 * 
	 * @return boolean
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function assignTocToConnection($tocs);
	
	/**
	 * Maps tour operator and their groups to specific connection.
	 *
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#describe-connection
	 *
	 * @param string $connection
	 * 		connection id
	 *
	 * @return Collection|\Ors\Orsapi\ConnConfig\ConnectionDescription
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function describeConnection($connection);
	
	/**
	 * Returns configuration of specific connection. 
	 *
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#get-configuration
	 *
	 * @param string $connection
	 * 		connection id
	 * @param int|null $agid
	 * 		agency id (account id). If agid is null, then general connection configuration is returned, 
	 * 		else agency specific configuration is returned.
	 *
	 * @return \Ors\Orsapi\ConnConfig\ConnectionConfiguration
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function getConfiguration($connection, $agid = null);
	
	/**
	 * Sets either global or agency-specific configuration on connection.  
	 *
	 * @link http://ors.si/wiki/doku.php?id=ors:orsxml2:connconfig-api#set-configuration
	 *
	 * @param array|\Ors\Orsapi\ConnConfig\ConnectionConfiguration $configuration
	 * @param string $connection
	 * 		connection id
	 * @param int|null $agid
	 * 		agency id (account id). If agid is null, then general connection configuration is set, 
	 * 		else agency specific configuration is set.
	 *
	 * @return boolean
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function setConfiguration($configuration, $connection, $agid = null);
}