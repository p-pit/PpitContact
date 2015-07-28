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
	    		'name' => 'n_last',
	    		'attributes' => array(
        				'id' => 'n_last',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => '* Last name',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'n_first',
	    		'attributes' => array(
	    				'id' => 'n_first',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => '* First name',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ORG',
	    		'attributes' => array(
	    				'id' => 'org',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Organization',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'EMAIL',
	    		'attributes' => array(
	    				'id' => 'tel',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => '* Email',
	    		),
	    ));
	     
	    $this->add(array(
	    		'name' => 'TEL_work',
	    		'attributes' => array(
	    				'id' => 'tel',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => '* Work phone',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'TEL_cell',
	    		'attributes' => array(
	    				'id' => 'tel',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Cellular phone',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_street',
	    		'attributes' => array(
	    				'id' => 'adr_street',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - street',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_extended',
	    		'attributes' => array(
	    				'id' => 'adr_extended',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - extended',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_post_office_box',
	    		'attributes' => array(
	    				'id' => 'adr_post_office_box',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - post office box',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_zip',
	    		'attributes' => array(
	    				'id' => 'adr_zip',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - zip',
	    		),
	    ));
	     
	    $this->add(array(
	    		'name' => 'ADR_city',
	    		'attributes' => array(
	    				'id' => 'adr_city',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - city',
	    		),
	    ));

	    $this->add(array(
	    		'name' => 'ADR_country',
	    		'attributes' => array(
	    				'id' => 'adr_country',
	    				'type'  => 'text',
	    				'size'  => '255',
	    		),
	    		'options' => array(
	    				'label' => 'Address - country',
	    		),
	    ));
	     
        $this->add(array(
			'name' => 'submit',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'OK',
				'id' => 'submitbutton',
			),
		));
        
        // Champs cachés
        $this->add(
            array(
                'name' => 'csrf',
                'type' => 'Csrf',
                'options' => array(
                    'csrf_options' => array(
                        'timeout' => 600
                    )
                )
            )
        );
        
    	$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
    }
}
