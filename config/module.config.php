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
			'admin' => array(
					'show' => true,
					'labels' => array(
							'en_US' => 'Admin',
							'fr_FR' => 'Admin',
					),
			),
	),
		
	'ppitContactDependencies' => array(
	),

	'ppitCoreDependencies' => array(
			'contact_vcard' => new \PpitContact\Model\Vcard,
	),
);
