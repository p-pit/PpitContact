<?php
namespace PpitContact\Form;

use Zend\Form\Form;

class VcardForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('instance');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }

    public function addElements($translator)
    {
    	
    	$this->add(
    			array(
    					'name' => 'csrf',
    					'type' => 'Csrf',
    					'options' => array(
    							'csrf_options' => array(
    									'timeout' => 600
    							))
    				));
    	
    	$this->add(array(
    			'name' => 'id',
    			'attributes' => array(
    					'type'  => 'hidden',
    			),
    	));
    	
    	$this->add(array(
        		'name' => 'n_title',
        		'type'  => 'Select',
        		'attributes' => array(
        				'id'    => 'n_title'
        		),
        		'options' => array(
        				'label' => '* Title',
        				'value_options' => array($translator->translate('Mr') => 'Mr', $translator->translate('Ms') => 'Ms'),
        				'empty_option'  => '--- Please choose ---'
        		),
        ));
        
    	$this->add(array(
    			'name' => 'n_first',
    			'attributes' => array(
    					'id' => 'n_first',
    					'type'  => 'text',
    					'size'  => '255',
    			),
    	));

	    $this->add(array(
	    		'name' => 'n_last',
	    		'attributes' => array(
        				'id' => 'n_last',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'org',
	    		'attributes' => array(
	    				'id' => 'org',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'tel_work',
	    		'attributes' => array(
	    				'id' => 'tel_work',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'tel_cell',
	    		'attributes' => array(
	    				'id' => 'tel_cell',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'email',
	    		'attributes' => array(
	    				'id' => 'email',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_street',
	    		'attributes' => array(
	    				'id' => 'ADR_street',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_extended',
	    		'attributes' => array(
	    				'id' => 'ADR_extended',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_post_office_box',
	    		'attributes' => array(
	    				'id' => 'ADR_post_office_box',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_zip',
	    		'attributes' => array(
	    				'id' => 'ADR_zip',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_city',
	    		'attributes' => array(
	    				'id' => 'ADR_city',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_country',
	    		'attributes' => array(
	    				'id' => 'ADR_country',
	    				'type'  => 'text',
	    				'size'  => '255',
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
