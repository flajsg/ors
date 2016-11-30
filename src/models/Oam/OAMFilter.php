<?php namespace  Ors\Orsapi\Oam;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * ORS API Model: Filter
 *
 * This is a model for a ORS Filter (toc, vpc, zac, ...). Data are providet from OrsApi call.
 */

class OAMFilter extends Eloquent {

    /**
     * Attributes for this model
     * @var array
     */
    protected $fillable = [
    	'code', 'selected', 'name',
	];

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'code';
    
    /**
     * Object filter values
     * @var Collection|OAMFilterVal[]
     */
    public $values;
    
    /**
     * Override toArray to add additional attributes
     * @return array
     */
    public function toArray(){
        $array = parent::toArray();
        $array['values'] = $this->values->toArray();
        $array['keyed_values'] = $this->values->keyBy('code')->toArray();
        return $array;
    }
}