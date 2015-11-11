<?php
namespace PpitContact\Controller;

use PpitCore\Controller\PpitController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use PpitContact\Model\ContactEvent;
use PpitContact\Model\TmpVcard;
use PpitContact\Model\Vcard;
use PpitContact\Model\VcardProperty;
use PpitContact\Form\VcardForm;
use PpitContact\Form\VcardDevisForm;
use PpitCore\Controller\Functions;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Csrf;
use PpitCustomer\Model\Customer;
use PpitOrder\Model\Order;
use PpitUser\Model\User;
use PpitUser\Model\UserRoleLinker;
use SplFileObject;
use Zend\Db\Sql\Expression;

class VcardController extends PpitController
{
	protected $customerTable;
	protected $contactEventTable;
	protected $orderTable;
	protected $tmpVcardTable;

	public $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
	public $telRegex = "/^\+?([0-9\. ]*)$/";
	
	public function indexAction()
    {
    	// Retrieve the current user
    	$currentUser = Functions::getUser($this);
    	$currentUser->retrieveHabilitations($this);

    	// Control access to other customers (limited to supplyer-side users)
    	if ($currentUser->customer_id) $customer_id = $currentUser->customer_id;
       	else $customer_id = (int) $this->params()->fromRoute('customer_id', 0);
	    	
	    if ($customer_id) {
	    	$customer = $this->getCustomerTable()->get($customer_id, $currentUser);
	    }
	    else $customer = null;

    	// Prepare the SQL request
    	$currentPage = $this->params()->fromQuery('page', 1);
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'n_last';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $select = $this->getVcardTable()->getSelect()
        	->order(array($major.' '.$dir, 'n_last', 'n_first'));
		if ($customer_id) $select->where(array('customer_id' => $customer_id));
        $cursor = $this->getVcardTable()->selectWith($select, $currentUser);

        $vcards = Vcard::visibleContactList($cursor, $customer_id, $currentUser);
        
    	// Create the email array
    	$select = $this->getVcardTable()->getSelect()->order(array('n_last', 'n_first'))
	    	->join('contact_vcard_property', 'contact_vcard.id = contact_vcard_property.vcard_id', array('text_value'))
	    	->where(array('contact_vcard_property.name' => 'EMAIL', 'contact_vcard_property.type' => 'work'));
    	$cursor = $this->getVcardTable()->selectWith($select, $currentUser);
		$emails = array();
		foreach ($cursor as $email) $emails[$email->id] = $email->text_value;

		// Create the phone array
		$select = $this->getVcardTable()->getSelect()->order(array('n_last', 'n_first'))
			->join('contact_vcard_property', 'contact_vcard.id = contact_vcard_property.vcard_id', array('text_value'))
			->where(array('contact_vcard_property.name' => 'TEL_work', 'contact_vcard_property.type' => 'work'));
		$cursor = $this->getVcardTable()->selectWith($select, $currentUser);
		$tels = array();
		foreach ($cursor as $tel) $tels[$tel->id] = $tel->text_value;
		
    	// Return the page
    	return new ViewModel(array(
    		'currentUser' => $currentUser,
    		'customer_id' => $customer_id,
    		'customer' => $customer,
    		'major' => $major,
    		'dir' => $dir,
    		'vcards' => $vcards,
    		'emails' => $emails,
    		'tels' => $tels
        ));
    }
    
