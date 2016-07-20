<?php
namespace PpitContact\Model;

use PpitContact\Model\iTarget;
use Zend\db\sql\Where;

define ('TEL_REGEX', "/^\+?([0-9\. ]+)$/");

class UnitaryTarget implements iTarget {

	public $to = array();
	public $filter;
	public $result = array();
	
	public function __construct($message) {
		$this->to = $message->to;
		if (isset($to->filter)) $this->filter = $to->filter;
	}

	public function loadData($data)
	{
		$this->filter = trim(strip_tags($data['filter']));

		$this->to = array();
		if ($this->filter) $this->to['filter'] = $this->filter;
		return 200;
	}

	public function loadDataFromRequest($request)
	{
		$data = array();
		$data['filter'] = $request->getPost('filter');
	    return $this->loadData($data);
	}

	public function compute()
	{
		$select = Vcard::getTable()->getSelect()->order(array('n_fn'))
			->join('customer', 'contact_vcard.id = customer.contact_id', array('customer' => 'id'), 'left')
			->join(array('backup_customer' => 'customer'), 'contact_vcard.id = customer.backup_contact_id', array('backup_customer' => 'id'), 'left');
		$where = new Where();
		if ($this->filter == 'customer') {
			$where->nest->notEqualTo('customer.id', '')->or->notEqualTo('backup_customer.id', '')->unnest;
		}
		$select->where($where);
		$cursor = $this->contactTable->selectWith($select, $this->currentUser);
		$this->to = array();
		foreach ($cursor as $contact) {
			$contact->ok = $this->addTo($contact->tel_cell);
			$this->result[] = $contact;
		}
		return $this->to;
	}
	
	public function addTo($tel_cell)
	{
		// Suppress spaces
		$tel_cell = preg_replace('/\s/', '', $tel_cell);
		
		// Check french cellular phone format (06xxxxxx or 07xxxxxx or +336000000 or +337xxxxxx)
    	if (!preg_match(TEL_REGEX, $tel_cell)) return false;
		if (substr($tel_cell, 0, 2) != '06' && substr($tel_cell, 0, 2) != '07' && substr($tel_cell, 0, 4) != '+336' && substr($tel_cell, 0, 4) != '+337') return false;
    	if ((substr($tel_cell, 0, 2) == '06' || substr($tel_cell, 0, 2) == '07') && strlen($tel_cell) != 10) return false;
		if ((substr($tel_cell, 0, 4) == '+336' || substr($tel_cell, 0, 4) == '+337') && strlen($tel_cell) != 12) return false;
		
    	// Normalize cellular phone number (+336000000 or +337xxxxxx)
		if (substr($tel_cell, 0, 2) == '06' || substr($tel_cell, 0, 2) == '07') $tel_cell = '+33'.substr($tel_cell, 1);

    	$this->to[] = $tel_cell;
		return true;
	}
}
