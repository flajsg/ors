<?php namespace Ors\Orsapi\Interfaces;

/**
 * Abstract class for orm ORS API calls.
 *
 * @author Gregor Flajs
 *
 */
interface OrmApiInterface {
	
	/**
	 * ORM call
	 * @param \Ors\Support\SmartSearchParameters $params
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