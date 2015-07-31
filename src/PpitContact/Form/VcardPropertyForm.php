<?php
namespace PpitContact\Form;

use Zend\Form\Form;

class VcardPropertyForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('instance');
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
    			'name' => 'vcard_id',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'id' => 'id',
    			),
    	));
    	
    	$this->add(
    			array(
    					'name' => 'order',
    					'type' => 'Select',
    					'attributes' => array(
    							'id'    => 'order'
    					),
    					'options' => array(
    							'value_options' => NULL ,
    							'empty_option'  => '--- Please choose ---'
    					),
    			));
    	
    	$this->add(array(
    			'name' => 'name',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'name',
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
    							'value_options' => NULL ,
    							'empty_option'  => '--- Please choose ---'
    					),
    			));
    	
    	$this->add(array(
    			'name' => 'text_value',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'text_value',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'blob_value',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'id' => 'blob_value',
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
