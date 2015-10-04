<?php
namespace PpitContact;

use PpitContact\Model\ContactEvent;
use PpitContact\Model\Vcard;
use PpitContact\Model\VcardProperty;
use PpitCore\Model\GenericTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module //implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
 	          	'PpitContact\Model\ContactEventTable' =>  function($sm) {
                    $tableGateway = $sm->get('ContactEventTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'ContactEventTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ContactEvent());
                    return new TableGateway('contact_event', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitContact\Model\VcardTable' =>  function($sm) {
                    $tableGateway = $sm->get('VcardTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'VcardTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Vcard());
                    return new TableGateway('contact_vcard', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'PpitContact\Model\VcardPropertyTable' =>  function($sm) {
                    $tableGateway = $sm->get('VcardPropertyTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'VcardPropertyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new VcardProperty());
                    return new TableGateway('contact_vcard_property', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
