<?php
namespace PpitContact\Form;

use Zend\Form\Form;

class ContactEventForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('contactEvent');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }

    public function addElements()
    {
    	$this->add(array(
    			'name' => 'csrf',
    			'type' => 'Csrf',
    			'options' => array(
    					'csrf_options' => array(
    							'timeout' => 600
    					)
    			)
    	));
    	
    	
    	$this->add(array(
    			'name' => 'id',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'id' => 'id',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'contact_id',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'id' => 'customer_id',
    			),
    	));
    	
    	$this->add(
    			array(
    					'name' => 'type',
    					'type' => 'Select',
    					'attributes' => array(
    							'id'    => 'type'
    					),
    					'options' => array(
    							'value_options' => NULLL ,
    							'empty_option'  => '--- Please choose ---'
    					),
    			));
    	
    	$this->add(array(
    			'type' => 'Zend\Form\Element\Date',
    			'name' => 'date',
    			'attributes' => array(
    					'id' => 'order_date',
    					'min' => '2010-01-01',
    					'max' => '2999-01-01',
    					'step' => '1',
    			)
    	));
    	$this->get('date')->setValue(date('Y-m-d'));
    	
    	$this->add(array(
    			'name' => 'caption',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'caption',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'description',
    			'type'  => 'textarea',
    			'attributes' => array(
    					'rows' => 5,
    					'cols' => 100,
    					'id' => 'description',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'comment',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'id' => 'retraction_limit',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'submit',
    			'attributes' => array(
    					'type'  => 'submit',
    					'value' => 'update',
    					'id' => 'submit',
    			),
    	));
    	
    }
}
