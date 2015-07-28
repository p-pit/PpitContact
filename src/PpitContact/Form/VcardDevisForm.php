<?php
namespace PpitContact\Form;

use Zend\Form\Form;

class VcardDevisForm extends Form
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
	    				'label' => '* Organization',
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
	    		'name' => 'souscrire',
	    		'type' => 'Checkbox',
	    		'attributes' => array(
	    				'id'    => 'souscrire'
	    		),
	    		'options' => array(
	    				'label' => 'Souscrire location gratuite 1 an - 50 utilisateurs',
	    				'use_hidden_element' => true,
	    				'checked_value' => '1',
	    				'unchecked_value' => '0'
	    		)
	    ));
	    $this->get('souscrire')->setValue('1');

	    $this->add(array(
	    		'name' => 'quotation',
	    		'type' => 'Checkbox',
	    		'attributes' => array(
	    				'id'    => 'subscribe'
	    		),
	    		'options' => array(
	    				'label' => 'Demander un devis location ou achat premium',
	    				'use_hidden_element' => true,
	    				'checked_value' => '1',
	    				'unchecked_value' => '0'
	    		)
	    ));

	    $this->add(array(
	    		'name' => 'comment',
	    		'type'  => 'textarea',
	    		'attributes' => array(
	    				'rows' => 5,
	    				'cols' => 100,
	    		),
	    		'options' => array(
	    				'label' => 'Votre question',
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
    }
}