    public function updateAction()
    {
    	// Retrieve the current user
    	$currentUser = Functions::getUser($this);
    	$currentUser->retrieveHabilitations($this);

    	// Control access to other customers (limited to supplyer-side users)
    	$customer_id = (int) $this->params()->fromRoute('customer_id', 0);
    	if ($customer_id) {
    		$customer = $this->getCustomerTable()->get($customer_id, $currentUser);
    		if ($currentUser->customer_id) return $this->redirect()->toRoute('index');
    	}
    	else $customer = null;

    	// Check the presence of an id parameter (update case)
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		// Retrieve the vcard and its properties
    		$vcard = $this->getVcardTable()->get($id, $currentUser);
    		$select = $this->getVcardPropertyTable()->getSelect()
 		   		->where(array('vcard_id' => $id))
    			->order(array('order'));
    		$cursor = $this->getVcardPropertyTable()->selectWith($select, $currentUser);
    		$properties = array();
    		foreach($cursor as $property) {
    			switch ($property->name) {
    				case 'ADR_street' :
    					$vcard->ADR_street = $property->text_value;
    					break;
    				case 'ADR_extended' :
    					$vcard->ADR_extended = $property->text_value;
    					break;
    				case 'ADR_post_office_box' :
    					$vcard->ADR_post_office_box = $property->text_value;
    					break;
    				case 'ADR_zip' :
    					$vcard->ADR_zip = $property->text_value;
    					break;
    				case 'ADR_city' :
    					$vcard->ADR_city = $property->text_value;
    					break;
    				case 'ADR_state' :
    					$vcard->ADR_state = $property->text_value;
    					break;
    				case 'ADR_country' :
    					$vcard->ADR_country = $property->text_value;
    					break;
    			}
    		}
       	}
       	else $vcard = new Vcard();
        
		// Instanciate the csrf form
        $csrfForm = new CsrfForm();
    	$csrfForm->addCsrfElement('csrf');

    	$error = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
			$vcard->customer_id = $customer_id;
        	$vcard->n_title = $request->getPost('n_title');
        	$vcard->n_last = $request->getPost('n_last');
        	$vcard->n_first = $request->getPost('n_first');
        	$vcard->org = $request->getPost('org');
        	$vcard->email = $request->getPost('email');
        	$vcard->tel_work = $request->getPost('tel_work');
        	$vcard->tel_cell = $request->getPost('tel_cell');

        	$vcard->ADR_street = $request->getPost('ADR_street');
        	$vcard->ADR_extended = $request->getPost('ADR_extended');
        	$vcard->ADR_post_office_box = $request->getPost('ADR_post_office_box');
        	$vcard->ADR_zip = $request->getPost('ADR_zip');
        	$vcard->ADR_city = $request->getPost('ADR_city');
        	$vcard->ADR_state = $request->getPost('ADR_state');
        	$vcard->ADR_country = $request->getPost('ADR_country');
        	
        	$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
        	$csrfForm->setData($request->getPost());

