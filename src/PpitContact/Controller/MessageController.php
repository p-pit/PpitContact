<?php
namespace PpitContact\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitContact\Model\ContactMessage;
use PpitContact\Model\smsenvoi;
use PpitContact\Model\UnitaryTarget;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MessageController extends AbstractActionController
{
	public function getTargetExemplary($message)
	{ 
		return new UnitaryTarget($message); 
	}
	
	public function indexAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the messages
		$major = $this->params()->fromQuery('major', NULL);
		if (!$major) $major = 'id';
		$dir = $this->params()->fromQuery('dir', NULL);
		if (!$dir) $dir = 'DESC';
		$select = ContactMessage::getTable()->getSelect()
			->order(array($major.' '.$dir, 'id DESC'));
		$cursor = ContactMessage::getTable()->selectWith($select);
		$messages = array();
		foreach ($cursor as $message) $messages[] = $message;
	
		// Return the message list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'major' => $major,
				'dir' => $dir,
				'messages' => $messages,
		));
//   		$view->setTerminal(true);
   		return $view;
	}

	public function simulateAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
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
				'context' => $context,
				'config' => $context->getconfig(),
				'contacts' => $contacts,
		));
   		$view->setTerminal(true);
   		return $view;
	}

	public function updateAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the message and the target
		$id = (int) $this->params()->fromRoute('id', 0);
		if ($id) $modelMessage = ContactMessage::getTable()->get($id);
		else {
			$modelMessage = new ContactMessage;
			$modelMessage->type = 'SMS';
		}
		$target = $this->getTargetExemplary($modelMessage);
		
		// Retrieve credits
		if ($modelMessage->type == 'SMS') {
			$sms = new smsenvoi();
			$modelMessage->credits = $sms->checkCredits()['sms']['PREMIUM'];
//			$modelMessage->credits = 5;
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
						$modelMessage->from = $context->getConfig()['ppitCoreSettings']['mailAdmin'];
						
						// Save
						
						if ($request->getPost('action') != 'transmit') {
							ContactMessage::getTable()->save($modelMessage);
							$message = 'OK';
						}						
						// Transmit the message
						else {
							// Check the credit
							if ($modelMessage->credits < count($modelMessage->to)) $error = 'Insufficient';
							else {
								$tos = $target->compute();
								$sms = new smsenvoi();
								$modelMessage->rejected = array();
								$modelMessage->volume = 0;
								foreach ($tos as $to) {
									$return = ($context->getConfig()['ppitContactSettings']['isSmsActive']) ? $sms->sendSMS($to, $modelMessage->subject, 'PREMIUM', $context->getConfig()['ppitCoreSettings']['nameAdmin']) : 1;
									if (!$return) $modelMessage->rejected[] = $to;
									else {
										$modelMessage->accepted[] = $to;
								
										// Write to the log
										if ($context->getConfig()['ppitCoreSettings']['isTraceActive']) {
											$writer = new Writer\Stream('data/log/mailing.txt');
											$logger = new Logger();
											$logger->addWriter($writer);
											if ($context->getConfig()['ppitContactSettings']['isSmsActive']) {
												if ($return) $result = 'OK'; else $result = 'KO';
											}
											else $result = 'SMS function not enabled => NOT SENT';
											$logger->info($result.' - '.$modelMessage->type.' to: '.$to.' - subject: '.$modelMessage->subject.' - body: '.$modelMessage->body);
										}
									}
								}
								// Save the emission_time and cost
								$modelMessage->emission_time = date("Y-m-d H:i:s");
								$modelMessage->volume = count($modelMessage->accepted);
								$modelMessage->cost = $modelMessage->volume * $context->getConfig()['ppitContactSettings']['smsCost'];
								ContactMessage::getTable()->save($modelMessage);
							
								$message = 'OK';
							}
						}
					}
				}
			}
		}
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'modelMessage' => $modelMessage,
				'target' => $target,
		));
   		$view->setTerminal(true);
   		return $view;
	}

	public function deleteAction()
	{
		// Check the presence of the id parameter for the entity to delete
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');
	
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the message
		$modelMessage = ContactMessage::getTable()->get($id);
		$target = $this->getTargetExemplary($modelMessage);

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
						ContactMessage::getTable()->delete($id);
					}
					$message = 'OK';
				}
			}
		}
	
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'modelMessage' => $modelMessage,
				'target' => $target,
		));
   		$view->setTerminal(true);
   		return $view;
	}
}
