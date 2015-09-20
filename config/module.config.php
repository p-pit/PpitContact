<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitContact\Controller\Vcard' => 'PpitContact\Controller\VcardController',
        ),
    ),
 
    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\Vcard',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       		),
            ),
   	       	'vcard' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/vcard',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\Vcard',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	                'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index[/:customer_id]',
		                    'constraints' => array(
		                    	'customer_id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:customer_id][/:id]',
		                    'constraints' => array(
		                    	'customer_id'     => '[0-9]*',
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
	                        ),
	                    ),
	                ),
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       			'import' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/import[/:id]',
		                    'constraints' => array(
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'import',
	                        ),
	                    ),
	                ),
	                'devis' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/devis[/:id]',
		                    'constraints' => array(
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'devis',
	                        ),
	                    ),
	                ),
	       		),
	       	),
        ),
    ),
	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(

				// Contacts
				array('route' => 'vcard', 'roles' => array('super_admin', 'customer_admin')),
				array('route' => 'vcard/add', 'roles' => array('customer_admin')),
				array('route' => 'vcard/delete', 'roles' => array('customer_admin')),
				array('route' => 'vcard/devis', 'roles' => array('guest')),
				array('route' => 'vcard/import', 'roles' => array('customer_admin')),
				array('route' => 'vcard/index', 'roles' => array('super_admin', 'customer_admin')),
				array('route' => 'vcard/update', 'roles' => array('user')),
			)
		)
	),
		
    'view_manager' => array(
    	'strategies' => array(
    			'ViewJsonStrategy',
    	),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'ppit-contact' => __DIR__ . '/../view',
        ),
    ),
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-contact'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),
);
