<?php
namespace PpitContact;

//use PpitContact\Model\Community;
use PpitContact\Model\Contract;
use PpitContact\Model\ContactEvent;
use PpitContact\Model\Credits;
use PpitContact\Model\ContactMessage;
//use PpitContact\Model\Vcard;
//use PpitContact\Model\VcardProperty;
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
 	          	'PpitContact\Model\ContractTable' =>  function($sm) {
                    $tableGateway = $sm->get('ContractTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'ContractTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Contract());
                    return new TableGateway('contact_contract', $dbAdapter, null, $resultSetPrototype);
                },
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
 	          	'PpitContact\Model\ContactMessageTable' =>  function($sm) {
                    $tableGateway = $sm->get('ContactMessageTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'ContactMessageTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ContactMessage());
                    return new TableGateway('contact_message', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
