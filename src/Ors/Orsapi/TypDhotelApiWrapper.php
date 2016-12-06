<?php namespace Ors\Orsapi;


class TypDhotelApiWrapper extends SearchApiWrapper {
	
	public function __construct($oa_handler) {
		parent::__construct($oa_handler);
		$this->ctype_id = 'dhotel';
	}
	
	public function objects($params) {
	    return $this->handler()->objects($this->ctype_id, $params);
	}
	
	public function object($params) {
	    return $this->handler()->object($this->ctype_id, $params);
	}
	
	public function availability($params) {
	    return $this->handler()->availability($this->ctype_id, $params);
	}
	
	public function filters() {
	    return $this->handler()->getFilters();
	}
	
	public function sorts() {
	    return $this->handler()->getSorts();
	}
}