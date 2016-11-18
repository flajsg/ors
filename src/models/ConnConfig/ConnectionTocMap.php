<?php namespace Ors\Orsapi\ConnConfig;

use Illuminate\Database\Eloquent\Model as Eloquent;
use ConnConfig;

/**
 * ORS Connection toc-map model.
 *
 * To which connection is specific tos mapped.
 *
 * @author Gregor Flajs
 *
 */
class ConnectionTocMap extends Eloquent{
	
	protected $fillable = ['toc', 'connection', 'group'];
	
	protected $tocs = [];
	
	protected $primaryKey = 'toc';
	
	/**
	 * Return Connection that this toc is mapped to
	 * 
	 * @return \Ors\ConnConfig\Connection
	 */
	public function connection() {
		return ConnConfig::findConnection($this->attributes['connection']);
	}
	
}