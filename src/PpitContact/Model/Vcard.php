<?php
namespace PpitContact\Model;

use PpitContact\Model\VcardProperty;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Vcard implements InputFilterAwareInterface
{
    public $id;
	public $instance_id;
	public $customer_id;
	public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $org;
    public $tel_work;
    public $tel_cell;
    public $email;
    
    // Additional fields (from joined table)
    public $address_type;
    public $ADR_street;
    public $ADR_extended;
    public $ADR_post_office_box;
    public $ADR_zip;
    public $ADR_city;
    public $ADR_state;
    public $ADR_country;
    
    protected $inputFilter;
    protected $devisInputFilter;

    // Static fields
    public static $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
    public static $telRegex = "/^\+?([0-9\. ]*)$/";
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->customer_id = (isset($data['customer_id'])) ? $data['customer_id'] : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->org = (isset($data['org'])) ? $data['org'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        
		// Additional fileds
        $this->address_type = (isset($data['address_type'])) ? $data['address_type'] : null;
        $this->ADR_street = (isset($data['ADR_street'])) ? $data['ADR_street'] : null;
        $this->ADR_extended = (isset($data['ADR_extended'])) ? $data['ADR_extended'] : null;
        $this->ADR_post_office_box = (isset($data['ADR_post_office_box'])) ? $data['ADR_post_office_box'] : null;
        $this->ADR_zip = (isset($data['ADR_zip'])) ? $data['ADR_zip'] : null;
        $this->ADR_city = (isset($data['ADR_city'])) ? $data['ADR_city'] : null;
        $this->ADR_state = (isset($data['ADR_state'])) ? $data['ADR_state'] : null;
        $this->ADR_country = (isset($data['ADR_country'])) ? $data['ADR_country'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['customer_id'] = (int) $this->customer_id;
    	$data['n_title'] = $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    	$data['org'] = $this->org;
    	$data['tel_work'] = $this->tel_work;
    	$data['tel_cell'] = $this->tel_cell;
    	$data['email'] = $this->email;
    	 
    	return $data;
    }

    public static function retrieveExisting($n_last, $n_first, $email, $tel_cell, $tel_work, $vcardTable, $currentUser)
    {
    	// Search for an existing contact with same last_name, first_name and either email or cellular
    	$select = $vcardTable->getSelect()->where(array('n_last' => $n_last, 'n_first' => $n_first, 'email' => $email));
    	$cursor = $vcardTable->selectWith($select, $currentUser);
    	if (count($cursor) > 0) return $cursor->current();
    	else {
	    	$select = $vcardTable->getSelect()->where(array('n_last' => $n_last, 'n_first' => $n_first, 'tel_cell' => $tel_cell));
	    	$cursor = $vcardTable->selectWith($select, $currentUser);
	    	if (count($cursor) > 0) return $cursor->current();
    		else {
    			$select = $vcardTable->getSelect()->where(array('n_last' => $n_last, 'n_first' => $n_first, 'tel_work' => $tel_work));
    			$cursor = $vcardTable->selectWith($select, $currentUser);
    			if (count($cursor) > 0) return $cursor->current();
    		}
    	}
    	return null;
    }
    
    public function retrieveProperties($vcardPropertyTable, $currentUser) {
    	$select = $vcardPropertyTable->getSelect()
	    	->where(array('vcard_id' => $this->id));
    	$cursor = $vcardPropertyTable->selectWith($select, $currentUser);
    	foreach ($cursor as $property) {
    		switch ($property->name) {
    			case 'ADR_street':
    				$this->ADR_street = $property->text_value;
    				break;
    			case 'ADR_extended':
    				$this->ADR_extended = $property->text_value;
    				break;
    			case 'ADR_post_office_box':
    				$this->ADR_post_office_box = $property->text_value;
    				break;
    			case 'ADR_zip':
    				$this->ADR_zip = $property->text_value;
    				break;
    			case 'ADR_city':
    				$this->ADR_city = $property->text_value;
    				break;
    			case 'ADR_state':
    				$this->ADR_state = $property->text_value;
    				break;
    			case 'ADR_country':
    				$this->ADR_country = $property->text_value;
    				break;
    		}
    	}
    }

    public function loadData($request) {
    
    	// Retrieve the data from the request
    	$this->n_title =  trim(strip_tags($request->getPost('n_title')));
    	$this->n_last =  trim(strip_tags($request->getPost('n_last')));
    	$this->n_first =  trim(strip_tags($request->getPost('n_first')));
    	$this->email =  trim(strip_tags($request->getPost('email')));
    	$this->tel_work =  trim(strip_tags($request->getPost('tel_work')));
    	$this->tel_cell =  trim(strip_tags($request->getPost('tel_cell')));
    	$this->ADR_street =  trim(strip_tags($request->getPost('ADR_street')));
    	$this->ADR_extended =  trim(strip_tags($request->getPost('ADR_extended')));
    	$this->ADR_post_office_box =  trim(strip_tags($request->getPost('ADR_post_office_box')));
    	$this->ADR_zip =  trim(strip_tags($request->getPost('ADR_zip')));
    	$this->ADR_city =  trim(strip_tags($request->getPost('ADR_city')));
    	$this->ADR_state =  trim(strip_tags($request->getPost('ADR_state')));
    	$this->ADR_country =  trim(strip_tags($request->getPost('ADR_country')));
    
    	// Check integrity
    
    	if (	strlen($this->n_title) > 255
    			||	$this->n_first == '' || strlen($this->n_first) > 255
    			||	$this->n_last == '' || strlen($this->n_last) > 255
    			||	strlen($this->email) > 255
    			|| ($this->email && !preg_match(Vcard::$emailRegex, $this->email))
    			||	strlen($this->tel_work) > 255
    			|| ($this->tel_work && !preg_match(Vcard::$telRegex, $this->tel_work))
    			||	strlen($this->tel_cell) > 255
    			|| ($this->tel_cell && !preg_match(Vcard::$telRegex, $this->tel_cell))
    			|| (!$this->email && (!$this->tel_work && !$this->tel_cell)) // At least an email or a phone
    			||	strlen($this->ADR_street) > 255
    			||	strlen($this->ADR_extended) > 255
    			||	strlen($this->ADR_post_office_box) > 255
    			||	strlen($this->ADR_zip) > 255
    			||	strlen($this->ADR_city) > 255
    			||	strlen($this->ADR_state) > 255
    			||	strlen($this->ADR_country) > 255) {
    
    				throw new \Exception('View error');
    			}
    			 
    			$this->n_fn = $this->n_last.', '.$this->n_first;
    }
    
    public function updateProperties($vcardPropertyTable, $currentUser) {
    	$vcardPropertyTable->multipleDelete(array('vcard_id' => $this->id), $currentUser);
    	$property = new VcardProperty();
    	$property->vcard_id = $this->id;
    	$property->type = $this->address_type;
    	if ($this->ADR_street) {
    		$property->id = 0;
    		$property->order = 1;
    		$property->name = 'ADR_street';
    		$property->text_value = $this->ADR_street;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
        if ($this->ADR_extended) {
    		$property->id = 0;
        	$property->order = 2;
    		$property->name = 'ADR_extended';
    		$property->text_value = $this->ADR_extended;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
        if ($this->ADR_post_office_box) {
    		$property->id = 0;
        	$property->order = 3;
    		$property->name = 'ADR_post_office_box';
    		$property->text_value = $this->ADR_post_office_box;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
        if ($this->ADR_zip) {
    		$property->id = 0;
        	$property->order = 4;
    		$property->name = 'ADR_zip';
    		$property->text_value = $this->ADR_zip;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
        if ($this->ADR_city) {
    		$property->id = 0;
        	$property->order = 5;
    		$property->name = 'ADR_city';
    		$property->text_value = $this->ADR_city;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
        if ($this->ADR_state) {
    		$property->id = 0;
        	$property->order = 6;
    		$property->name = 'ADR_state';
    		$property->text_value = $this->ADR_state;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
    	if ($this->ADR_country) {
    		$property->id = 0;
        	$property->order = 7;
    		$property->name = 'ADR_country';
    		$property->text_value = $this->ADR_country;
    		$vcardPropertyTable->save($property, $currentUser);
    	}
    }
    
    public function checkIntegrity() {
    
    	$this->n_title = trim(strip_tags($this->n_title));
    	$this->n_first = trim(strip_tags($this->n_first));
    	$this->n_last = trim(strip_tags($this->n_last));
    	$this->email = trim(strip_tags($this->email));
    	$this->tel_work = trim(strip_tags($this->tel_work));
    	$this->tel_cell = trim(strip_tags($this->tel_cell));
    	 
    	if (strlen($vcard->n_title) > 255 ||
    		!$vcard->n_first || strlen($vcard->n_first) > 255 ||
			!$vcard->n_last || strlen($vcard->n_last) > 255 ||
			!$vcard->email || strlen($vcard->email) > 255 ||
    		!preg_match(Vcard::$emailRegex, $vcard->email) ||
			strlen($vcard->tel_work) > 255 ||
    		!preg_match(Vcard::$telRegex, $vcard->tel_work) ||
			strlen($vcard->tel_cell) > 255 ||
    		!preg_match(Vcard::$telRegex, $vcard->tel_cell) ||
    		(!$vcard->tel_cell && ! $vcard->tel_work)) {
    		
    		throw new \Exception('javascript error');
    	}
    }
    
    public static function visibleContactList($cursor, $customer_id, $currentUser) {
    
    	// Execute the request
    	$contacts = array();
    	// Only the users belonging to one's instance, except superadmin which see everyone
    	foreach ($cursor as $contact) {
    
    		// Super admin
    		if ($currentUser->role_id == 'super_admin') $contacts[$contact->id] = $contact;
    
/*    		// Admin
    		elseif ($currentUser->role_id == 'admin' &&
    				$currentUser->instance_id == $contact->instance_id) {
    				
    			$contacts[$contact->id] = $contact;
    		}
    
    		// Customer admin*/
    		elseif (/*$currentUser->role_id == 'customer_admin' &&*/
    				$currentUser->instance_id == $contact->instance_id &&
    				$customer_id == $contact->customer_id) {

    			$contacts[$contact->id] = $contact;
    		}
    	}
    	return $contacts;
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

    public function getDevisInputFilter()
    {
        if (!$this->devisInputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'csrf',
            		'required' => false,
            )));

	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'n_last',
	        		'required' => TRUE,
	        		'filters'  => array(
	        				array('name' => 'StripTags'),
	        				array('name' => 'StringTrim'),
	        		),
	        		'validators' => array(
	        				array(
	        						'name'    => 'StringLength',
	        						'options' => array(
	        								'encoding' => 'UTF-8',
	        								'min'      => 1,
	        								'max'      => 255,
	        						),
	        				),
	        		),
	        )));
	
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'n_first',
	        		'required' => TRUE,
	        		'filters'  => array(
	        				array('name' => 'StripTags'),
	        				array('name' => 'StringTrim'),
	        		),
	        		'validators' => array(
	        				array(
	        						'name'    => 'StringLength',
	        						'options' => array(
	        								'encoding' => 'UTF-8',
	        								'min'      => 1,
	        								'max'      => 255,
	        						),
	        				),
	        		),
	        )));
	
	      
	    
	        
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'tel_work',
	        		'required' => TRUE,
	        		'filters'  => array(
	        				array('name' => 'StripTags'),
	        				array('name' => 'StringTrim'),
	        		),
	        		'validators' => array(
	        				array(
	        						'name'    => 'StringLength',
	        						'options' => array(
	        								'encoding' => 'UTF-8',
	        								'min'      => 1,
	        								'max'      => 255,
	        						),
	        				),
	        		),
	        )));
	
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'tel_cell',
	        		'required' => FALSE,
	        		'filters'  => array(
	        				array('name' => 'StripTags'),
	        				array('name' => 'StringTrim'),
	        		),
	        		'validators' => array(
	        				array(
	        						'name'    => 'StringLength',
	        						'options' => array(
	        								'encoding' => 'UTF-8',
	        								'min'      => 1,
	        								'max'      => 255,
	        						),
	        				),
	        		),
	        )));

	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'org',
	        		'required' => FALSE,
	        		'filters'  => array(
	        				array('name' => 'StripTags'),
	        				array('name' => 'StringTrim'),
	        		),
	        		'validators' => array(
	        				array(
	        						'name'    => 'StringLength',
	        						'options' => array(
	        								'encoding' => 'UTF-8',
	        								'min'      => 1,
	        								'max'      => 255,
	        						),
	        				),
	        		),
	        )));

        	$this->devisInputFilter = $inputFilter;
        }
        
        return $this->devisInputFilter;
    }    
}