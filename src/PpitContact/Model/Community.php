<?php
namespace PpitContact\Model;

use PpitContact\Model\Contract;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Community implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $name;
    public $main_contact_id;
    public $main_contact_status;
    public $backup_contact_id;
    public $backup_contact_status;
    public $origine;
    public $root_document_id;
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
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->main_contact_id = (isset($data['main_contact_id'])) ? $data['main_contact_id'] : null;
        $this->main_contact_status = (isset($data['main_contact_status'])) ? $data['main_contact_status'] : null;
        $this->backup_contact_id = (isset($data['backup_contact_id'])) ? $data['backup_contact_id'] : null;
        $this->backup_contact_status = (isset($data['backup_contact_status'])) ? $data['backup_contact_status'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
        $this->root_document_id = (isset($data['root_document_id'])) ? $data['root_document_id'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // => Contract
        $this->vcard_properties = (isset($data['vcard_properties'])) ? json_decode($data['vcard_properties'], true) : null;
        $this->authorized_roles = (isset($data['authorized_roles'])) ? json_decode($data['authorized_roles'], true) : null;
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['name'] =  $this->name;
    	$data['main_contact_id'] =  (int) $this->main_contact_id;
    	$data['main_contact_status'] =  $this->main_contact_status;
    	$data['backup_contact_id'] =  (int) $this->backup_contact_id;
    	$data['backup_contact_status'] =  $this->backup_contact_status;
    	$data['origine'] =  $this->origine;
    	$data['root_document_id'] = (int) $this->root_document_id;
    	
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
    	$select->where($where);
    	$cursor = Community::getTable()->selectWith($select);
    	$communities = array();
    	foreach ($cursor as $community) $communities[] = $community;
    	
		$globalVision = new Community;
		$globalVision->name = '*';
		$communities[0] = $globalVision;

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
    	return new Community;
    }
    
    public function loadData($data) {

    	$this->name = trim(strip_tags($data['name']));
    	if ($this->name == '' || strlen($this->name) > 255) return 'Integrity';

    	$this->main_contact_id = (int) $data['main_contact_id'];
    	if (!$this->main_contact_id) return 'Integrity';

    	$this->main_contact_role = trim(strip_tags($data['main_contact_role']));
    	if (strlen($this->main_contact_role) > 255) return 'Integrity';

    	$this->backup_contact_id = (int) $data['backup_contact_id'];
    	if (!$this->backup_contact_id) return 'Integrity';
    	
    	$this->backup_contact_role = trim(strip_tags($data['backup_contact_role']));
    	if (strlen($this->backup_contact_role) > 255) return 'Integrity';

    	$this->origine = trim(strip_tags($data['origine']));
    	if (strlen($this->origine) > 255) return 'Integrity';
    	 
    	return 'OK';
    }

    public function loadDataFromRequest($request) {
    
		$data = array();
		$data['name'] = $request->getPost('name');
		$data['main_contact_id'] = $request->getPost('main_contact_id');
		$data['main_contact_status'] = $request->getPost('main_contact_status');
		$data['backup_contact_id'] = $request->getPost('backup_contact_id');
		$data['backup_contact_status'] = $request->getPost('backup_contact_status');
		$data['origine'] = $request->getPost('origine');
		if ($this->loadData($data) != 'OK') throw new \Exception('View error');
    }

    public function add()
    {
    	$context = Context::getCurrent();
    
    	// Check consistency
//    	if (Generic::getTable()->cardinality('contact_community', array('name' => $this->name)) > 0) return 'Duplicate';

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
    	if ($Community->update_time > $update_time) return 'Isolation';
    
    	Community::getTable()->save($this);
    
    	return 'OK';
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
    	Community::getTable()->delete($this->id);
    
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