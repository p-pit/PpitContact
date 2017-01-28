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
	       			'demoMode' => array(
	                    'type' => 'segment',
	                    'options' => array(
	                        'route' => '/demo-mode',
	                    	'defaults' => array(
	                            'action' => 'demoMode',
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
				array('route' => 'vcard/demoMode', 'roles' => array('user')),
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
			'PpitContact' => array(
			),
	),
		
	'ppitContactDependencies' => array(
	),

	'ppitCoreDependencies' => array(
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
/*					'consumeCreditTitle' => array(
							'en_US' => 'Monthly P-PIT Communities credits consumption report',
							'fr_FR' => 'Rapport mensuel de consommation de crédits P-PIT Communities',
					),
					'consumeCreditText' => array(
							'en_US' => 'Hello %s,
							
Please note that the monthly count of P-PIT Communities credits has occurred on %s. Given the current %s active subscriptions, %s units have been consumed. Your new P-PIT Communities reserve rises %s units.

We hope that our services are giving you satisfaction. Please send your requests or questions to the P-PIT support: support@p-pit.fr or 06 29 87 90 02.
					
Best regards,

The P-PIT staff
',
							'fr_FR' => 'Bonjour %s,
							
Veuillez noter que le décompte mensuel de crédits P-PIT Communities a été effectué en date du %s. Compte tenu du nombre de dossiers %s actifs à ce jour, %s unités ont été décomptées. Votre nouvelle réserve P-PIT Communities est de %s unités.

Nous espérons que nos services vous donnent entière satisfaction. Veuillez adresser toute requête ou question au support P-PIT : support@p-pit.fr ou 06 29 87 90 02.
					
Bien cordialement,

L\'équipe P-PIT
',
					),*/
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
/*					'suspendedServiceMessage' => array(
							'en_US' => 'Your available P-PIT Communities credits reserve is out of stock.<br>
Please note that in the case where the credit reserve decreases under zero, the service is automatically suspended until a regularization occurs.<br>
You can add credits at <a href="%s">this place</a>',

							'fr_FR' => 'Votre réserve de crédits <em>P-Pit Communities</em> est épuisée.<br> 
Veuillez noter que dans le cas où la réserve de crédits devient négative, le service est automatiquement suspendu jusqu\'à régularisation.<br>
Vous pouvez ajouter des crédits depuis <a href="%s">cet emplacement</a>',
					),*/
			),
	),
);
