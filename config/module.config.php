<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitContact\Controller\Community' => 'PpitContact\Controller\CommunityController',
            'PpitContact\Controller\Contract' => 'PpitContact\Controller\ContractController',
        	'PpitContact\Controller\Message' => 'PpitContact\Controller\MessageController',
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
        	'community' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/community',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\Community',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       			'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       			'list' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list',
	                    	'defaults' => array(
	                    		'action' => 'list',
	                        ),
	                    ),
	                ),
	       			'dataList' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/data-list',
	                    	'defaults' => array(
	                    		'action' => 'dataList',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
	                        ),
	                    ),
	                ),
	       		),
        	),
        	'contract' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/community-function',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\Contract',
                        'action'     => 'list',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	       			'add' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/add',
	                    	'defaults' => array(
	                            'action' => 'add',
	                        ),
	                    ),
	                ),
	       			'dataList' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/data-list[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'dataList',
	                        ),
	                    ),
	                ),
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       			'list' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'list',
	                        ),
	                    ),
	                ),
	       		),
        	),
        	'message' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/message',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\Message',
                        'action'     => 'index',
                    ),
                ),
           		'may_terminate' => true,
	       		'child_routes' => array(
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'delete',
	                        ),
	                    ),
	                ),
	       			'index' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/index',
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	       			'simulate' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/simulate[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'simulate',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
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
	                        'route' => '/index[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'index',
	                        ),
	                    ),
	                ),
	                'search' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/search[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'search',
	                        ),
	                    ),
	                ),
	       			'list' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list',
	                    	'defaults' => array(
	                    		'action' => 'list',
	                        ),
	                    ),
	                ),
	       			'export' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/export',
	                    	'defaults' => array(
	                    		'action' => 'export',
	                        ),
	                    ),
	                ),
	       			'detail' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/detail[/:id]',
		                    'constraints' => array(
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'detail',
	                        ),
	                    ),
	                ),
	       			'listRest' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/list-rest[/:community_id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                    		'action' => 'listRest',
	                        ),
	                    ),
	                ),
	       			'update' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/update[/:community_id][/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    	'id' => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'update',
	                        ),
	                    ),
	                ),
	       			'photo' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/photo[/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
		                    	'id'     => '[0-9]*',
		                    ),
	                    	'defaults' => array(
	                            'action' => 'photo',
	                        ),
	                    ),
	                ),
	       			'delete' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/delete[/:id]',
		                    'constraints' => array(
		                    	'community_id' => '[0-9]*',
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
	       		),
	       	),
/*        	'vcardRest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/vcard-rest[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\VcardRest',
                    ),
                ),
            ),*/
        ),
    ),
	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(

				array('route' => 'community', 'roles' => array('admin')),
				array('route' => 'community/dataList', 'roles' => array('admin')),
				array('route' => 'community/delete', 'roles' => array('admin')),
				array('route' => 'community/index', 'roles' => array('admin')),
				array('route' => 'community/list', 'roles' => array('admin')),
				array('route' => 'community/update', 'roles' => array('admin')),
				array('route' => 'contract', 'roles' => array('admin')),
				array('route' => 'contract/add', 'roles' => array('admin')),
				array('route' => 'contract/datalist', 'roles' => array('admin')),
				array('route' => 'contract/delete', 'roles' => array('admin')),
				array('route' => 'contract/list', 'roles' => array('admin')),
				array('route' => 'message', 'roles' => array('admin')),
				array('route' => 'message/delete', 'roles' => array('admin')),
				array('route' => 'message/index', 'roles' => array('admin')),
				array('route' => 'message/simulate', 'roles' => array('admin')),
				array('route' => 'message/update', 'roles' => array('admin')),
				array('route' => 'vcard', 'roles' => array('admin')),
				array('route' => 'vcard/add', 'roles' => array('admin')),
				array('route' => 'vcard/photo', 'roles' => array('user')),
				array('route' => 'vcard/delete', 'roles' => array('admin')),
				array('route' => 'vcard/detail', 'roles' => array('admin')),
				array('route' => 'vcard/export', 'roles' => array('admin')),
				array('route' => 'vcard/index', 'roles' => array('admin')),
				array('route' => 'vcard/list', 'roles' => array('admin')),
				array('route' => 'vcard/listRest', 'roles' => array('admin')),
				array('route' => 'vcard/update', 'roles' => array('user')),
				array('route' => 'vcard/search', 'roles' => array('admin')),
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

	'ppitRoles' => array(
			'ppitCommitment' => array(
					'admin' => array(
							'show' => true,
							'labels' => array(
									'en_US' => 'Admin',
									'fr_FR' => 'Admin',
							),
					),
			),
	),
		
	'ppitContactDependencies' => array(
	),

	'ppitCoreDependencies' => array(
			'contact_vcard' => new \PpitContact\Model\Vcard,
	),

	'vcard/properties' => array(
			'n_title' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Title',
							'fr_FR' => 'Civilité',
					),
			),
			'n_first' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'First name',
							'fr_FR' => 'Prénom',
					),
			),
			'n_last' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Last name',
							'fr_FR' => 'Nom',
					),
			),
			'n_fn' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Name',
							'fr_FR' => 'Nom',
					),
			),
			'tel_work' => array(
					'type' => 'phone',
					'labels' => array(
							'en_US' => 'Phone',
							'fr_FR' => 'Téléphone',
					),
			),
			'tel_cell' => array(
					'type' => 'phone',
					'labels' => array(
							'en_US' => 'Cellular',
							'fr_FR' => 'Mobile',
					),
			),
			'email' => array(
					'type' => 'email',
					'labels' => array(
							'en_US' => 'Email',
							'fr_FR' => 'Email',
					),
			),
			'adr_street' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - street',
							'fr_FR' => 'Adresse - rue',
					),
			),
			'adr_extended' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - extended',
							'fr_FR' => 'Adresse - complément',
					),
			),
			'adr_post_office_box' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - post office box',
							'fr_FR' => 'Adresse - boîte postale',
					),
			),
			'adr_zip' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - ZIP',
							'fr_FR' => 'Adresse - Code postal',
					),
			),
			'adr_city' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - city',
							'fr_FR' => 'Adresse - ville',
					),
			),
			'adr_state' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - state',
							'fr_FR' => 'Adresse - état',
					),
			),
			'adr_country' => array(
					'type' => 'input',
					'maxSize' => 255,
					'labels' => array(
							'en_US' => 'Address - country',
							'fr_FR' => 'Adresse - pays',
					),
			),
			'locale' => array(
					'type' => 'select',
					'modalities' => array(
							'en_US' => array('en_US' => 'en_US', 'fr_FR' => 'en_US'),
							'fr_FR' => array('en_US' => 'fr_FR', 'fr_FR' => 'fr_FR'),
					),
					'labels' => array(
							'en_US' => 'Locale',
							'fr_FR' => 'Traduction',
					),
			),
	),
);
