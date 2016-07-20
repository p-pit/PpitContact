<?php
namespace PpitContact\Model;

use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Filter\StripTags;

class Contract implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
	public $customer_community_id;
	public $customer_bill_contact_id;
    public $supplyer_community_id;
    public $services;
    public $opening_date;
    public $closing_date;
    public $update_time;

    // Joined properties
    public $customer_name;
    public $supplyer_name;

    // Transient properties
	public $customer_community;
	public $supplyer_community;
	
    protected $inputFilter;

    // Static fields
    private static $table;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->customer_community_id = (isset($data['customer_community_id'])) ? $data['customer_community_id'] : null;
        $this->customer_bill_contact_id = (isset($data['customer_bill_contact_id'])) ? $data['customer_bill_contact_id'] : null;
        $this->supplyer_community_id = (isset($data['supplyer_community_id'])) ? $data['supplyer_community_id'] : null;
        $this->services = (isset($data['services'])) ? json_decode($data['services'], true) : null;
        $this->opening_date = (isset($data['opening_date'])) ? $data['opening_date'] : null;
        $this->closing_date = (isset($data['closing_date'])) ? $data['closing_date'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
        
        // Joined properties
        $this->customer_name = (isset($data['customer_name'])) ? $data['customer_name'] : null;
        $this->supplyer_name = (isset($data['supplyer_name'])) ? $data['supplyer_name'] : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['customer_community_id'] =  (int) $this->customer_community_id;
    	$data['customer_bill_contact_id'] =  (int) $this->customer_bill_contact_id;
    	$data['supplyer_community_id'] =  (int) $this->supplyer_community_id;
    	$data['services'] =  json_encode($this->services);
    	$data['opening_date'] =  ($this->opening_date) ? $this->opening_date : null;
    	$data['closing_date'] =  ($this->closing_date) ? $this->closing_date : null;
    	return $data;
    }

    public function getFunctions()
    {
    	return Context::getCurrent()->getConfig()['ppitContactSettings']['functions'];
    }
    
    public static function getList($major, $dir, $filter = array())
    {
		$select = Contract::getTable()->getSelect()
			->join(array('supplyer' => 'contact_community'), 'contact_contract.supplyer_community_id = supplyer.id', array('supplyer_name' => 'name'), 'left')
			->join(array('customer' => 'contact_community'), 'contact_contract.customer_community_id = customer.id', array('customer_name' => 'name'), 'left')
			->order(array($major.' '.$dir, 'supplyer_name', 'customer_name'));
		$where = new Where;
		foreach ($filter as $property => $value) {
			$where->like($property, '%'.$value.'%');
		}
		$select->where($where);
		$cursor = Contract::getTable()->selectWith($select);
		$contracts = array();
		foreach ($cursor as $contract) $contracts[] = $contract;
		return $contracts;
    }
    
    public static function get($id)
    {
    	$contract = Contract::getTable()->get($id);
    	
    	// Retrieve the communities properties
    	$contract->supplyer_community = Community::getTable()->get($contract->supplyer_community_id);
    	if ($contract->supplyer_community) $contract->supplyer_name = $contract->supplyer_community->name;
    	$contract->customer_community = Community::getTable()->get($contract->customer_community_id);
    	$contract->customer_name = $contract->customer_community->name;
    	 
    	return $contract;
    }

    public static function instanciate()
    {
		return new Contract;
    }

    public function loadData($data) {
    
    	// Retrieve the data
    	$this->customer_community_id = (int) $data['customer_community_id'];
    	if (!Community::get($this->customer_community_id)) return 'Integrity';
    	
    	$this->customer_bill_contact_id = (int) $data['customer_bill_contact_id'];
    	if (!Community::get($this->customer_bill_contact_id)) return 'Integrity';
    	
    	$this->supplyer_community_id = (int) $data['supplyer_community_id'];
    	if (!Community::get($this->supplyer_community_id)) return 'Integrity';
    	    	
    	$this->opening_date = trim(strip_tags($data['opening_date']));
    	if ($this->opening_date && !checkdate(substr($this->opening_date, 5, 2), substr($this->opening_date, 8, 2), substr($this->opening_date, 0, 4))) return 'Integrity';
    	
    	$this->closing_date = trim(strip_tags($data['closing_date']));
    	if ($this->closing_date && !checkdate(substr($this->closing_date, 5, 2), substr($this->closing_date, 8, 2), substr($this->closing_date, 0, 4))) return 'Integrity';
    	 
    	return 'OK';
    }
    
    public function loadDataFromRequest($request) {
    
    	$data = array();
    	$data['customer_community_id'] =  $request->getPost('customer_community_id');
    	$data['customer_bill_contact_id'] =  $request->getPost('customer_bill_contact_id');
    	$data['supplyer_community_id'] =  $request->getPost('supplyer_community_id');
    	$data['opening_date'] =  $request->getPost('opening_date');
    	$data['closing_date'] =  $request->getPost('closing_date');
    	if ($this->loadData($data) != 'OK') throw new \Exception('View error');
    }

    public function add()
    {
    	$context = Context::getCurrent();
    
    	// Check consistency
    	if (Generic::getTable()->cardinality('contact_contract', array('supplyer_community_id' => $this->supplyer_community_id, 'customer_community_id' => $this->customer_community_id)) > 0) return 'Duplicate';
    
    	$this->id = null;
    	Contract::getTable()->save($this);
    
    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$Contract = Contract::get($this->id);
    
    	// Isolation check
    	if ($Contract->update_time > $update_time) return 'Isolation';
    
    	Contract::getTable()->save($this);
    
    	return 'OK';
    }

    public function isDeletable()
    {
    	$context = Context::getCurrent();
    
    	// Check dependencies
    	$config = $context->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
    
    	return true;
    }
    
    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$contract = Contract::get($this->id);
    
    	// Isolation check
    	if ($contract->update_time > $update_time) return 'Isolation';
    
    	Contract::getTable()->delete($this->id);
    
    	return 'OK';
    }
    
    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!Contract::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Contract::$table = $sm->get('PpitContact\Model\ContractTable');
    	}
    	return Contract::$table;
    }
}