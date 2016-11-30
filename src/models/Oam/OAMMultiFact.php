<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;

/**
 * ORS API Model: Multi Fact
 * 
 * This is a characteristis group model for Object. 
 * Each Multi Fact can have more then one characteristic items (or facts).
 *
 * @author Gregor Flajs
 */
class OAMMultiFact extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['code', 'text'];
	
	/**
	 * Primary key
	 * @var int
	 */
	protected $primaryKey = 'code';
	
	/**
	 * Facts for this group
	 * @var Collection|OAM\OAMMultiFactItem[]
	 */
	public $facts;
}