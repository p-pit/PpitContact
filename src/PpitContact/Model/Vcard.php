<?php
namespace PpitContact\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Vcard implements InputFilterAwareInterface
{
    public $id;
	public $instance_id;
    public $n_title;
    public $n_first;
    public $n_last;
    public $n_fn;
    public $org;
    public $tel_work;
    public $tel_cell;
    public $email;
    
    // Additional fields (from joined table)
    public $text_value;
    public $ADR_street;
    public $ADR_extended;
    public $ADR_post_office_box;
    public $ADR_zip;
    public $ADR_city;
    public $ADR_state;
    public $ADR_country;
    
    protected $inputFilter;
    protected $devisInputFilter;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->org = (isset($data['org'])) ? $data['org'] : null;
        $this->tel_work = (isset($data['tel_work'])) ? $data['tel_work'] : null;
        $this->tel_cell = (isset($data['tel_cell'])) ? $data['tel_cell'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        
		// Additional fileds
        $this->text_value = (isset($data['text_value'])) ? $data['text_value'] : null;
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
    
    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
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
	
/*	    
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'n_fn',
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
	        )));*/
	     
	    
	
	     

	      
	    

        	$this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
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