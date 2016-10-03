<?php
namespace PpitContact\Model;

use PpitContact\Model\Contract;
use PpitCore\Model\Context;
use PpitCore\Model\Credit;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;

class Community implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $activation_date;
    public $next_credit_consumption_date;
    public $last_credit_consumption_date;
    public $status;
    public $name;
    public $contact_1_id;
    public $contact_1_status;
    public $contact_2_id;
    public $contact_2_status;
    public $contact_3_id;
    public $contact_3_status;
    public $contact_4_id;
    public $contact_4_status;
    public $contact_5_id;
    public $contact_5_status;
    public $origine;
    public $root_document_id;
    public $audit;
    public $update_time;

    // => Contract
    public $vcard_properties;
    public $authorized_roles = array();

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
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->activation_date = (isset($data['activation_date'])) ? $data['activation_date'] : null;
        $this->next_credit_consumption_date = (isset($data['next_credit_consumption_date'])) ? $data['next_credit_consumption_date'] : null;
        $this->last_credit_consumption_date = (isset($data['last_credit_consumption_date'])) ? $data['last_credit_consumption_date'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->contact_1_id = (isset($data['contact_1_id'])) ? $data['contact_1_id'] : null;
        $this->contact_1_status = (isset($data['contact_1_status'])) ? $data['contact_1_status'] : null;
        $this->contact_2_id = (isset($data['contact_2_id'])) ? $data['contact_2_id'] : null;
        $this->contact_2_status = (isset($data['contact_2_status'])) ? $data['contact_2_status'] : null;
        $this->contact_3_id = (isset($data['contact_3_id'])) ? $data['contact_3_id'] : null;
        $this->contact_3_status = (isset($data['contact_3_status'])) ? $data['contact_3_status'] : null;
        $this->contact_4_id = (isset($data['contact_4_id'])) ? $data['contact_4_id'] : null;
        $this->contact_4_status = (isset($data['contact_4_status'])) ? $data['contact_4_status'] : null;
        $this->contact_5_id = (isset($data['contact_5_id'])) ? $data['contact_5_id'] : null;
        $this->contact_5_status = (isset($data['contact_5_status'])) ? $data['contact_5_status'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
        $this->root_document_id = (isset($data['root_document_id'])) ? $data['root_document_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // => Contract
        $this->vcard_properties = (isset($data['vcard_properties'])) ? json_decode($data['vcard_properties'], true) : null;
        $this->authorized_roles = (isset($data['authorized_roles'])) ? json_decode($data['authorized_roles'], true) : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] =  $this->status;
    	$data['activation_date'] =  $this->activation_date;
    	$data['next_credit_consumption_date'] = ($this->next_credit_consumption_date) ? $this->next_credit_consumption_date : null;
    	$data['last_credit_consumption_date'] = ($this->last_credit_consumption_date) ? $this->last_credit_consumption_date : null;
    	$data['name'] =  $this->name;
    	$data['contact_1_id'] =  (int) $this->contact_1_id;
    	$data['contact_1_status'] =  $this->contact_1_status;
    	$data['contact_2_id'] =  (int) $this->contact_2_id;
    	$data['contact_2_status'] =  $this->contact_2_status;
    	$data['contact_3_id'] =  (int) $this->contact_3_id;
    	$data['contact_3_status'] =  $this->contact_3_status;
    	$data['contact_4_id'] =  (int) $this->contact_4_id;
    	$data['contact_4_status'] =  $this->contact_4_status;
    	$data['contact_5_id'] =  (int) $this->contact_5_id;
    	$data['contact_5_status'] =  $this->contact_5_status;
    	$data['origine'] =  $this->origine;
    	$data['root_document_id'] = (int) $this->root_document_id;
    	$data['audit'] = json_encode($this->audit);
    	 
    	// => Contract
    	$data['vcard_properties'] = json_encode($this->vcard_properties);
    	$data['authorized_roles'] = json_encode($this->authorized_roles);

    	return $data;
    }

    public static function getList($major, $dir, $filter = array())
    {
    	$select = Community::getTable()->getSelect()->order(array($major.' '.$dir, 'name'));
    	$where = new Where;
    	foreach ($filter as $property => $value) {
    		$where->like($property, '%'.$value.'%');
    	}
    	$where->notEqualTo('status', 'deleted');
    	$select->where($where);
    	$cursor = Community::getTable()->selectWith($select);
    	$communities = array();
    	foreach ($cursor as $community) $communities[$community->id] = $community;
/*    	
		$globalVision = new Community;
		$globalVision->name = '*';
		$communities[0] = $globalVision;*/

    	return $communities;
    }
    
    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();

    	// Access control : only one's community except for the instance manager (context's community == 0)
    	if ($context->getCommunityId()) $id = $context->getCommunityId();
    	
    	$community = Community::getTable()->get($id, $column);
    	return $community;
    }

    public static function instanciate()
    {
    	$community = new Community;
    	$community->status = 'new';
    	return $community;
    }
    
    public function loadData($data) {

    	if (array_key_exists('status', $data)) {
    		$this->status = trim(strip_tags($data['status']));
    		if (strlen($this->status) > 255) return 'Integrity';
    	}
    	 
		if (array_key_exists('name', $data)) {
	    	$this->name = trim(strip_tags($data['name']));
    		if ($this->name == '' || strlen($this->name) > 255) return 'Integrity';
    	}

		if (array_key_exists('contact_1_id', $data)) {
	    	$this->contact_1_id = (int) $data['contact_1_id'];
	    	if (!$this->contact_1_id) return 'Integrity';
		}

		if (array_key_exists('contact_1_status', $data)) {
	    	$this->contact_1_status = trim(strip_tags($data['contact_1_status']));
	    	if (strlen($this->contact_1_status) > 255) return 'Integrity';
		}

		if (array_key_exists('contact_2_id', $data)) {
	    	$this->contact_2_id = (int) $data['contact_2_id'];
	    	if (!$this->contact_2_id) return 'Integrity';
		}
    	
		if (array_key_exists('contact_2_status', $data)) {
	    	$this->contact_2_status = trim(strip_tags($data['contact_2_status']));
	    	if (strlen($this->contact_2_status) > 255) return 'Integrity';
		}

		if (array_key_exists('contact_2_id', $data)) {
			$this->contact_2_id = (int) $data['contact_2_id'];
			if (!$this->contact_2_id) return 'Integrity';
		}
		 
		if (array_key_exists('contact_2_status', $data)) {
			$this->contact_2_status = trim(strip_tags($data['contact_2_status']));
			if (strlen($this->contact_2_status) > 255) return 'Integrity';
		}

		if (array_key_exists('contact_3_id', $data)) {
			$this->contact_3_id = (int) $data['contact_3_id'];
			if (!$this->contact_3_id) return 'Integrity';
		}
		 
		if (array_key_exists('contact_3_status', $data)) {
			$this->contact_3_status = trim(strip_tags($data['contact_3_status']));
			if (strlen($this->contact_3_status) > 255) return 'Integrity';
		}

		if (array_key_exists('contact_4_id', $data)) {
			$this->contact_4_id = (int) $data['contact_4_id'];
			if (!$this->contact_4_id) return 'Integrity';
		}
		 
		if (array_key_exists('contact_4_status', $data)) {
			$this->contact_4_status = trim(strip_tags($data['contact_4_status']));
			if (strlen($this->contact_4_status) > 255) return 'Integrity';
		}

		if (array_key_exists('contact_5_id', $data)) {
			$this->contact_5_id = (int) $data['contact_5_id'];
			if (!$this->contact_5_id) return 'Integrity';
		}
		 
		if (array_key_exists('contact_5_status', $data)) {
			$this->contact_5_status = trim(strip_tags($data['contact_5_status']));
			if (strlen($this->contact_5_status) > 255) return 'Integrity';
		}
		
		if (array_key_exists('origine', $data)) {
	    	$this->origine = trim(strip_tags($data['origine']));
	    	if (strlen($this->origine) > 255) return 'Integrity';
		}
    	 
    	return 'OK';
    }