            if ($csrfForm->isValid()) {
    			
    			// Double the client controls
    			if (strlen($vcard->n_title) > 255 ||
    				!$vcard->n_first || strlen($vcard->n_first) > 255 ||
					!$vcard->n_last || strlen($vcard->n_last) > 255 ||
					strlen($vcard->org) > 255 ||
					strlen($vcard->email) > 255 ||
    				!preg_match($this->emailRegex, $vcard->email) ||
					strlen($vcard->tel_work) > 255 ||
    				!preg_match($this->telRegex, $vcard->tel_work) ||
					strlen($vcard->tel_cell) > 255 ||
    				!preg_match($this->telRegex, $vcard->tel_cell) ||
    				(!$vcard->tel_cell && !$vcard->tel_work) ||
    				(!$vcard->tel_cell && !$vcard->email) ||
					strlen($vcard->ADR_street) > 255 ||
					strlen($vcard->ADR_extended) > 255 ||
					strlen($vcard->ADR_post_office_box) > 255 ||
					strlen($vcard->ADR_zip) > 255 ||
					strlen($vcard->ADR_city) > 255 ||
					strlen($vcard->ADR_state) > 255 ||
    				strlen($vcard->ADR_country) > 255
    			) throw new \Exception('javascript error');

    			else { // Check for duplicate contact
    				
    				// same first name, last name and email
    				$select = $this->getVcardTable()->getSelect()
    					->where(array('n_first' => $vcard->n_first, 'n_last' => $vcard->n_last, 'email' => $vcard->email));
    				$cursor = $this->getVcardTable()->selectWith($select, $currentUser);
		            if (count($cursor) > 0 && $cursor->current()->id != $id) $error = 'Duplicate';
		            else {
    					// same first name, last name and cellular
		            	$select = $this->getVcardTable()->getSelect()
		            		->where(array('n_first' => $vcard->n_first, 'n_last' => $vcard->n_last, 'tel_cell' => $vcard->tel_cell));
    					$cursor = $this->getVcardTable()->selectWith($select, $currentUser);
		            	if ($vcard->tel_cell && count($cursor) > 0 && $cursor->current()->id != $id) $error = 'Duplicate';
						else {

							// Atomically save the vcard and its properties
				    		$connection = $this->getVcardTable()->getAdapter()->getDriver()->getConnection();
			    			$connection->beginTransaction();
							try {
			    				$vcard->n_fn = $vcard->n_last.', '.$vcard->n_first;
				                $vcard->id = $this->getVcardTable()->save($vcard, $currentUser);

				                // Delete then add the vcard properties
				                $this->getVcardPropertyTable()->multipleDelete(array('vcard_id' => $vcard->id), $currentUser);

				                // Add the vcard properties
				                $property = new VcardProperty();
				                $property->vcard_id = ($id) ? $id : $vcard->id;
			
				                // Address - Street
				                $property->order = 1;
				                $property->name = 'ADR_street';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_street;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
	
				                // Address - Extended
				                $property->order = 2;
				                $property->name = 'ADR_extended';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_extended;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
			
				                // Address - Post office box
				                $property->order = 3;
				                $property->name = 'ADR_post_office_box';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_post_office_box;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
			
				                // Address - Zip
				                $property->order = 4;
				                $property->name = 'ADR_zip';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_zip;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
			
				                // Address - City
				                $property->order = 5;
				                $property->name = 'ADR_city';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_city;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
	
				                // Address - State
				                $property->order = 6;
				                $property->name = 'ADR_state';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_state;
				                $this->getVcardPropertyTable()->save($property, $currentUser);
				                 
				                // Address - Country
				                $property->order = 7;
				                $property->name = 'ADR_country';
				                $property->type = 'work';
				                $property->text_value = $vcard->ADR_country;
				                $this->getVcardPropertyTable()->save($property, $currentUser);

				                $connection->commit();

				                // Redirect
				                return $this->redirect()->toRoute('vcard');
			            	}
							catch (\Exception $e) {
		    					$connection->rollback();
		    					throw $e;
		    				}
						}
		            }
		        }
            }
        }
        return array(
    		'currentUser' => $currentUser,
        	'customer_id' => $customer_id,
        	'customer' => $customer,
        	'id' => $id,
        	'csrfForm' => $csrfForm,
        	'vcard' => $vcard,
        	'error' => $error
        );
    }

    public function devisAction()
    {
        // Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('vcard/index');
    	}
    	$currentUser = $this->getUserTable()->get($id, null);
    	$currentUser->retrieveHabilitations($this);
    
    	$form = new VcardDevisForm();
    	$form->addElements($this->getServiceLocator()->get('translator'));
    	$form->get('submit')->setValue($this->getServiceLocator()->get('translator')->translate('Add'));
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$vcard = new Vcard();
    		$form->setInputFilter($vcard->getDevisInputFilter());
    		$form->setData($request->getPost());
