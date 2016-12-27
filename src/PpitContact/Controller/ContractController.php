<?php

namespace PpitContact\Controller;

use PpitContact\Model\Contract;
use PpitCore\Model\Csrf;
use PpitCore\Model\Context;
use PpitCore\Form\CsrfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractController extends AbstractActionController
{
    public function listAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Filter
    	$filter = array();
    	$communityId = $this->params()->fromRoute('community_id', NULL);
    	if ($communityId) $filter['customer_community_id'] = $communityId;

		// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'customer_name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    
		$contracts = Contract::getList($major, $dir, $filter);
    
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getconfig(),
    			'major' => $major,
    			'dir' => $dir,
    			'contracts' => $contracts,
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
    	$type = $this->params()->fromQuery('customer_name', NULL);
    	if ($type) $filter['customer_name'] = $name;
    	
    	// Order
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'customer_name';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	     
		$contracts = Contract::getList($major, $dir, $filter);
       	$data = array();
    	foreach ($contracts as $contract) {
    		$data[] = array(
    				'id' => $contract->id,
    				'customer_community_id' => $contract->customer_community_id,
    				'customer_name' => $contract->customer_name,
    				'supplyer_community_id' => $contract->supplyer_community_id,
    				'supplyer_name' => $contract->supplyer_name,
    		);
    	}
    	return new JsonModel(array('data' => $data));
    }
    
    public function addAction()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$contract = Contract::instanciate();
    	
    	$csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');
    	$message = null;
    	$error = null;
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    
    		if ($csrfForm->isValid()) { // CSRF check
    			$contract->loadDataFromRequest($request);
    				
    			// Atomicity
    			$connection = Contract::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Add
    				$return = $contract->add();
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
    			'contract' => $contract,
//       			'id' => $id,
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
		$contract = Contract::get($id);
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
    			$connection = Contract::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $contract->delete($contract->update_time);
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
    		'contract' => $contract,
    		'id' => $id,
    		'csrfForm' => $csrfForm,
    		'message' => $message,
    		'error' => $error,
    	));
   		$view->setTerminal(true);
   		return $view;
    }
}
    