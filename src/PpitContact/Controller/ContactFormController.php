<?php
namespace PpitContact\Controller;

use PpitCore\Form\CsrfForm;
use PpitCore\Model\Account;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitContact\Model\ContactMessage;
use PpitContact\ViewHelper\SsmlContactMessageViewHelper;
use PpitContact\Model\smsenvoi;
use PpitContact\Model\UnitaryTarget;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContactFormController extends AbstractActionController
{
	public function indexAction()
	{
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		
		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'active' => 'application',
				'applicationId' => $applicationId,
				'applicationName' => $applicationName,
				'currentEntry' => $currentEntry,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function state1Action()
	{
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		
		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'active' => 'application',
				'applicationId' => $applicationId,
				'applicationName' => $applicationName,
				'currentEntry' => $currentEntry,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function state2Action()
	{
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		
		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'active' => 'application',
				'applicationId' => $applicationId,
				'applicationName' => $applicationName,
				'currentEntry' => $currentEntry,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function state3Action()
	{
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		
		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'active' => 'application',
				'applicationId' => $applicationId,
				'applicationName' => $applicationName,
				'currentEntry' => $currentEntry,
		));
		$view->setTerminal(true);
		return $view;
	}

	public function state4Action()
	{
		$context = Context::getCurrent();
		$place = Place::get($context->getPlaceId());
		
		$applicationId = 'p-pit-contact';
		$applicationName = $context->getConfig('ppitApplications')['p-pit-contact']['labels'][$context->getLocale()];
		$currentEntry = $this->params()->fromQuery('entry', 'contactMessage');
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'place' => $place,
				'active' => 'application',
				'applicationId' => $applicationId,
				'applicationName' => $applicationName,
				'currentEntry' => $currentEntry,
		));
		$view->setTerminal(true);
		return $view;
	}
}
