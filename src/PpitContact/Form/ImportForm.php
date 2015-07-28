<?php
namespace PpitCore\Form;

use Zend\Form\Form;

class ImportForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('import');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        
        $this->add(
        		array(
        				'name' => 'name',
        				'type' => 'File',
        				'attributes' => array(
        						'id'    => 'name'
        				),
        				'options' => array(
        						'label' => 'Upload'
        				)
        		)
        );
                
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
        
    	$this->add(array(
            'name' => 'owner_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

    	$this->add(array(
    			'name' => 'parent_id',
    			'attributes' => array(
    					'type'  => 'hidden',
    			),
    	));

    	$this->add(array(
    			'name' => 'uploaded_time',
    			'attributes' => array(
    					'type'  => 'hidden',
    			),
    	));
    	 
    	$this->add(array(
            'name' => 'object_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
    }
}
