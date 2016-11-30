<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;

/**
 * ORS API Model: Rating
 * 
 * This is an Object rating info. Each rating have some general attributes 
 * plus ratings of different groups of voters.
 */

class OAMRating extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['gid', 'hcid', 'cnt', 'emf'];
	
	/**
	 * Primary key
	 * @var int
	 */
	protected $primaryKey = 'gid';
	
	/**
	 * Rating groups
	 * @var Collection|OAMRatingGroup[]
	 */
	public $groups;
}