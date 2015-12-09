<?php
namespace PpitContact\Controller;

use PpitCore\Controller\Functions;
use PpitCore\Controller\PpitController;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Csrf;
use PpitContact\Model\ContactMessage;
use PpitContact\Model\smsenvoi;
use PpitContact\Model\UnitaryTarget;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\View\Model\ViewModel;

class MessageController extends PpitController
{
	protected $creditTable;
	protected $contactMessageTable;
	
	public function getTargetExemplary($message, $controller) { 

		// Retrieve the context
		$settings = $this->getServiceLocator()->get('config');
		$currentUser = Functions::getUser($this);
		return new UnitaryTarget($message, null, $currentUser, $controller); 
	}
	
	public function indexAction()
	{
		// Retrieve the context
		$settings = $this->getServiceLocator()->get('config');
		$currentUser = Functions::getUser($this);
		$currentUser->retrieveHabilitations($this);

		// Retrieve the messages
		$major = $this->params()->fromQuery('major', NULL);
		if (!$major) $major = 'id';
		$dir = $this->params()->fromQuery('dir', NULL);
		if (!$dir) $dir = 'DESC';
		$select = $this->getContactMessageTable()->getSelect()
			->order(array($major.' '.$dir, 'id DESC'));
		$cursor = $this->getContactMessageTable()->selectWith($select, $currentUser);
		$messages = array();
		foreach ($cursor as $message) $messages[] = $message;
	
		// Return the message list
		return new ViewModel(array(
				'currentUser' => $currentUser,
				'settings' => $settings,
				'major' => $major,
				'dir' => $dir,
				'messages' => $messages,
		));
	}

	public function simulateAction()
	{
		// Retrieve the context
		$settings = $this->getServiceLocator()->get('config');
		$currentUser = Functions::getUser($this);
		$currentUser->retrieveHabilitations($this);
	
		// Retrieve the message and the target
		$modelMessage = new ContactMessage;
		$modelMessage->type = 'SMS';
		$target = $this->getTargetExemplary($modelMessage, $this);
		$return = $target->loadDataFromRequest($this->getRequest());
		if ($return != 200) return $this->redirect()->toRoute('index');
		$target->compute();
		$contacts = $target->result;

		// Return the message list
		$view = new ViewModel(array(
				'settings' => $settings,
				'currentUser' => $currentUser,
				'contacts' => $contacts,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function updateAction()
	{
		// Retrieve the context
		$settings = $this->getServiceLocator()->get('config');
		$currentUser = Functions::getUser($this);
		$currentUser->retrieveHabilitations($this);

		// Retrieve the message and the target
		$id = (int) $this->params()->fromRoute('id', 0);
		if ($id) $modelMessage = $this->getContactMessageTable()->get($id, $currentUser);
		else {
			$modelMessage = new ContactMessage;
			$modelMessage->type = 'SMS';
		}
		$target = $this->getTargetExemplary($modelMessage, $this);
		
		// Retrieve credits
		if ($modelMessage->type == 'SMS') {
			$sms = new smsenvoi();
			$modelMessage->credits = $sms->checkCredits();
		}
		
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				
				// Isolation check
				if ($request->getPost('db_update_time') != $modelMessage->update_time) $error = 'Isolation';
				else {

					$return = $modelMessage->loadDataFromRequest($request, $modelMessage->type, $target);
					if ($return != 200) $error = 'Invalid';
					else {
						
						// From
						$modelMessage->from = $settings['ppitCoreSettings']['mailAdmin'];
						
						// Save
						
						if ($request->getPost('action') != 'transmit') {
							$this->getContactMessageTable()->save($modelMessage, $currentUser);
							$message = 'OK';
						}						
						// Transmit the message
						else {								
							// Check the credit
							if ($modelMessage->credits['sms']['LOWCOST'] < count($modelMessage->to)) $error = 'Insufficient';
							else {
								$tos = $target->compute();
								$sms = new smsenvoi();
								$modelMessage->rejected = array();
								$modelMessage->volume = 0;
								foreach ($tos as $to) {
									$return=1; //$return = $sms->sendSMS($to, $modelMessage->subject, 'LOWCOST');
									if (!$return) $modelMessage->rejected[] = $to;
									else {
										$modelMessage->accepted[] = $to;
								
										// Write to the log
										if ($settings['ppitCoreSettings']['isTraceActive']) {
											$writer = new Writer\Stream('data/log/mailing.txt');
											$logger = new Logger();
											$logger->addWriter($writer);
											if ($return) $result = 'OK'; else $result = 'KO';
											$logger->info($result.' - '.$modelMessage->type.' to: '.$to.' - subject: '.$modelMessage->subject.' - body: '.$modelMessage->body);
										}
									}
								}
								// Save the emission_time and cost
								$modelMessage->emission_time = date("Y-m-d H:i:s");
								$modelMessage->volume = count($modelMessage->accepted);
								$modelMessage->cost = $modelMessage->volume * $settings['ppitContactSettings']['smsCost'];
								$this->getContactMessageTable()->save($modelMessage, $currentUser);
							
								$message = 'OK';
							}
						}
					}
				}
			}
		}
		return array(
				'currentUser' => $currentUser,
				'settings' => $settings,
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'modelMessage' => $modelMessage,
				'target' => $target,
		);
	}

	public function deleteAction()
	{
		// Check the presence of the id parameter for the entity to delete
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');
	
		// Retrieve the settings
		$settings = $this->getServiceLocator()->get('config');
		 
		// Retrieve the current user
		$currentUser = Functions::getUser($this);
		$currentUser->retrieveHabilitations($this);
	
		// Retrieve the message
		$modelMessage = $this->getContactMessageTable()->get($id, $currentUser);
		$target = $this->getTargetExemplary($modelMessage, $this);

		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				if ($modelMessage->update_time != $request->getPost('db_update_time')) $error = 'Isolation';
				else {
	
					if ($modelMessage) { // In case the link has already been deleted in the meantime
						$this->getContactMessageTable()->delete($id, $currentUser);
					}
					$message = 'OK';
				}
			}
		}
	
		return array(
				'currentUser' => $currentUser,
				'settings' => $settings,
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'modelMessage' => $modelMessage,
				'target' => $target,
		);
	}

	public function getCreditTable()
	{
		if (!$this->creditTable) {
			$sm = $this->getServiceLocator();
			$this->creditTable = $sm->get('PpitContact\Model\CreditTable');
		}
		return $this->creditTable;
	}
	
    public function getContactMessageTable()
    {
    	if (!$this->contactMessageTable) {
    		$sm = $this->getServiceLocator();
    		$this->contactMessageTable = $sm->get('PpitContact\Model\ContactMessageTable');
    	}
    	return $this->contactMessageTable;
    }
}
