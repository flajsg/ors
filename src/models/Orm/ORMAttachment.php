<?php  namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORMConfirmation model.
 *
 * Holds booking confirmation information (when executing DR or XR requests).
 *
 * @author Gregor Flajs
 *
 */
class ORMAttachment extends Eloquent {
	
	protected $fillable = ['cny', 'eml', 'fax', 'mob', 'csn', 'ast', 'arm', 'anm', 'ink', 'vurl', 'pdf'];
	
}