<?php
namespace PpitContact\Model;

use PpitContact\Model\iTarget;

define ('TEL_REGEX', "/^\+?([0-9\. ]+)$/");

class UnitaryTarget implements iTarget {

	public $to = array();
	public $contactTable;
	public $currentUser;
	
	public function __construct($message, $contactTable, $currentUser, $ontroller) {
		$this->to = $message->to;
		$this->contactTable = $contactTable; 
		$this->currentUser = $currentUser;
	}

	public function loadData($data) {
		$this->to = trim(strip_tags($data['to']));
		if (!$this->to || strlen($this->to) > 255) return 400;
		$this->to = explode(',', $this->to);
		return 200;
	}

	public function loadDataFromRequest($request) {
		$data = array();
	    $data['to'] = $request->getPost('to');
	    return $this->loadData($data);
	}

	public function compute() { return $this->to; }
	
	public function addTo($tel_cell) {

		// Suppres spaces
		$tel_cell = preg_replace('/\s/', '', $tel_cell);
		
		// Check french cellular phone format (06xxxxxx or 07xxxxxx or +336000000 or +337xxxxxx)
    	if (!preg_match(TEL_REGEX, $tel_cell)) return false;
		if (substr($tel_cell, 0, 2) != '06' && substr($tel_cell, 0, 2) != '07' && substr($tel_cell, 0, 4) != '+336' && substr($tel_cell, 0, 4) != '+337') return false;
    	if ((substr($tel_cell, 0, 2) == '06' || substr($tel_cell, 0, 2) == '07') && strlen($tel_cell) != 10) return false;
		if ((substr($tel_cell, 0, 4) == '+336' || substr($tel_cell, 0, 4) == '+337') && strlen($tel_cell) != 12) return false;
		
    	// Normalize cellular phone number (+336000000 or +337xxxxxx)
		if (substr($tel_cell, 0, 2) == '06' || substr($tel_cell, 0, 2) == '07') $tel_cell = '+33'.substr($tel_cell, 1, 7);

    	$this->to[] = $tel_cell;
		return true;
	}
}
