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

    // Additional fields (from joined table)
    public $text_value;
    
    protected $inputFilter;
    protected $devisInputFilter;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['n_title'] = (int) $this->n_title;
    	$data['n_first'] = $this->n_first;
    	$data['n_last'] = $this->n_last;
    	$data['n_fn'] = $this->n_fn;
    
    	return $data;
    }
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->n_title = (isset($data['n_title'])) ? $data['n_title'] : null;
        $this->n_first = (isset($data['n_first'])) ? $data['n_first'] : null;
        $this->n_last = (isset($data['n_last'])) ? $data['n_last'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        
        $this->text_value = (isset($data['text_value'])) ? $data['text_value'] : null;
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
	
	        $inputFilter->add($factory->createInput(array(
	        		'name'     => 'ORG',
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
	        		'name'     => 'EMAIL',
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
	        		'name'     => 'TEL_work',
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
	        		'name'     => 'TEL_cell',
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
	        		'name'     => 'ORG',
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
	        		'name'     => 'ADR_street',
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
	        		'name'     => 'ADR_extended',
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
	        		'name'     => 'ADR_post_office_box',
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
	        		'name'     => 'ADR_zip',
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
	        		'name'     => 'ADR_city',
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
	        		'name'     => 'ADR_country',
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
	        		'name'     => 'ORG',
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
	        		'name'     => 'EMAIL',
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
	        		'name'     => 'TEL_work',
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
	        		'name'     => 'TEL_cell',
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
	        		'name'     => 'ORG',
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