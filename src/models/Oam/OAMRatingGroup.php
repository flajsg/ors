<?php namespace Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: RatingGroup
 * 
 * This are the ratings of specific group of voters.
 */

class OAMRatingGroup extends Eloquent {
	
	/**
	 * Attributes for this model
	 * @var array
	 */
	protected $fillable = ['id', 'ovr', 'htl', 'loc', 'svc', 'fod', 'rom', 'spt', 'emf'];
	
	/**
	 * Primary key
	 * @var string
	 */
	protected $primaryKey = 'id';
	
}