/*
    public function loadDataFromRequest($request) {
    
		$data = array();
		$data['name'] = $request->getPost('name');
		$data['contact_1_id'] = $request->getPost('contact_1_id');
		$data['contact_1_status'] = $request->getPost('contact_1_status');
		$data['contact_2_id'] = $request->getPost('contact_2_id');
		$data['contact_2_status'] = $request->getPost('contact_2_status');
		$data['origine'] = $request->getPost('origine');
		if ($this->loadData($data) != 'OK') throw new \Exception('View error');
    }*/

    public function add()
    {
    	$context = Context::getCurrent();
    
    	// Check consistency
    	if (Generic::getTable()->cardinality('contact_community', array('name' => $this->name)) > 0) return 'Duplicate';

    	// Create the root document for the new community
    	$rootDoc = new Document;
    	$this->root_document_id = Document::getTable()->save($rootDoc);

    	$this->id = null;
    	Community::getTable()->save($this);

    	$rootDoc->acl = array('contacts' => array(), 'communities' => array($this->id => 'write'));
    	Document::getTable()->save($rootDoc);

    	return ('OK');
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();
    	$Community = Community::get($this->id);
    
    	// Isolation check
    	if ($update_time && $Community->update_time > $update_time) return 'Isolation';
    
    	Community::getTable()->save($this);
    
    	return 'OK';
    }

    public static function consumeCredits($live, $mailTo)
    {
    	$context = Context::getCurrent();
    	$config = $context->getConfig();
    	 
    	// Open log
    	if ($live) {
    		$writer = new Writer\Stream('data/log/console.txt');
    		$logger = new Logger();
    		$logger->addWriter($writer);
    	}
    
    	// Retrieve instances
    	$select = Instance::getTable()->getSelect();
    	$cursor = Instance::getTable()->selectWith($select);
    	$instances = array();
    	$instanceIds = array();
    	foreach ($cursor as $instance) {
    		$unlimitedCredits = (array_key_exists('unlimitedCredits', $instance->specifications)) ? $instance->specifications['unlimitedCredits'] : false;
    
    		// Log
    		if ($config['isTraceActive']) {
    			$logText = 'Instance : id='.$instance->id.', caption='.$instance->caption.', unlimitedCredits='.(($unlimitedCredits) ? 'true' : 'false');
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    		}
    
    		if (!$unlimitedCredits) {
    			$instance->administrators = array();
    			$instances[$instance->id] = $instance;
    			$instanceIds[] = $instance->id;
    		}
    	}
    
    	// Retrieve credits
    	$select = Credit::getTable()->getSelect();
    	$where = new Where();
    	$where->in('instance_id', $instanceIds);
    	$where->equalTo('type', 'p-pit-communities');
    	$select->where($where);
    	$cursor = Credit::getTable()->transSelectWith($select);
    	$credits = array();
    	foreach ($cursor as $credit) {
    		$credit->consumers = array();
    		$credits[$credit->instance_id] = $credit;
    	}
    
    	// Retrieve communities and count
    	$select = Community::getTable()->getSelect()
	    	->join('core_instance', 'contact_community.instance_id = core_instance.id', array(), 'left');
    	$where = new Where();
    	$where->in('instance_id', $instanceIds);
    	$where->notEqualTo('contact_community.status', 'closed');
    	$where->notEqualTo('contact_community.status', 'suspended');
    	$select->where($where);
    	$cursor = Community::getTable()->transSelectWith($select);
    	foreach ($cursor as $community) {
    		if (array_key_exists($community->instance_id, $credits)) $credits[$community->instance_id]->consumers[] = $community;
    	}
    
    	// Retrieve administrators to be notified
    	$select = Vcard::getTable()->getSelect();
    	$where = new Where;
    	$where->like('roles', '%admin%');
    	$select->where($where);
    	$cursor = Vcard::getTable()->transSelectWith($select);
    	foreach ($cursor as $contact) {
    		if ($contact->is_notified) $instances[$contact->instance_id]->administrators[] = $contact;
    	}
    
    	// Check enough credits are available
    	foreach ($credits as $credit) {
  
			// Compute the credit consumption at -7 days and at due date
    		$counter7 = 0;
			$counter0 = 0;
			$dailyConsumption = 0;
			$creditModified = false;
			foreach($credit->consumers as $community) {
    			if ($community->next_credit_consumption_date <= date('Y-m-d', strtotime(date('Y-m-d').' + 7 days'))) $counter7++;
    			if ($community->next_credit_consumption_date <= date('Y-m-d')) $counter0++;
    			
    			if ($community->next_credit_consumption_date == date('Y-m-d')) {
	    			// Consume 1 credit
    				$dailyConsumption++;
    				$credit->quantity--;
					$community->next_credit_consumption_date = date('Y-m-d', strtotime(date('Y-'.(date('m') + 1).'-'.substr($community->activation_date, 8, 2)).' + 0 days'));
					$community->last_credit_consumption_date = date('Y-m-d');
    				$community->audit[] = array(
    						'status' => $community->status,
    						'time' => Date('Y-m-d G:i:s'),
    						'n_fn' => 'P-PIT',
    						'comment' => 'Monthly consuming',
    				);
    				if ($live) Community::getTable()->transSave($community);
    
    				// Log
    				if ($config['isTraceActive']) {
   						$logText = 'Community : instance_id='.$community->instance_id.', id='.$community->id.', caption='.$community->name.', status='.$community->status;
    					if ($live) $logger->info($logText);
    					else print_r($logText."\n");
    				}
					$creditModified = true;
    			}
			}
			// Suspend the subscription service if no enough credits at due date
			if ($credit->quantity < 0) {
				if ($credit->status == 'active') {
					$credit->status = 'suspended';
					$creditModified = true;
				}
			}
			// Re-activate the subscription service if enough credits at due date
			else {
				if ($credit->status == 'suspended') {
					$credit->status = 'active';
    				$creditModified = true;
				}
			}
			if ($creditModified) {
    			$credit->audit[date('Y-m')] = array(
    					'status' => $credit->status,
    					'quantity' => $credit->quantity,
    					'time' => Date('Y-m-d G:i:s'),
    					'n_fn' => 'P-PIT',
    					'comment' => 'Daily consuming',
    			);
    			if ($live) Credit::getTable()->transSave($credit);
			}

			// Notify a suspension of service
    		if ($credit->status == 'suspended') {

    			// Log
    			$logText = 'ALERT : Not enough credits for P-PIT Communities available on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    			
    			// Notify
    			if ($live) {
    				$url = $config['ppitCoreSettings']['domainName'];
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['community/consumeCredit']['messages']['suspendedServiceTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['community/consumeCredit']['messages']['suspendedServiceText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								$credit->quantity
    						);
    						ContactMessage::sendMail($contact->email, $text, $title);
    					}
    				}
    			}
    		}
    		elseif ($credit->quantity - $counter7 < 0) {
    
    			// Log
    			$logText = 'ALERT : Risk of credits lacking for P-PIT Communities on instance '.$credit->instance_id.'. Available='.$credit->quantity.', 7 days estimation='.$counter7;
    			if ($live) $logger->info($logText);
    			else print_r($logText."\n");
    
    			// Notify
    			if ($live) {
    				$url = $config['ppitCoreSettings']['domainName'];
    				$instance = $instances[$credit->instance_id];
    				foreach ($instance->administrators as $contact) {
    					if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    						$title = sprintf($config['community/consumeCredit']['messages']['availabilityAlertTitle'][$contact->locale], 'P-PIT Communities');
    						$text = sprintf(
    								$config['community/consumeCredit']['messages']['availabilityAlertText'][$contact->locale],
    								$contact->n_first,
    								$instance->caption,
    								$credit->quantity,
    								$counter7
    								);
    						ContactMessage::sendMail($contact->email, $text, $title);
    					}
    				}
    			}
    		}
    		// Notify is a consumption accurs
    		if ($dailyConsumption > 0)
    		{
    			$logText = 'Consuming '.$dailyConsumption.' credits for instance: '.$credit->instance_id;
    			if ($live) {
    				$connection = Credit::getTable()->getAdapter()->getDriver()->getConnection();
    				$connection->beginTransaction();
    				try {
    
    					// Notify
    					$url = $config['ppitCoreSettings']['domainName'];
    					$instance = $instances[$credit->instance_id];
    					foreach ($instance->administrators as $contact) {
    						if (!$mailTo || !strcmp($contact->email, $mailTo)) { // Restriction on the given mailTo parameter
    							$title = sprintf($config['community/consumeCredit']['messages']['consumeCreditTitle'][$contact->locale], 'P-PIT Communities');
    							$text = sprintf(
    									$config['community/consumeCredit']['messages']['consumeCreditText'][$contact->locale],
    									$contact->n_first,
    									Context::sDecodeDate(date('Y-m-d'), $contact->locale),
    									$instance->caption,
    									count($credit->consumers),
    									$credit->quantity
    									);
    							ContactMessage::sendMail($contact->email, $text, $title);
    						}
    					}
    					$connection->commit();
    
    					// Log
    					$logger->info($logText);
    				}
    				catch (\Exception $e) {
    					$connection->rollback();
    					throw $e;
    				}
    			}
    			else {
    				if ($config['isTraceActive']) print_r($logText."\n");
    			}
    
    		}
    	}
    }
    
    public function isUsed($object)
    {
    	// Allow or not deleting an instance
    	if (get_class($object) == 'PpitCore\Model\Instance') {
    		if (Community::getTable()->get($object->community_id)) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function isDeletable() {

    	// Check dependency on instances
    	$select = Instance::getTable()->getSelect()->where(array('community_id' => $this->id));
    	if (count(Instance::getTable()->selectWith($select)) > 0) return false;

    	// Check dependency on functional relationships between communities
    	$select = Contract::getTable()->getSelect()
    		->where(array('supplyer_community_id' => $this->id));
    	$cursor = Contract::getTable()->selectWith($select);
    	foreach ($cursor as $relation) if (!$relation->isDeletable()) return false;

    	$select = Contract::getTable()->getSelect()
	    	->where(array('customer_community_id' => $this->id));
    	$cursor = Contract::getTable()->selectWith($select);
    	foreach ($cursor as $relation) if (!$relation->isDeletable()) return false;
    	
    	// Check other dependencies
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}

    	return true;
    }

    public function delete($update_time)
    {
    	$context = Context::getCurrent();
    	$community = Community::get($this->id);
    
    	// Isolation check
    	if ($community->update_time > $update_time) return 'Isolation';
    
//		Document::getTable()->delete($this->root_document_id);
		$this->status = 'deleted';
    	Community::getTable()->update($this);
    
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
    	if (!Community::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Community::$table = $sm->get('PpitContact\Model\CommunityTable');
    	}
    	return Community::$table;
    }
}