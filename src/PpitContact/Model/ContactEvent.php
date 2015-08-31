<?php
namespace PpitContact\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class ContactEvent implements InputFilterAwareInterface
{
    public $id;
    public $contact_id;
    public $type;
    public $date;
    public $caption;
    public $description;
    public $comment;
    
    protected $inputFilter;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['contact_id'] = (int) $this->contact_id;
    	$data['type'] = $this->type;
    	$data['date'] = $this->date;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['comment'] = $this->comment;
    	
    	return $data;
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->contact_id = (isset($data['contact_id'])) ? $data['contact_id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
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
	        		'name'     => 'caption',
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
	        		'name'     => 'description',
	        		'required' => false,
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
	        								'max'      => 2047,
	        						),
	        				),
	        		),
	        )));
	        
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'comment',
	        		'required' => false,
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
	        								'max'      => 2047,
	        						),
	        				),
	        		),
	        )));
	        
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'type',
	        		'required' => false,
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

        	$this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}