<?php
namespace PpitContact\Model;

use PpitContact\Model\VcardProperty;
use PpitCore\Model\Context;
use PpitCore\Model\Functions;
use PpitCore\Model\Generic;
use PpitCore\Model\Instance;
use PpitDocument\Model\Document;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Vcard implements InputFilterAwareInterface
{
    public $id;
	public $instance_id;
	public $attributed_credits;
	public $last_credit_consumption_date;
	public $community_id;
	public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $org;
    public $tel_work;
    public $tel_cell;
    public $email;
    public $adr_street;
    public $adr_extended;
    public $adr_post_office_box;
    public $adr_zip;
    public $adr_city;
    public $adr_state;
    public $adr_country;
    public $sex;
    public $birth_date;
    public $place_of_birth;
    public $nationality;
    public $origine;
    public $photo_link_id;
    public $roles = array();
    public $perimeters;
    public $locale;
    public $is_demo_mode_active;
    public $is_notified;
    public $update_time;

    // Additional properties from joined tables
    public $community_name;
    
    // Transient properties
    public $authorized_roles;
    public $previous_n_last;
    public $previous_n_first;
    public $previous_email;
    public $previous_tel_cell;
    public $files;
    public $properties;
    
    protected $inputFilter;
    protected $devisInputFilter;

    // Static fields
    private static $table;
    public static $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
    public static $telRegex = "/^\+?([0-9\. ]*)$/";

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->attributed_credits = (isset($data['attributed_credits'])) ? json_decode($data['attributed_credits'], true) : null;
        $this->last_credit_consumption_date = (isset($data['last_credit_consumption_date'])) ? $data['last_credit_consumption_date'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->org = (isset($data['org'])) ? $data['org'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->adr_street = (isset($data['adr_street'])) ? $data['adr_street'] : null;
        $this->adr_extended = (isset($data['adr_extended'])) ? $data['adr_extended'] : null;
        $this->adr_post_office_box = (isset($data['adr_post_office_box'])) ? $data['adr_post_office_box'] : null;
        $this->adr_zip = (isset($data['adr_zip'])) ? $data['adr_zip'] : null;
        $this->adr_city = (isset($data['adr_city'])) ? $data['adr_city'] : null;
        $this->adr_state = (isset($data['adr_state'])) ? $data['adr_state'] : null;
        $this->adr_country = (isset($data['adr_country'])) ? $data['adr_country'] : null;
        $this->sex = (isset($data['sex'])) ? $data['sex'] : null;
        $this->birth_date = (isset($data['birth_date'])) ? $data['birth_date'] : null;
        $this->place_of_birth = (isset($data['place_of_birth'])) ? $data['place_of_birth'] : null;
        $this->nationality = (isset($data['nationality'])) ? $data['nationality'] : null;
        $this->origine = (isset($data['origine'])) ? $data['origine'] : null;
        $this->roles = (isset($data['roles'])) ? json_decode($data['roles'], true) : null;
        $this->perimeters = (isset($data['perimeters'])) ? json_decode($data['perimeters'], true) : null;
        $this->locale = (isset($data['locale'])) ? $data['locale'] : null;
        $this->is_notified = (isset($data['is_notified'])) ? $data['is_notified'] : null;
        $this->is_demo_mode_active = (isset($data['is_demo_mode_active'])) ? $data['is_demo_mode_active'] : null;
        $this->photo_link_id = (isset($data['photo_link_id'])) ? $data['photo_link_id'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

    	// Additional properties from joined tables
	    $this->community_name = (isset($data['community_name'])) ? $data['community_name'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['attributed_credits'] = json_encode($this->attributed_credits);
    	$data['last_credit_consumption_date'] = ($this->last_credit_consumption_date) ? $this->last_credit_consumption_date : null;
    	$data['community_id'] = (int) $this->community_id;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['org'] = $this->org;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['email'] = $this->email;
    	$data['adr_street'] = $this->adr_street;
    	$data['adr_extended'] = $this->adr_extended;
    	$data['adr_post_office_box'] = $this->adr_post_office_box;
    	$data['adr_zip'] = $this->adr_zip;
    	$data['adr_city'] = $this->adr_city;
    	$data['adr_state'] = $this->adr_state;
    	$data['adr_country'] = $this->adr_country;
    	$data['sex'] = $this->sex;
    	$data['birth_date'] = ($this->birth_date) ? $this->birth_date : null;
    	$data['place_of_birth'] = $this->place_of_birth;
    	$data['nationality'] = $this->nationality;
    	$data['origine'] = $this->origine;
    	$data['roles'] = json_encode($this->roles);
    	$data['perimeters'] = json_encode($this->perimeters);
    	$data['locale'] = $this->locale;
    	$data['is_notified'] = $this->is_notified;
    	$data['is_demo_mode_active'] = $this->is_demo_mode_active;
    	$data['photo_link_id'] = $this->photo_link_id;
    	 
    	return $data;
    }
    
	public static function optimize($vcard)
	{
		// Determine if the contact change (change in the identifying data)
		if ($vcard->n_last != $vcard->previous_n_last
		||	$vcard->n_first != $vcard->previous_n_first
		|| 	($vcard->email && $vcard->email != $vcard->previous_email)
		||	($vcard->tel_cell && $vcard->tel_cell != $vcard->previous_tel_cell)) {
			$vcard->id = null;
			
	    	// Check for an existing contact : same community_id and first name and last name and (email or cellular)
	    	if ($vcard->email || $vcard->tel_cell) {
			    $select = Vcard::getTable()->getSelect()
			    	->where(array('community_id' => $vcard->community_id, 'n_first' => $vcard->n_first, 'n_last' => $vcard->n_last));
			    if ($vcard->email) {
				   	$select->where->equalTo('email', $vcard->email);
			    }
			    else {
					$select->where->equalTo('tel_cell', $vcard->tel_cell);
			    }
			    $cursor = Vcard::getTable()->selectWith($select);
				if (count($cursor) > 0) {
					$vcard->id = $cursor->current()->id;
					$vcard->n_fn = $cursor->current()->n_fn;
					if (!$vcard->org) $vcard->org = $cursor->current()->org;
					if (!$vcard->tel_work) $vcard->tel_work = $cursor->current()->tel_work;
					if (!$vcard->tel_cell) $vcard->tel_cell = $cursor->current()->tel_cell;
					if (!$vcard->email) $vcard->email = $cursor->current()->email;
					if (!$vcard->adr_street) $vcard->adr_street = $cursor->current()->adr_street;
					if (!$vcard->adr_extended) $vcard->adr_extended = $cursor->current()->adr_extended;
					if (!$vcard->adr_post_office_box) $vcard->adr_post_office_box = $cursor->current()->adr_post_office_box;
					if (!$vcard->adr_zip) $vcard->adr_zip = $cursor->current()->adr_zip;
					if (!$vcard->adr_city) $vcard->adr_city = $cursor->current()->adr_city;
					if (!$vcard->adr_state) $vcard->adr_state = $cursor->current()->adr_state;
					if (!$vcard->adr_country) $vcard->adr_country = $cursor->current()->adr_country;
					if (!$vcard->sex) $vcard->sex = $cursor->current()->sex;
					if (!$vcard->birth_date) $vcard->birth_date = $cursor->current()->birth_date;
					if (!$vcard->place_of_birth) $vcard->place_of_birth = $cursor->current()->place_of_birth;
					if (!$vcard->nationality) $vcard->nationality = $cursor->current()->nationality;
					if (!$vcard->photo_link_id) $vcard->photo_link_id = $cursor->current()->photo_link_id;
					if (!$vcard->locale) $vcard->locale = $cursor->current()->locale;
					if (!$vcard->is_notified) $vcard->is_notified = $cursor->current()->is_notified;
					if (!$vcard->is_demo_mode_active) $vcard->is_demo_mode_active = $cursor->current()->is_demo_mode_active;
					if (!$vcard->roles) $vcard->role = $cursor->current()->roles;
					if (!$vcard->perimeters) $vcard->perimeters = $cursor->current()->perimeters;
				}
	    	}
		}
		if ($vcard->n_fn) {
			if ($vcard->files) {
	    		if ($context->getCommunityId()) {
	    			$community = Community::get($context->getCommunityId());
	    			$root_id = $community->root_document_id;
	    		}
	    		else $root_id = Document::getTable()->get(0, 'parent_id')->id; 
	    		$document = Document::instanciate($root_id);
	    		$document->files = $this->files;
	    		$document->saveFile();
	    		$vcard->photo_link_id = $document->save();
	    	}
	    	Vcard::getTable()->save($vcard);
		}
    	return $vcard;
	}

    public static function getList($community_id, $params, $major, $dir, $mode = '')
    {
    	$context = Context::getCurrent();
    
    	// Prepare the SQL request
    	if (!$major) $major = 'n_last';
    	if (!$dir) $dir = 'ASC';
    	$select = Vcard::getTable()->getSelect();
    	
    	$where = new Where();

    	// Access control
    	$community = Community::get($community_id);
    	if ($community) $community_id = $community->id; else $community_id = 0;
    	$where->equalTo('community_id', $community_id);

    	if ($mode == 'todo') {
    	
    		// Todo : Limit the list size
    	}
    	else {
    			
    		// Set the filters
    		if (isset($params['n_fn'])) $where->like('n_fn', '%'.$params['n_fn'].'%');
    		if (isset($params['email'])) $where->like('email', '%'.$params['email'].'%');
    		if (isset($params['tel_cell'])) $where->like('tel_cell', '%'.$params['tel_cell'].'%');
    		if (isset($params['tel_work'])) $where->like('tel_work', '%'.$params['tel_work'].'%');
    		if (isset($params['adr_city'])) $where->like('adr_city', '%'.$params['adr_city'].'%');
    		if (isset($params['adr_state'])) $where->like('adr_state', '%'.$params['adr_state'].'%');
    		if (isset($params['adr_country'])) $where->like('adr_country', '%'.$params['adr_country'].'%');
    		if (isset($params['sex'])) $where->equalTo('sex', $params['sex']);
    		if (isset($params['min_birth_date'])) $where->greaterThanOrEqualTo('birth_date', $params['min_birth_date']);
			if (isset($params['max_birth_date'])) $where->lessThanOrEqualTo('birth_date', $params['max_birth_date']);
    		if (isset($params['place_of_birth'])) $where->like('place_of_birth', '%'.$params['place_of_birth'].'%');
    		if (isset($params['nationality'])) $where->like('nationality', '%'.$params['nationality'].'%');
    		if (isset($params['is_notified'])) $where->like('is_notified', '%'.$params['is_notified'].'%');
    		if (isset($params['is_demo_mode_active'])) $where->like('is_demo_mode_active', '%'.$params['is_demo_mode_active'].'%');
    	}

    	$select->where($where)->order(array($major.' '.$dir, 'n_fn'));
    	$cursor = Vcard::getTable()->selectWith($select);
    
    	// Execute the request
    	$vcards = array();
    	foreach ($cursor as $vcard) {
    		$vcards[$vcard->id] = $vcard;
    		if ($community) $vcard->community_name = $community->name;
    	}
    	return $vcards;
    }

    public static function getNew($community_id)
    {
    	$context = Context::getCurrent();
    	$vcard = new Vcard;
    	$vcard->roles = array();
    	$vcard->perimeters = array();

    	// Access control
    	$community = null;
//		$vcard->instance_id = $context->getInstanceId();
    	if ($community_id) {
    		$community = Community::getTable()->get($community_id);
    		if ($community) $vcard->community_id = $community_id;
    		else $vcard->community_id = $context->getCommunityId();
    	}
    	else $vcard->community_id = $context->getCommunityId();

    	$vcard->properties = array();

    	// Retrieve the authorized roles
/*    	$roleList = $context->getConfig()['ppitRoles'];

    	$vcard->authorized_roles = array();
    	if ($community) {
    		foreach ($community->authorized_roles as $roleId) {
    			$role = $roleList[$roleId];
    			if ($role['show']) $vcard->authorized_roles[$roleId] = array('labels' => $role['labels'], 'isChecked' => false);
    		}
    	}
    	else {
    		foreach ($roleList as $roleId => $role) {
    			if ($role['show']) $vcard->authorized_roles[$roleId] = array('labels' => $role['labels'], 'isChecked' => false);
    		}
    	}*/
    	
    	return $vcard;
    }
    
    public static function instanciate($community_id = 0)
    {
    	$vcard = new Vcard;
		$vcard->community_id = $community_id;
		$vcard->properties = array();
    	$vcard->attributed_credits = array();
    	$vcard->roles = array();
    	$vcard->perimeters = array();
    	$vcard->locale = 'fr_FR';
    	$vcard->properties = $vcard->toArray();
    	return $vcard;
    }

    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$vcard = Vcard::getTable()->get($id, $column);
    	if ($vcard) {
	    	$instance_id = $vcard->instance_id;
	    	$community_id = $vcard->community_id;
	    	 
	    	// Access control
	    	$community = null;
	    	if ($instance_id) {
	    		$instance = Instance::getTable()->get($instance_id);
	    		if (!$instance) return null;
	    	}
	    	if ($community_id) {
	    		$community = Community::getTable()->get($community_id);
	    		if (!$community) return null;
	    	}
	    
	    	if ($community_id) $vcard->community_name = $community->name;

	    	$roles = array();
	    	foreach ($vcard->roles as $role) $roles[$role] = $role;
	    	$vcard->roles = $roles;
	    	
	    	// Retrieve the authorized roles
/*	    	$roleList = $context->getConfig()['ppitRoles'];
	    	$vcard->authorized_roles = array();
	    	if ($community) {
	    		foreach ($community->authorized_roles as $roleId) {
	    			$role = $roleList[$roleId];
	    			if ($role['show']) $vcard->authorized_roles[$roleId] = array('labels' => $role['labels'], 'isChecked' => false);
	    		}
	    	}
	    	else {
	    		foreach ($roleList as $roleId => $role) {
	    			if ($role['show']) $vcard->authorized_roles[$roleId] = array('labels' => $role['labels'], 'isChecked' => false);
	    		}
	    	}
	    	foreach ($vcard->roles as $role) if (array_key_exists($role, $vcard->authorized_roles)) $vcard->authorized_roles[$role]['isChecked'] = true;*/
	
			$vcard->properties = $vcard->toArray();
    	}
    	return $vcard;
    }

    public function loadData($data, $community_id = 0)
    {
    	$context = Context::getCurrent();

    	// Save the identifying previous data
    	$this->previous_n_last = $this->n_last;
    	$this->previous_n_first = $this->n_first;
    	$this->previous_email = $this->email;
    	$this->previous_tel_cell = $this->tel_cell;

    	if (array_key_exists('attributed_credits', $data)) {
	    	foreach($data['attributed_credits'] as $credit => $checked) {
	    		if ($checked) $this->attributed_credits[$credit] = true;
	    		else unset($this->attributed_credits[$credit]);
	    	}
    	}    	
    	if (array_key_exists('community_id', $data)) $this->community_id = (int) $data['community_id'];
    	if (array_key_exists('n_title', $data)) {
    		$this->n_title =  trim(strip_tags($data['n_title']));
    		if (strlen($this->n_title) > 255) return 'Integrity';
    	}
    	if (array_key_exists('n_last', $data)) {
    		$this->n_last =  trim(strip_tags($data['n_last']));
    		if ($this->n_last == '' || strlen($this->n_last) > 255) return 'Integrity';
    	}
    	if (array_key_exists('n_first', $data)) {
    		$this->n_first =  trim(strip_tags($data['n_first']));
    		if ($this->n_first == '' || strlen($this->n_first) > 255) return 'Integrity';
    	}
    	if (array_key_exists('email', $data)) {
    		$this->email =  trim(strip_tags($data['email']));
    		if (strlen($this->email) > 255) return 'Integrity';
    		if ($this->email && !preg_match(Vcard::$emailRegex, $this->email)) return 'Integrity';
    	}
    	if (array_key_exists('tel_work', $data)) {
    		$this->tel_work =  trim(strip_tags($data['tel_work']));
	    	if (strlen($this->tel_work) > 255) return 'Integrity';
	    	if ($this->tel_work && !preg_match(Vcard::$telRegex, $this->tel_work)) return 'Integrity';
	    }
    	if (array_key_exists('tel_cell', $data)) {
    		$this->tel_cell =  trim(strip_tags($data['tel_cell']));
	    	if (strlen($this->tel_cell) > 255) return 'Integrity';
	    	if ($this->tel_cell && !preg_match(Vcard::$telRegex, $this->tel_cell)) return 'Integrity';
    	}
    	if (array_key_exists('adr_street', $data)) {
    		$this->adr_street = trim(strip_tags($data['adr_street']));
    		if (strlen($this->adr_street) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_extended', $data)) {
    		$this->adr_extended = trim(strip_tags($data['adr_extended']));
    		if (strlen($this->adr_extended) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_post_office_box', $data)) {
    		$this->adr_post_office_box = trim(strip_tags($data['adr_post_office_box']));
    		if (strlen($this->adr_post_office_box) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_zip', $data)) {
    		$this->adr_zip = trim(strip_tags($data['adr_zip']));
    		if (strlen($this->adr_zip) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_city', $data)) {
    		$this->adr_city = trim(strip_tags($data['adr_city']));
    		if (strlen($this->adr_city) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_state', $data)) {
    		$this->adr_state = trim(strip_tags($data['adr_state']));
    		if (strlen($this->adr_state) > 255) return 'Integrity';
    	}
    	if (array_key_exists('adr_country', $data)) {
    		$this->adr_country = trim(strip_tags($data['adr_country']));
    		if (strlen($this->adr_country) > 255) return 'Integrity';
    	}
    	if (array_key_exists('sex', $data)) {
    		$this->sex = trim(strip_tags($data['sex']));
    		if (strlen($this->sex) > 255) return 'Integrity';
    	}
    	if (array_key_exists('birth_date', $data)) {
    		$this->birth_date = trim(strip_tags($data['birth_date']));
			if ($this->birth_date && !checkdate(substr($this->birth_date, 5, 2), substr($this->birth_date, 8, 2), substr($this->birth_date, 0, 4))) return 'Integrity';
       	}
    	if (array_key_exists('place_of_birth', $data)) {
    		$this->place_of_birth = trim(strip_tags($data['place_of_birth']));
    		if (strlen($this->place_of_birth) > 255) return 'Integrity';
    	}
    	if (array_key_exists('nationality', $data)) {
    		$this->nationality = trim(strip_tags($data['nationality']));
    		if (strlen($this->nationality) > 255) return 'Integrity';
    	}
    	if (array_key_exists('locale', $data)) {
    		$this->locale = trim(strip_tags($data['locale']));
    		if (strlen($this->locale) > 255) return 'Integrity';
    	}
    	if (array_key_exists('is_notified', $data)) {
    		$this->is_notified = $data['is_notified'];
    	}
    	if (array_key_exists('is_demo_mode_active', $data)) {
    		$this->is_demo_mode_active = (int) $data['is_demo_mode_active'];
    	}
    	if (array_key_exists('roles', $data)) {
	    	$roles = array();
/*	    	foreach ($this->roles as $role) {
	    		if (array_key_exists($role, $data['roles'])) {
	    			if ($data['roles']) $roles[] = $role;
	    		}
	    		else $roles[] = $role;
	    	}*/
	    	foreach($data['roles'] as $role => $checked) {
	    		if ($checked) $roles[$role] = $role;
	    	}
	    	$this->roles = $roles;
    	}
    	if (array_key_exists('perimeters', $data)) {
	    	foreach ($data['perimeters'] as $perimeterId => $perimeter) {
	    		$this->perimeters[$perimeterId] = $perimeter;
	    	}
    	}
    	$this->n_fn = $this->n_last.', '.$this->n_first;

    	// Retrieve the photo file
    	if (array_key_exists('file', $data)) $this->file = $data['file'];
    
    	return 'OK';
    }
    
    public function loadDataFromRequest($request, $community_id)
    {
    	$context = Context::getCurrent();
    
    	$data = array();
    	if ($request->getPost('community_id')) $data['community_id'] = $request->getPost('community_id');
    	$data['n_title'] = $request->getPost('n_title');
    	$data['n_last'] =  $request->getPost('n_last');
    	$data['n_first'] = $request->getPost('n_first');
    	$data['email'] = $request->getPost('email');
    	$data['tel_work'] = $request->getPost('tel_work');
    	$data['tel_cell'] = $request->getPost('tel_cell');
    
    	// Retrieve the input value for authorized properties (restriction list at community level, no restriction if no community)
    	if (!$this->properties || array_key_exists('adr_street', $this->properties)) $data['adr_street'] = $request->getPost('adr_street');
    	if (!$this->properties || array_key_exists('adr_extended', $this->properties)) $data['adr_extended'] = $request->getPost('adr_extended');
    	if (!$this->properties || array_key_exists('adr_post_office_box', $this->properties)) $data['adr_post_office_box'] = $request->getPost('adr_post_office_box');
    	if (!$this->properties || array_key_exists('adr_zip', $this->properties)) $data['adr_zip'] = $request->getPost('adr_zip');
    	if (!$this->properties || array_key_exists('adr_city', $this->properties)) $data['adr_city'] = $request->getPost('adr_city');
    	if (!$this->properties || array_key_exists('adr_state', $this->properties)) $data['adr_state'] = $request->getPost('adr_state');
    	if (!$this->properties || array_key_exists('adr_country', $this->properties)) $data['adr_country'] = $request->getPost('adr_country');
    	if (!$this->properties || array_key_exists('sex', $this->properties)) $data['sex'] = $request->getPost('sex');
    	if (!$this->properties || array_key_exists('birth_date', $this->properties)) $data['birth_date'] = $request->getPost('birth_date');
    	if (!$this->properties || array_key_exists('place_of_birth', $this->properties)) $data['place_of_birth'] = $request->getPost('place_of_birth');
    	if (!$this->properties || array_key_exists('nationality', $this->properties)) $data['nationality'] = $request->getPost('nationality');
    	 
    	$data['roles'] = array();
    	foreach ($this->authorized_roles as $id => $role) {

    		if ($request->getPost('role_'.$id)) {
    			$this->authorized_roles[$id]['isChecked'] = true;
    			$data['roles'][] = $id;
    		}
    		else $this->authorized_roles[$id]['isChecked'] = false;
    	}
    	
    	$data['is_notified'] = $request->getPost('is_notified');
    	$data['is_demo_mode_active'] = $request->getPost('is_demo_mode_active');
    	 
    	// Retrieve the photo file
    	$files = $request->getFiles()->toArray();
    	if (array_key_exists('vcard_photo', $files)) $data['file'] = $files['vcard_photo'];
    	 
    	if ($this->loadData($data, $community_id) != 'OK') throw new \Exception('View error');
    }
    
    public function add()
    {
    	$context = Context::getCurrent();
		// Save the photo and the vcard
            	 
    	// Save the order form and the order
    	if ($this->files) {
    		if ($context->getCommunityId()) {
    			$community = Community::get($context->getCommunityId());
    			$root_id = $community->root_document_id;
    		}
    		else $root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->photo_link_id = $document->save();
    	}
       	Vcard::getTable()->save($this);

    	return 'OK';
    }
    
    public function update($update_time)
    {
    	$context = Context::getCurrent();

    	// Check isolation
    	if ($update_time) {
		    $vcard = Vcard::getTable()->transGet($this->id);
		    if ($vcard && $vcard->update_time > $update_time) return 'Isolation';
    	}
    	// Save the photo and the vcard
    	if ($this->files) {
    		if ($context->getCommunityId()) {
    			$community = Community::get($context->getCommunityId());
    			$root_id = $community->root_document_id;
    		}
    		else $root_id = Document::getTable()->get(0, 'parent_id')->id; 
    		$document = Document::instanciate($root_id);
    		$document->files = $this->files;
    		$document->saveFile();
    		$this->photo_link_id = $document->save();
    	}
       	Vcard::getTable()->save($this);

	    return 'OK';
    }

    public function saveFile($file) {
    	$context = Context::getCurrent();
    	if ($context->getInstanceId() != 0) {
	    	$context = Context::getCurrent();
    		if ($file['size'] > $context->getConfig()['ppitCoreSettings']['maxUploadSize']) $error = 'Size';
    		else {
    			$name = $file['name'];
    			$type = $file['type'];
    			$src = $this->id;
    			$dest = $this->id.'.jpg';
    			$path = 'data/photos/';
    
    			$adapter = new \Zend\File\Transfer\Adapter\Http();

    			// Create the file on the file system with $id as a name
    			$adapter->addFilter('Rename', $path.$src);
    			if (file_exists($path.$src)) unlink($path.$src);
    			if ($adapter->receive($file['name'])) {

		    		$info = getimagesize($path.$src);
    				if ($info['mime'] == 'image/gif' || $info['mime'] == 'image/png') {
	 	    			// Compress the image
		    			if ($info['mime'] == 'image/gif') $image = imagecreatefromgif($path.$src);
		    			elseif ($info['mime'] == 'image/png') $image = imageCreateFromPng($path.$src);
		
		    			//compress and save file to jpg
		    			imagejpeg($image, $path.$dest, 75);
		    			unlink($path.$src);
		    			rename($path.$dest, $path.$src);
    				}
    			}
    		}
    	}
    }
    
    public function isUsed($object)
    {
    	// Allow or not deleting an instance
    	if (get_class($object) == 'PpitCore\Model\Instance') {
    		if (Generic::getTable()->cardinality('contact_vcard', array('instance_id' => $object->id)) > 0) return true;
    	}
    	// Allow or not deleting an community
    	if (get_class($object) == 'PpitContact\Model\Community') {
    		if (Generic::getTable()->cardinality('contact_vcard', array('community_id' => $object->id)) > 0) return true;
    	}
    	return false;
    }
    
    public function isDeletable()
    {
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitContactDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
    	return true;
    }

    public function delete($update_time)
    {
    	// Check isolation and save
    	$vcard = Vcard::getTable()->get($this->id);
    	if ($vcard->update_time != $update_time) return 'Isolation';
    	Vcard::getTable()->delete($this->id);
    	return 'OK';
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public function getDevisInputFilter()
    {
        throw new \Exception("Not used");
    }    

    public static function getTable()
    {
    	if (!Vcard::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Vcard::$table = $sm->get('PpitContact\Model\VcardTable');
    	}
    	return Vcard::$table;
    }
}