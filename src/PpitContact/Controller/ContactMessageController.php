<?php
namespace PpitContact\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitContact\Model\ContactMessage;
use PpitContact\ViewHelper\SsmlContactMessageViewHelper;
use PpitContact\Model\smsenvoi;
use PpitContact\Model\UnitaryTarget;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContactMessageController extends AbstractActionController
{
   public function indexAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::getTable()->transGet($context->getPlaceId());

		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'active' => 'application',
    			'applicationId' => $applicationId,
    			'applicationName' => $applicationName,
    			'currentEntry' => $currentEntry,
    	));
    }

    public function getFilters($params)
    {
		$context = Context::getCurrent();
    	
    	// Retrieve the query parameters
    	$filters = array();

    	foreach ($context->getConfig('contactMessage/search')['main'] as $propertyId => $rendering) {
    
    		$property = ($params()->fromQuery($propertyId, null));
    		if ($property) $filters[$propertyId] = $property;
    		$min_property = ($params()->fromQuery('min_'.$propertyId, null));
    		if ($min_property) $filters['min_'.$propertyId] = $min_property;
    		$max_property = ($params()->fromQuery('max_'.$propertyId, null));
    		if ($max_property) $filters['max_'.$propertyId] = $max_property;
    	}
    	 
    	return $filters;
    }

    public function searchAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	 
    	$params = $this->getFilters($this->params());
    	$major = ($this->params()->fromQuery('major', 'emission_time'));
    	$dir = ($this->params()->fromQuery('dir', 'DESC'));

    	if (count($params) == 0) $mode = 'todo'; else $mode = 'search';

    	// Retrieve the list
    	$contactMessages = ContactMessage::getList('email', $params, $major, $dir, $mode);

    	// Return the link list
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'contactMessages' => $contactMessages,
    			'mode' => $mode,
    			'params' => $params,
    			'major' => $major,
    			'dir' => $dir,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function listAction()
    {
    	return $this->getList();
    }
    
    public function exportAction()
    {
    	$view = $this->getList();

   		include 'public/PHPExcel_1/Classes/PHPExcel.php';
   		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';

		$workbook = new \PHPExcel;
		(new SsmlContactMessageViewHelper)->formatXls($workbook, $view);		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);

		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Messages.xlsx ');
		$writer->save('php://output');

		return $this->response;
/*    	$view = new ViewModel(array());
    	$view->setTerminal(true);
    	return $view;*/
    }

    public function detailAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $contactMessage = ContactMessage::get($id);
    	else $contactMessage = ContactMessage::instanciate();
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'contactMessage' => $contactMessage,
    	));
    	$view->setTerminal(true);
    	return $view;
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $contactMessage = ContactMessage::get($id);
    	else $contactMessage = ContactMessage::instanciate();
    	$action = $this->params()->fromRoute('act', null);
    
    	// Instanciate the csrf form
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$error = null;
    	if ($action == 'delete') $message = 'confirm-delete';
    	elseif ($action) $message =  'confirm-update';
    	else $message = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		 
    		if ($csrfForm->isValid()) { // CSRF check
    
    			// Load the input data
    			$data = array();
    			foreach($context->getConfig('contactMessage/update') as $propertyId => $unused) {
    				$data[$propertyId] = $request->getPost(($propertyId));
    			}
    			if ($contactMessage->loadData($data) != 'OK') throw new \Exception('View error');
    
    			// Atomically save
    			$connection = ContactMessage::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
    				if (!$contactMessage->id) $rc = $contactMessage->add();
    				elseif ($action == 'delete') $rc = $contactMessage->delete($request->getPost('contact-message_update_time'));
    				else $rc = $contactMessage->update($request->getPost('contact-message_update_time'));
    				if ($rc != 'OK') $error = $rc;
    				if ($error) $connection->rollback();
    				else {
    					$connection->commit();
    					$message = 'OK';
    				}
    			}
    			catch (\Exception $e) {
    				$connection->rollback();
    				throw $e;
    			}
    			$action = null;
    		}
    	}
    	$contactMessage->properties = $contactMessage->getProperties();
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'id' => $id,
    			'action' => $action,
    			'contactMessage' => $contactMessage,
    			'csrfForm' => $csrfForm,
    			'error' => $error,
    			'message' => $message
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function sendAction()
    {
    	$context = Context::getCurrent();
    	$select = ContactMessage::getTable()->getSelect()->where(array('type' => 'email', 'emission_time' => null));
    	$cursor = ContactMessage::getTable()->transSelectWith($select);
    	foreach ($cursor as $email) $email->sendHtmlMail();
    }
}
