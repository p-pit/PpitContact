<?php

namespace PpitContact\Controller;

use PpitCore\Model\Community;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CommunityController extends AbstractActionController
{
    public function indexAction()
    {    	
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$view = new ViewModel(array(
    			'context' => $context,
				'config' => $context->getconfig(),
    	));
   		$view->setTerminal(true);
   		return $view;
    }

    public function listAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Filter
		$filter = array();
    	$name = $this->params()->fromQuery('name', NULL);
    	if ($name) $filter['name'] = $name;

		// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    
		$communities = Community::getList($major, $dir, $filter);
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'name' => $name,
    			'major' => $major,
    			'dir' => $dir,
    			'communities' => $communities,
    	));
    	$view->setTerminal(true);
    	return $view;
    }

    public function dataListAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
    	// Filter
    	$filter = array();
    	$identifier = $this->params()->fromQuery('name', NULL);
    	if ($identifier) $filter['name'] = $identifier;
    
    	// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    
		$communities = Community::getList($major, $dir, $filter);
       	$data = array();
    	foreach ($communities as $community) {
    		$data[] = array(
    				'id' => $community->id,
    				'name' => $community->name,
    		);
    	}
    	return new JsonModel(array('data' => $data));
    }
    
    public function updateAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) $community = Community::get($id);
    	else $community = Community::instanciate();
    	
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
    			$community->loadDataFromRequest($request);

    			// Atomicity
    			$connection = Community::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Add or update
    				if (!$id) $return = $community->add();
    				else $return = $community->update($request->getPost('update_time'));
   					if ($return != 'OK') {
	    				$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
					}
    			}
           	    catch (\Exception $e) {
	    			$connection->rollback();
	    			throw $e;
	    		}
    		}
    	}
    	$view = new ViewModel(array(
    			'context' => $context,
				'config' => $context->getconfig(),
    			'community' => $community,
       			'id' => $id,
    			'csrfForm' => $csrfForm,
    			'message' => $message,
    			'error' => $error
    	));
   		$view->setTerminal(true);
   		return $view;
    }
    
	public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) return $this->redirect()->toRoute('index');

    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Retrieve the organizational unit
		$community = Community::get($id);
    	
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) {

    			// Atomicity
    			$connection = Community::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $community->delete($request->getPost('update_time'));
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
					}
    			}
           	    catch (\Exception $e) {
	    			$connection->rollback();
	    			throw $e;
	    		}
    		}  
    	}
    	$view = new ViewModel(array(
    		'context' => $context,
			'config' => $context->getconfig(),
    		'community' => $community,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }
}
    