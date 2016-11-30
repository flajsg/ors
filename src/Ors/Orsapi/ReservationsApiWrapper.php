<?php namespace Ors\Orsapi;

use Ors\Orsapi\OrsApiBase;
use Ors\Orsapi\OrsApiException;
use Ors\Orsapi\Interfaces\ReservationsApiInterface;


/**
 * ORS API Bookings implementation class.
 * 
 * ORS API handler must implement OrsApi_abstract_booking class.
 * 
 * @author Gregor Flajs.
 *
 */
class ReservationsApiWrapper extends OrsApiBase {
	
	
	/**
	 * Create wrapper
	 * @param ReservationsApiInterface $oa_handler
	 * @throws OrsApiException
	 */
	public function __construct($oa_handler) {
	    if ($oa_handler instanceof ReservationsApiInterface)
	        parent::__construct($oa_handler);
	    else
	        throw new OrsApiException('Invalid handler!');
	}
	
	public function search($params, $filters, $search = array()) {
	    return $this->handler()->search($params, $filters, $search);
	}
	
	public function totals($params, $filters, $search = array()) {
	    return $this->handler()->totals($params, $filters, $search);
	}
	
	public function history($params, $bookings) {
	    return $this->handler()->history($params, $bookings);
	}
	
	public function chown($params, $owner, $filters, $search = array()) {
	    return $this->handler()->chown($params, $owner, $filters, $search);
	}
	
	/**
	 * This function creates group with filters.
	 * All filters have the same id (name) and operatos '=' (is).
	 * Values for filters are inside array $values.
	 * 
	 * Example of filter structure:
	 * <code>
	 * 		array([
	 *   		'@attributes' => array('op' => '='),
	 *   		'field' => [
	 *   			array('@attributes' => array(name, ope, value))
	 *   		]
	 *   	]);
	 * </code>
	 *
	 * @param string $name
	 * 		filter id (name)
	 * @param array $values
	 * 		filter values
	 * @param string $op
	 * 		group operator
	 * @return array
	 *		a single group with filters is returned
	 */
	public function makeSimpleFiltersGroup($name, $values ,$op = 'AND') {
	    $group = array([
	        '@attributes' => array('op' => $op),
	        'field' => array()
	    ]);
	
	    foreach ($values as $value) {
	        //$group ['field'] []= array('id' => $name, 'value' => $value, 'operator' => '=', 'check' => true);
	    	$field 	= array('@attributes' => array(
	    	    'name' => $name,
	    	    'op' => '=',
	    	    'value' =>  $value
	    	));
	    	$group['field'][]=$field;
	    }
	    return $group;
	}
}