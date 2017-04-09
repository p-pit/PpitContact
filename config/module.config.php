<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'PpitContact\Controller\Contract' => 'PpitContact\Controller\ContractController',
        	'PpitContact\Controller\ContactMessage' => 'PpitContact\Controller\ContactMessageController',
        ),
    ),

	'console' => array(
			'router' => array(
					'routes' => array(
							'send' => array(
									'options' => array(
											'route'    => 'contact-message send',
											'defaults' => array(
													'controller' => 'PpitContact\Controller\ContactMessage',
													'action'     => 'send'
											)
									)
							),
					),
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
        	'contactMessage' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/contact-message',
                    'defaults' => array(
                        'controller' => 'PpitContact\Controller\ContactMessage',
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
        						'search' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/search',
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
		        				'update' => array(
		        						'type' => 'segment',
		        						'options' => array(
		        								'route' => '/update[/:id][/:act]',
		        								'constraints' => array(
		        										'id'     => '[0-9]*',
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

				array('route' => 'contactMessage', 'roles' => array('admin')),
				array('route' => 'contactMessage/index', 'roles' => array('admin')),
				array('route' => 'contactMessage/search', 'roles' => array('admin')),
				array('route' => 'contactMessage/export', 'roles' => array('admin')),
				array('route' => 'contactMessage/list', 'roles' => array('admin')),
				array('route' => 'contactMessage/detail', 'roles' => array('admin')),
				array('route' => 'contactMessage/update', 'roles' => array('admin')),
						
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

	'ppitApplications' => array(
    		'p-pit-contact' => array(
    				'labels' => array('fr_FR' => 'P-Pit Contacts', 'en_US' => 'Contacts by 2Pit'),
    				'route' => 'contactMessage/index',
    				'params' => array(),
					'roles' => array(
					),
			),
	),
		
	'ppitContactDependencies' => array(
	),

	'ppitCoreDependencies' => array(
	),

	'menus' => array(
			'p-pit-contact' => array(
					'contact-message' => array(
							'route' => 'contactMessage/index',
							'params' => array(),
							'glyphicon' => 'glyphicon-envelope',
							'label' => array(
									'en_US' => 'Messages',
									'fr_FR' => 'Messages',
							),
					),
			),
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

	'contactMessage/types' => array(
			'type' => 'select',
			'modalities' => array(
					'email' => array('en_US' => 'Email', 'fr_FR' => 'email'),
			),
			'labels' => array('en_US' => 'Type', 'fr_FR' => 'Type'),
	),
		
	'contactMessage' => array(
			'statuses' => array(),
			'properties' => array(
					'type' => array(
							'type' => 'repository',
							'definition' => 'contactMessage/types',
					),
					'to' => array(
							'type' => 'array',
							'labels' => array(
									'en_US' => 'To',
									'fr_FR' => 'À',
							),
					),
					'cc' => array(
							'type' => 'array',
							'labels' => array(
									'en_US' => 'Cc',
									'fr_FR' => 'Cc',
							),
					),
					'cci' => array(
							'type' => 'array',
							'labels' => array(
									'en_US' => 'Cci',
									'fr_FR' => 'Cci',
							),
					),
					'subject' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'Subject',
									'fr_FR' => 'Objet',
							),
					),
					'from_name' => array(
							'type' => 'input',
							'labels' => array(
									'en_US' => 'From',
									'fr_FR' => 'De',
							),
					),
					'body' => array(
							'type' => 'textarea',
							'labels' => array(
									'en_US' => 'Body',
									'fr_FR' => 'Corps',
							),
					),
					'emission_time' => array(
							'type' => 'time',
							'labels' => array(
									'en_US' => 'Emission',
									'fr_FR' => 'Emission',
							),
					),
			),
	),
	
	'contactMessage/index' => array(
			'title' => array('en_US' => 'P-Pit Contacts', 'fr_FR' => 'P-Pit Contacts'),
	),
	
	'contactMessage/search' => array(
			'title' => array('en_US' => 'Messages', 'fr_FR' => 'Messages'),
			'todoTitle' => array('en_US' => 'current', 'fr_FR' => 'en cours'),
			'searchTitle' => array('en_US' => 'search', 'fr_FR' => 'recherche'),
			'main' => array(
					'type' => 'select',
					'emission_time' => 'range',
					'to' => 'contains',
					'subject' => 'contains',
			),
	),
	
	'contactMessage/list' => array(
			'type' => 'select',
			'emission_time' => 'date',
			'to' => 'array',
			'cci' => 'array',
			'subject' => 'contains',
	),

	'contactMessage/detail' => array(
			'title' => array('en_US' => 'Message detail', 'fr_FR' => 'Détail du message'),
	),
	
	'contactMessage/update' => array(
			'type' => array('mandatory' => true),
			'to' => array('mandatory' => false),
			'cc' => array('mandatory' => false),
			'cci' => array('mandatory' => false),
			'subject' => array('mandatory' => true),
			'from_name' => array('mandatory' => true),
//			'body' => array('mandatory' => false),
	),
		
	'contactMessage/messages' => array(
			'genericSubject' => array(
					'en_US' => 'Message subject',
					'fr_FR' => 'Sujet du message',
			),
			'genericText' => array(
					'en_US' => 'Hello %s,

Message text
					
Best regards,
			
The P-Pit staff
',
					'fr_FR' => 'Bonjour %s,
				
Texte du message

Bien cordialement,
			
L\'équipe P-Pit
',
			),
	),
	'community/sendMessage' => array(
			'to' => 'support@p-pit.fr',
			'from_mail' => 'support@p-pit.fr',
			'from_name' => 'noreply@p-pit.fr',
			'subject' => array('en_US' => 'Important message from P-Pit', 'fr_FR' => 'Message important de P-Pit'),
			'body' => array(
					'en_US' => '<p>Hello,</p>
<p>We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr.</p>
<p>Best regards,</p>
<p>The P-Pit staff</p>
', 
					'fr_FR' => '<p>Bonjour,</p>
<p>Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr.</p>
<p>Bien cordialement,</p>
<p>L\'équipe P-Pit</p>
',
			),
	),
	'community/consumeCredit' => array(
			'messages' => array(
					'availabilityAlertTitle' => array(
							'en_US' => 'P-Pit Communities credits available',
							'fr_FR' => 'Crédits P-Pit Communities disponibles',
					),
					'availabilityAlertText' => array(
							'en_US' => 'Hello %s,
							
Your available P-Pit Communities credits reserve for %s is almost out of stock (*). 
In order to avoid the risk of suffering use restrictions, you can right now renew your subscription, for the desired period of time.
Our tip : Have peace of mind by renewing for a 1-year period of time.
					
(*) Your current P-Pit Communities reserve rises %s units. For the 7 next days, your monthly consumption is estimated up to now to %s units, estimation based on the current active communities.

We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-Pit staff
',
							'fr_FR' => 'Bonjour %s,
							
Votre réserve de crédits P-Pit Communities disponibles pour %s est bientôt épuisée (*). 
Pour ne pas risquer de subir des restrictions à l\'utilisation, vous pouvez dès à présent renouveller en ligne votre souscription pour la durée que vous souhaitez.
Notre conseil : Ayez l\'esprit tranquille en renouvelant pour un an.

(*) Votre réserve actuelle P-Pit Communities est de %s unités. Pour les 7 prochains jours, votre décompte mensuel est estimé à %s unités, estimation basée sur le nombre de communautés actives à ce jour.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-Pit
',
					),
					'suspendedServiceTitle' => array(
							'en_US' => 'P-Pit Communities access suspended',
							'fr_FR' => 'Accès P-Pit Communities suspendus',
					),
					'suspendedServiceText' => array(
							'en_US' => 'Hello %s,
							
Your available P-Pit Communities credits reserve for %s is out of stock (*). 
Please note that the access has been automatically suspended for the communities listed below until a new subscription of credits occurs:
%s
Our tip : Have peace of mind by renewing for a 1-year period of time (12 monthly credits per active community).
					
(*) Your current P-Pit Communities solde rises %s units.

We hope that our services are giving you satisfaction. Please send your requests or questions to the P-Pit support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-Pit staff
',
							'fr_FR' => 'Bonjour %s,
							
Votre réserve de crédits P-Pit Communities pour %s est épuisée (*). 
Veuillez noter que l\'accès a été automatiquement suspendu pour les communautés listées ci-après jusqu\'à la souscription de nouveaux crédits :
%s
Notre conseil : Ayez l\'esprit tranquille en renouvelant pour un an (12 crédits mensuels par communauté active).

(*) Votre solde actuel P-Pit Communities est de %s unités.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-Pit : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-Pit
',
					),
			),
	),
);
