<?php namespace Ors\Orsapi;
use PDOException;
use Lang;

class OrsApiException extends  PDOException {
	
	/**
	 * Ors Api request id
	 * @var string
	 */
	protected $rqid;
	
	/**
	 * Additional note for this exception, beside message.
	 * @var string
	 */
	protected $note;
	
	// Redefine the exception so message isn't optional
	public function __construct($message, $code = 0, PDOException $previous = null, $rqid = null, $note = null) {
	    // make sure everything is assigned properly
	    parent::__construct($message, $code, $previous);
	    
	    $this->rqid = $rqid;
	    $this->note = $note;
	    
	    $this->setNoteFromCode($code);
	}
	
	// custom string representation of object
	public function __toString() {
	    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
	
	public function setNote($note) {
	    $this->note = $note;
	}
	
	public function getRqid() {
	    return $this->rqid;
	}
	
	public function getNote() {
	    return $this->note;
	}
	
	public function hasNote() {
		return !empty($this->note);
	}
	
	public function __toJson() {
		return AjaxResponse::error($this->message);
	}

	/**
	 * Set note by exception code.
	 * By calling this method, you will overwrite current note value.
	 * @param int $code
	 */
	public function setNoteFromCode($code) {
		if (Lang::has('orsapi_err_codes.'.$code))
			$this->note = Lang::get('orsapi_err_codes.'.$code);
	}
}
?>