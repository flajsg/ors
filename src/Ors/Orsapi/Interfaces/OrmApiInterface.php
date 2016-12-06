<?php namespace Ors\Orsapi\Interfaces;

/**
 * Interface for ORM API handlers.
 *
 * @author Gregor Flajs
 *
 */
interface OrmApiInterface {
	
	/**
	 * ORM call
	 * @param \Ors\Support\CRSFieldInterface $params
	 * 		parameters to create API header
	 * @param \Ors\Orsapi\Orm\Orm $orm
	 * 		orm object
	 *
	 * @return \Ors\Orsapi\Orm\ORM
	 * 		api returns another ORM object
	 * @throws \Ors\Orsapi\OrsApiException
	 */
	public function orm($params, $orm);
	
}