/*    		$validator = new \Zend\Validator\Db\NoRecordExists(
    				array(
    						'table'   => 'contact_vcard',
    						'field'   => 'email',
    						'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
    				)
    		);
    		$form->getInputFilter()->get('email')->getValidatorChain()->addValidator($validator);*/
    		
    		if ($form->isValid()) {
    			 
    			// Check the email
    			if (!preg_match($this->emailRegex, $form->get('email')->getValue())) {
    				$form->get('email')->setMessages(array(array('email' => $this->getServiceLocator()->get('translator')->translate('This is not a valid email address'))));
    			}
    			// Check the work phone
    			elseif (!preg_match($this->telRegex, $form->get('tel_work')->getValue())) {
    				$form->get('tel_work')->setMessages(array(array('tel_work' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
    			}
    			// Check the cell phone
    			elseif (!preg_match($this->telRegex, $form->get('tel_cell')->getValue())) {
    				$form->get('tel_cell')->setMessages(array(array('tel_cell' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
    			}
    			else {
    				$vcard->exchangeArray($form->getData());
    				$vcard->id = NULL;
    				$vcard->instance_id = $currentUser->instance_id;
    				$vcard->n_fn = $vcard->n_last.', '.$vcard->n_first;
    				$id = $this->getVcardTable()->save($vcard, $currentUser);

    				if ($form->get('souscrire')->getValue()) {

	    				// Create the customer
	    				$customer = new Customer();
	    				$customer->instance_id = $currentUser->instance_id;
	    				$customer->name = $form->get('org')->getValue();
	    				$customer->contact_id = $id;
	    				$customer_id = $this->getCustomerTable()->save($customer, $currentUser);
	    				
	    				// Create the order
						$order = new Order();
	    				$order->instance_id = $currentUser->instance_id;
						$order->customer_id = $customer_id;
						$order->order_date = date('Y-m-d');
						$order->caption = 'location gratuite 1 an - 50 utilisateurs';    				
						$this->getOrderTable()->save($order, $currentUser);
    				}

    				if ($form->get('quotation')->getValue()) {
    				
    					// Create the event
    					$event = new ContactEvent();
	    				$event->instance_id = $currentUser->instance_id;
    					$event->contact_id = $id;
    					$event->type = 'quotation';
						$event->date = date('Y-m-d');
						$event->caption = 'Demander un devis location ou achat premium';
						$event->comment = $form->get('comment')->getValue();
						$event_id = $this->getContactEventTable()->save($event, $currentUser);
    				}
    				
    				// Redirect to the user list
    				return $this->redirect()->toRoute('zfcuser/login');
    			}
    		}
    	}
    	return array(
    			'currentUser' => $currentUser,
    			'title' => 'Notes de frais',
    			'form' => $form,
    			'id' => $id,
    	);
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('vcard/index');
        }
    	// Retrieve the current user
    	$currentUser = Functions::getUser($this);
    	$currentUser->retrieveHabilitations($this);
    	 
        // retrieve the vcard
	    $vcard = $this->getVcardTable()->get($id, $currentUser);

	    $csrfForm = new CsrfForm();
	    $csrfForm->addCsrfElement('csrf');

	    $error = null;
	    $request = $this->getRequest();
        if ($request->isPost()) {
    		$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
    		$csrfForm->setData($request->getPost());
    		
    		if ($csrfForm->isValid()) { // CSRF check
    			 
    			// Check if the contact is not bound to a user or an agent
    			$select = $this->getUserTable()->getSelect()->where(array('contact_id' => $vcard->id));
    			if (count($this->getUserTable()->selectWith($select)) > 0) $error = 'Consistency';
    			else {
    				$select = $this->getAgentTable()->getSelect()->where(array('contact_id' => $vcard->id));
    				if (count($this->getAgentTable()->selectWith($select, $currentUser)) > 0) {
    					$error = 'Consistency';
    				}
	    			else {
			        	$this->getVcardTable()->delete($id, $currentUser);
						$this->getVcardPropertyTable()->multipleDelete(array('vcard_id' => $id), $currentUser);

			            // Redirect
			            return $this->redirect()->toRoute('vcard');
	    			}
    			}
    		}
        }

        return array(
    		'currentUser' => $currentUser,
 	  		'csrfForm' => $csrfForm,
        	'vcard' => $vcard,
    		'id' => $id,
    		'error' => $error
        );
    }

    public function getCustomerTable()
    {
    	if (!$this->customerTable) {
    		$sm = $this->getServiceLocator();
    		$this->customerTable = $sm->get('PpitCustomer\Model\CustomerTable');
    	}
    	return $this->customerTable;
    }

    public function getContactEventTable()
    {
    	if (!$this->contactEventTable) {
    		$sm = $this->getServiceLocator();
    		$this->contactEventTable = $sm->get('PpitContact\Model\ContactEventTable');
    	}
    	return $this->contactEventTable;
    }

    public function getOrderTable()
    {
    	if (!$this->orderTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderTable = $sm->get('PpitOrder\Model\OrderTable');
    	}
    	return $this->orderTable;
    }
    
    public function getTmpVcardTable()
    {
    	if (!$this->tmpVcardTable) {
    		$sm = $this->getServiceLocator();
    		$this->tmpVcardTable = $sm->get('PpitContact\Model\TmpVcardTable');
    	}
    	return $this->tmpVcardTable;
    }
}
