<?php namespace Ors\Orsapi\Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;


/**
 * ORMConfirmation model.
 * 
 * Holds booking confirmation information (when executing DR or XR requests).
 * 
 * @author Gregor Flajs
 *
 */
class ORMConfirmation extends Eloquent {
	
	protected $fillable = ['confirminfo', 'agency', 'confirmremark', 'confirmdetails', 'confirmextra', 'confirmheader'];

	/**
	 * confirmextra attribute.
	 * (Extra booking confirmation information)
	 * @return ORMConfirmextra
	 */
	public function getConfirmextraAttribute() {
		if (empty($this->attributes['confirmextra'])) return new ORMConfirmextra();
		
		return $this->attributes['confirmextra'] instanceof ORMConfirmextra ? $this->attributes['confirmextra'] : 
		new ORMConfirmextra($this->attributes['confirmextra']);
	}
	
	/**
	 * confirmheader attribute.
	 * (Booking confirmation header information)
	 * @return ORMConfirmheader
	 */
	public function getConfirmheaderAttribute() {
		if (empty($this->attributes['confirmheader'])) return new ORMConfirmextra();
		
		return $this->attributes['confirmheader'] instanceof ORMConfirmheader ? $this->attributes['confirmheader'] : 
		new ORMConfirmheader($this->attributes['confirmheader']);
	}
}