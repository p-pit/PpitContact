<?php
namespace PpitContact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use PpitContact\Model\ContactEvent;
use PpitContact\Model\TmpVcard;
use PpitContact\Model\Vcard;
use PpitContact\Model\VcardProperty;
use PpitContact\Form\VcardForm;
use PpitContact\Form\VcardDevisForm;
use PpitCore\Controller\Functions;
use PpitMasterData\Model\Customer;
use PpitOrder\Model\Order;
use PpitUser\Model\User;
use PpitUser\Model\UserRoleLinker;
use SplFileObject;
use Zend\Db\Sql\Expression;

class VcardController extends AbstractActionController
{
	protected $customerTable;
	protected $contactEventTable;
	protected $orderTable;
	protected $tmpVcardTable;
	protected $emailRegex = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/";
	protected $telRegexRequired = "/^\+?([0-9\. ]+)$/";
	protected $telRegex = "/^\+?([0-9\. ]*)$/";
	
   public function indexAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
    	// Prepare the SQL request
    	$currentPage = $this->params()->fromQuery('page', 1);
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'n_last';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $select = $this->getVcardTable()->getSelect()
        	->where(array('instance_id' => $current_user->instance_id, 'id <> ?' => 1))
        	->order(array($major.' '.$dir, 'n_last', 'n_first'));
       	$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\DbSelect($select, $adapter));
    	$paginator->setCurrentPageNumber($currentPage);
    	$paginator->setDefaultItemCountPerPage(30);
    	
    	// Create the email array
    	$select = $this->getVcardTable()->getSelect()->order(array('n_last', 'n_first'))
    	->join('contact_vcard_property', 'contact_vcard.id = contact_vcard_property.vcard_id', array('text_value'))
    	->where(array('contact_vcard_property.name' => 'EMAIL', 'contact_vcard_property.type' => 'work'));
    	$cursor = $this->getVcardTable()->selectWith($select);
		$emails = array();
		foreach ($cursor as $email) $emails[$email->id] = $email->text_value;

		// Create the phone array
		$select = $this->getVcardTable()->getSelect()->order(array('n_last', 'n_first'))
		->join('contact_vcard_property', 'contact_vcard.id = contact_vcard_property.vcard_id', array('text_value'))
		->where(array('contact_vcard_property.name' => 'TEL_work', 'contact_vcard_property.type' => 'work'));
		$cursor = $this->getVcardTable()->selectWith($select);
		$tels = array();
		foreach ($cursor as $tel) $tels[$tel->id] = $tel->text_value;
		
    	// Return the page
    	return new ViewModel(array(
    		'current_user' => $current_user,
    		'title' => 'Contact',
    		'major' => $major,
    		'dir' => $dir,
    		'vcards' => $paginator,
    		'emails' => $emails,
    		'tels' => $tels
        ));
    }
    
    public function addAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
    	$form = new VcardForm();
        $form->addElements($this->getServiceLocator()->get('translator'));
        $form->get('submit')->setValue($this->getServiceLocator()->get('translator')->translate('Add'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $vcard = new Vcard();
            $form->setInputFilter($vcard->getInputFilter());
            $form->setData($request->getPost());
            $validator = new \Zend\Validator\Db\NoRecordExists(
			    array(
			        'table'   => 'contact_vcard',
			        'field'   => 'n_fn',
			        'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
			    )
			);
   			$form->getInputFilter()->get('n_last')->getValidatorChain()->addValidator($validator);

            if ($form->isValid()) {
            	            	 
            	// Check the email
		    	if (!preg_match($this->emailRegex, $form->get('EMAIL')->getValue())) {
		    		$form->get('EMAIL')->setMessages(array(array('EMAIL' => $this->getServiceLocator()->get('translator')->translate('This is not a valid email address'))));
		    	}
            	// Check the work phone
		    	elseif (!preg_match($this->telRegexRequired, $form->get('TEL_work')->getValue())) {
		    		$form->get('TEL_work')->setMessages(array(array('TEL_work' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
		    	}
                // Check the cell phone
		    	elseif (!preg_match($this->telRegex, $form->get('TEL_cell')->getValue())) {
		    		$form->get('TEL_cell')->setMessages(array(array('TEL_cell' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
		    	}
		    	else {
      				$vcard->exchangeArray($form->getData());
	                $vcard->id = NULL;
	                $vcard->n_fn = $vcard->n_last.', '.$vcard->n_first;
	                $id = $this->getVcardTable()->save($vcard, $current_user);
	
	                // Add the vcard properties
	                $property = new VcardProperty();
	                $property->vcard_id = $id;

	                // Organization
	                $property->order = 1;
	                $property->name = 'ORG';
	                $property->type = NULL;
	                $property->text_value = $form->get('ORG')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);
	                 
	                // Email
	                $property->order = 2;
	                $property->name = 'EMAIL';
	                $property->type = 'work';
	                $property->text_value = $form->get('EMAIL')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);
	                 
	                // Work phone
	                $property->order = 3;
	                $property->name = 'TEL_work';
	                $property->type = 'work';
	                $property->text_value = $form->get('TEL_work')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Cellular phone
	                $property->order = 4;
	                $property->name = 'TEL_cell';
	                $property->type = 'cell';
	                $property->text_value = $form->get('TEL_cell')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - Street
	                $property->order = 5;
	                $property->name = 'ADR_street';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_street')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - Extended
	                $property->order = 6;
	                $property->name = 'ADR_extended';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_extended')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - Post office box
	                $property->order = 7;
	                $property->name = 'ADR_post_office_box';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_post_office_box')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - Zip
	                $property->order = 8;
	                $property->name = 'ADR_zip';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_zip')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - City
	                $property->order = 9;
	                $property->name = 'ADR_city';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_city')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Address - Country
	                $property->order = 10;
	                $property->name = 'ADR_country';
	                $property->type = 'work';
	                $property->text_value = $form->get('ADR_country')->getValue();
	                $this->getVcardPropertyTable()->save($property, $current_user);

	                // Redirect to the user list
	                return $this->redirect()->toRoute('vcard/index');
      			}
            }
        }
        return array(
    			'current_user' => $current_user,
    			'title' => 'Contact',
        		'form' => $form,
        );
    }

    public function devisAction()
    {
    	// No user connected : Log as guest user
    	$current_user = new User();
    	$current_user->username = 'guest';
    	$current_user->retrieveHabilitations($this);
    
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
    			if (!preg_match($this->emailRegex, $form->get('EMAIL')->getValue())) {
    				$form->get('EMAIL')->setMessages(array(array('EMAIL' => $this->getServiceLocator()->get('translator')->translate('This is not a valid email address'))));
    			}
    			// Check the work phone
    			elseif (!preg_match($this->telRegexRequired, $form->get('TEL_work')->getValue())) {
    				$form->get('TEL_work')->setMessages(array(array('TEL_work' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
    			}
    			// Check the cell phone
    			elseif (!preg_match($this->telRegex, $form->get('TEL_cell')->getValue())) {
    				$form->get('TEL_cell')->setMessages(array(array('TEL_cell' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
    			}
    			else {
    				$vcard->exchangeArray($form->getData());
    				$vcard->id = NULL;
    				$vcard->n_fn = $vcard->n_last.', '.$vcard->n_first;
    				$id = $this->getVcardTable()->save($vcard, $current_user);
    
    				// Add the vcard properties
    				$property = new VcardProperty();
    				$property->vcard_id = $id;
    
    				// Organization
    				$property->order = 1;
    				$property->name = 'ORG';
    				$property->type = NULL;
    				$property->text_value = $form->get('ORG')->getValue();
    				$this->getVcardPropertyTable()->save($property, $current_user);
    
    				// Email
    				$property->order = 2;
    				$property->name = 'EMAIL';
    				$property->type = 'work';
    				$property->text_value = $form->get('EMAIL')->getValue();
    				$this->getVcardPropertyTable()->save($property, $current_user);
    
    				// Work phone
    				$property->order = 3;
    				$property->name = 'TEL_work';
    				$property->type = 'work';
    				$property->text_value = $form->get('TEL_work')->getValue();
    				$this->getVcardPropertyTable()->save($property, $current_user);
    
    				// Cellular phone
    				$property->order = 4;
    				$property->name = 'TEL_cell';
    				$property->type = 'cell';
    				$property->text_value = $form->get('TEL_cell')->getValue();
    				$this->getVcardPropertyTable()->save($property, $current_user);

    				if ($form->get('souscrire')->getValue()) {

	    				// Create the customer
	    				$customer = new Customer();
	    				$customer->name = $form->get('ORG')->getValue();
	    				$customer->contact_id = $id;
	    				$customer_id = $this->getCustomerTable()->save($customer, $current_user);
	    				
	    				// Create the order
						$order = new Order();
						$order->customer_id = $customer_id;
						$order->order_date = date('Y-m-d');
						$order->caption = 'location gratuite 1 an - 50 utilisateurs';    				
						$this->getOrderTable()->save($order, $current_user);
    				}

    				if ($form->get('quotation')->getValue()) {
    				
    					// Create the customer
    					$event = new ContactEvent();
    					$event->contact_id = $id;
    					$event->type = 'quotation';
						$event->date = date('Y-m-d');
						$event->caption = 'Demander un devis location ou achat premium';
						$event->comment = $form->get('comment')->getValue();
						$event_id = $this->getContactEventTable()->save($event, $current_user);
    				}
    				
    				// Redirect to the user list
    				return $this->redirect()->toRoute('zfcuser/login');
    			}
    		}
    	}
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Notes de frais',
    			'form' => $form,
    	);
    }
    
    public function updateAction()
    {
        // Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('vcard/index');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
    	// Retrieve the vcard and its properties
    	$vcard = $this->getVcardTable()->get($id, $current_user);
    	$select = $this->getVcardPropertyTable()->getSelect()->where(array('vcard_id' => $id));
    	$cursor = $this->getVcardPropertyTable()->selectWith($select, $current_user);
    	$properties = array();
    	foreach($cursor as $property) $properties[$property->name] = $property->text_value;
    	
		// Create and fill the form
    	$form = new VcardForm();
    	$form->addElements($this->getServiceLocator()->get('translator'));
        $form->bind($vcard);
		foreach($properties as $name => $value) $form->get($name)->setValue($value);

        $form->get('submit')->setAttribute('value', $this->getServiceLocator()->get('translator')->translate('Update'));
        $request = $this->getRequest();
        
        // process the post request (form filled and submitted)
        if ($request->isPost()) {
            $form->setInputFilter($vcard->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
            	            	            	
            	// Check the email
            	if (!preg_match($this->emailRegex, $form->get('EMAIL')->getValue())) {
            		$form->get('EMAIL')->setMessages(array(array('EMAIL' => $this->getServiceLocator()->get('translator')->translate('This is not a valid email address'))));
            	}
            	// Check the work phone
            	elseif (!preg_match($this->telRegexRequired, $form->get('TEL_work')->getValue())) {
            		$form->get('TEL_work')->setMessages(array(array('TEL_work' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
            	}
            	// Check the cell phone
            	elseif (!preg_match($this->telRegex, $form->get('TEL_cell')->getValue())) {
            		$form->get('TEL_cell')->setMessages(array(array('TEL_cell' => $this->getServiceLocator()->get('translator')->translate('This is not a valid phone number'))));
            	}
            	else {
            		// Update the vcard
	                $vcard->n_fn = $vcard->n_last.', '.$vcard->n_first;
            		$this->getVcardTable()->save($vcard, $current_user);
            	
            		// Delete then add the vcard properties
            		$this->getVcardPropertyTable()->multipleDelete(array('vcard_id' => $vcard->id));
            		
            		$property = new VcardProperty();
            		$property->vcard_id = $id;

            		// Organization
            		$property->order = 1;
            		$property->name = 'ORG';
            		$property->type = NULL;
            		$property->text_value = $form->get('ORG')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);

            		// Email
            		$property->order = 2;
            		$property->name = 'EMAIL';
            		$property->type = 'work';
            		$property->text_value = $form->get('EMAIL')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            		
            		// Work phone
            		$property->order = 3;
            		$property->name = 'TEL_work';
            		$property->type = 'work';
            		$property->text_value = $form->get('TEL_work')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Cellular phone
            		$property->order = 4;
            		$property->name = 'TEL_cell';
            		$property->type = 'cell';
            		$property->text_value = $form->get('TEL_cell')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - Street
            		$property->order = 5;
            		$property->name = 'ADR_street';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_street')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - Extended
            		$property->order = 6;
            		$property->name = 'ADR_extended';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_extended')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - Post office box
            		$property->order = 7;
            		$property->name = 'ADR_post_office_box';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_post_office_box')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - Zip
            		$property->order = 8;
            		$property->name = 'ADR_zip';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_zip')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - City
            		$property->order = 9;
            		$property->name = 'ADR_city';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_city')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
            	
            		// Address - Country
            		$property->order = 10;
            		$property->name = 'ADR_country';
            		$property->type = 'work';
            		$property->text_value = $form->get('ADR_country')->getValue();
            		$this->getVcardPropertyTable()->save($property, $current_user);
	
	                // Redirect to the user list
	                return $this->redirect()->toRoute('vcard/index');
            	}
            }
        }
        return array(
    		'current_user' => $current_user,
    		'title' => 'Contact',
        	'form' => $form,
        	'id' => $id
        );
    }

    public function importAction()
    {
    	// Check the presence of the id parameter for the entity to import
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('vcard');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
    	// Retrieve the link and its parent folder
    	$link = $this->getLinkTable()->get($id, $current_user);
    	$parent_id = $link->parent_id;

    	// Retrieve the role list
    	$config = $this->getServiceLocator()->get('config');
    	$settings = $config['ppitUserSettings'];
    	$roles = array();
    	foreach ($settings['roles'] as $role_id => $role_caption) $roles[$role_caption] = $role_id;
    	 
    	$file = 'data/documents/'.$link->id;
    	$validity = Functions::controlCsv(
    							$file, // Path to the file
    							array(255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, $roles), // Type list
    							TRUE, // Ignore first row (column headers)
    							500); // Max number of rows
    	foreach ($validity as $ok => $content) { // content is a list of errors if not ok
			// sort between duplicate and not duplicate rows according to the primary key last_name + first_name
			$not_duplicate = array();
			$duplicate = array();
    		if ($ok) {
    			$emails = array();
    			foreach ($content as $row) {
	    			$select = $this->getVcardPropertyTable()->getSelect()->where(array('name' => 'EMAIL', 'text_value' => $row[4]));
    				$select = $this->getVcardPropertyTable()->getSelect()->where(array('name' => 'EMAIL', 'text_value' => $row[4]));
	    			$cursor = $this->getVcardPropertyTable()->selectWith($select);
	    			if (count($cursor) > 0) $duplicate[] = $row;
	    			elseif (array_key_exists($row[4], $emails)) $duplicate[] = $row;
    				else {
	    				$not_duplicate[] = $row;
	    				$emails[$row[4]] = null;
    				}
    			}
			    $request = $this->getRequest();
		        if ($request->isPost()) {
		            $confirm = $request->getPost('confirm', $this->getServiceLocator()->get('translator')->translate('No'));
		
		            if ($confirm == $this->getServiceLocator()->get('translator')->translate('Import the data')) {
		            	
						// Load the temporary array
		            	$tmpVcards = array();
		            	foreach ($not_duplicate as $row) {
    						$tmpVcard[] = array();
		            		$tmpVcard['n_title'] = $row[0];
		            		$tmpVcard['n_last'] = $row[1];
		            		$tmpVcard['n_first'] = $row[2];
		            		$tmpVcard['org'] = $row[3];
		            		$tmpVcard['email'] = $row[4];
		            		$tmpVcard['tel_work'] = $row[5];
		            		$tmpVcard['tel_cell'] = $row[6];
		            		$tmpVcard['adr_street'] = $row[7];
		            		$tmpVcard['adr_extended'] = $row[8];
		            		$tmpVcard['adr_post_office_box'] = $row[9];
		            		$tmpVcard['adr_zip'] = $row[10];
		            		$tmpVcard['adr_city'] = $row[11];
		            		$tmpVcard['adr_country'] = $row[12];
		            		$tmpVcard['role'] = $row[13];
    								
    						$tmpVcards[] = $tmpVcard;
			            }
		            	$vcard = new Vcard();
		            	$property = new VcardProperty();
		            	$user = new User();
		            	$userRL = new UserRoleLinker();
		            	 
		            	foreach ($tmpVcards as $tmpVcard) {
		            		$vcard->n_title = $tmpVcard['n_title'];
		            		$vcard->n_first = $tmpVcard['n_first'];
		            		$vcard->n_last = $tmpVcard['n_last'];
		            		$vcard->n_fn = $tmpVcard['n_last'].', '.$tmpVcard['n_first'];
		            		$vcard_id = $this->getVcardTable()->save($vcard, $current_user);
            	
		            		// Add the vcard properties
		            		$property->vcard_id = $vcard_id;

		            		// Organization
		            		$property->order = 1;
		            		$property->name = 'ORG';
		            		$property->type = NULL;
		            		$property->text_value = $tmpVcard['org'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);

		            		// Email
		            		$property->order = 2;
		            		$property->name = 'EMAIL';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['email'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            		
		            		// Work phone
		            		$property->order = 3;
		            		$property->name = 'TEL_work';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['tel_work'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Cellular phone
		            		$property->order = 4;
		            		$property->name = 'TEL_cell';
		            		$property->type = 'cell';
		            		$property->text_value = $tmpVcard['tel_cell'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - Street
		            		$property->order = 5;
		            		$property->name = 'ADR_street';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_street'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - Extended
		            		$property->order = 6;
		            		$property->name = 'ADR_extended';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_extended'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - Post office box
		            		$property->order = 7;
		            		$property->name = 'ADR_post_office_box';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_post_office_box'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - Zip
		            		$property->order = 8;
		            		$property->name = 'ADR_zip';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_zip'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - City
		            		$property->order = 9;
		            		$property->name = 'ADR_city';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_city'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);
		            	
		            		// Address - Country
		            		$property->order = 10;
		            		$property->name = 'ADR_country';
		            		$property->type = 'work';
		            		$property->text_value = $tmpVcard['adr_country'];
		            		$this->getVcardPropertyTable()->save($property, $current_user);

		            		if ($tmpVcard['email'] && $tmpVcard['role']) {
	    						// Insert the user
								$user->instance_id = $current_user->instance_id;
	    						$user->username = $tmpVcard['email'];
						        $user->state = 1;
								$user->password = md5(uniqid(rand(), true));
								$user->contact_id = $vcard_id;
						        $user_id = $this->getUserTable()->save($user, $current_user);
	
						        // Sets the role
						        $userRL->user_id = $user_id;
						        $userRL->role_id = $roles[$tmpVcard['role']];
						        $this->getUserRoleLinkerTable()->save($userRL, $current_user);
	
				                // Send the email to the user
								$config = $this->getServiceLocator()->get('config');
								$settings = $config['ppitCoreSettings'];
				                \PpitCore\Controller\Functions::envoiMail(
				                		$this->getServiceLocator(),
				                		$user->username,
				                		'Afin de saisir votre mot de passe initial pour votre identifiant : '.$user->username.
				                		' veuillez cliquer sur ce lien : '.
				                		$settings['domainName'].'ppit-user/initpassword/'.$user_id.'?hash='.$user->password,
				                		'Vos identifiants',
				                		NULL, NULL);
		            		}
		            	}
		            }
		            return $this->redirect()->toRoute('vcard/index', array('id' => 2));
		        }

		        return array(
    				'current_user' => $current_user,
    				'title' => 'Contact',
		        	'id'    => $id,
    				'ok' => $ok,
    				'not_duplicate' => $not_duplicate,
    				'duplicate' => $duplicate
    			);
    		}
			else {
	    		// Return the page
	    		return new ViewModel(array(
    				'current_user' => $current_user,
    				'title' => 'Contact',
	    			'ok' => $ok,
	    			'errors' => $content
	    		));
			}
    	}
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('vcard/index');
        }
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
        // retrieve the vcard
	    $vcard = $this->getVcardTable()->get($id);
        
	    $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', $this->getServiceLocator()->get('translator')->translate('No'));

            if ($del == $this->getServiceLocator()->get('translator')->translate('Confirm')) {
                $id = (int) $request->getPost('id');
                $this->getVcardTable()->delete($id);
				$this->getVcardPropertyTable()->multipleDelete(array('vcard_id' => $id));
            }

            // Redirect to the index
            return $this->redirect()->toRoute('vcard/index');
        }

        return array(
    			'current_user' => $current_user,
    			'title' => 'Contact',
        		'id'    => $id,
        );
    }

    public function getCustomerTable()
    {
    	if (!$this->customerTable) {
    		$sm = $this->getServiceLocator();
    		$this->customerTable = $sm->get('PpitMasterData\Model\CustomerTable');
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

    // Don't remove if using UserTable::retrieveHabilitations
    public $routes;
    protected $instanceTable;
	protected $linkTable;
    protected $userTable;
    protected $userPerimeterTable;
    protected $userRoleTable;
    protected $userRoleLinkerTable;
    protected $vcardTable;
    protected $vcardPropertyTable;
    
    public function getInstanceTable()
    {
    	if (!$this->instanceTable) {
    		$sm = $this->getServiceLocator();
    		$this->instanceTable = $sm->get('PpitCore\Model\InstanceTable');
    	}
    	return $this->instanceTable;
    }

    public function getLinkTable()
    {
    	if (!$this->linkTable) {
    		$sm = $this->getServiceLocator();
    		$this->linkTable = $sm->get('PpitCore\Model\LinkTable');
    	}
    	return $this->linkTable;
    }
    
    public function getUserTable()
    {
    	if (!$this->userTable) {
    		$sm = $this->getServiceLocator();
    		$this->userTable = $sm->get('PpitUser\Model\UserTable');
    	}
    	return $this->userTable;
    }
    
    public function getUserPerimeterTable()
    {
    	if (!$this->userPerimeterTable) {
    		$sm = $this->getServiceLocator();
    		$this->userPerimeterTable = $sm->get('PpitUser\Model\UserPerimeterTable');
    	}
    	return $this->userPerimeterTable;
    }
    
    public function getUserRoleTable()
    {
    	if (!$this->userRoleTable) {
    		$sm = $this->getServiceLocator();
    		$this->userRoleTable = $sm->get('PpitUser\Model\UserRoleTable');
    	}
    	return $this->userRoleTable;
    }
    
    public function getUserRoleLinkerTable()
    {
    	if (!$this->userRoleLinkerTable) {
    		$sm = $this->getServiceLocator();
    		$this->userRoleLinkerTable = $sm->get('PpitUser\Model\UserRoleLinkerTable');
    	}
    	return $this->userRoleLinkerTable;
    }
    
    public function getVcardTable()
    {
    	if (!$this->vcardTable) {
    		$sm = $this->getServiceLocator();
    		$this->vcardTable = $sm->get('PpitContact\Model\VcardTable');
    	}
    	return $this->vcardTable;
    }
    
    public function getVcardPropertyTable()
    {
    	if (!$this->vcardPropertyTable) {
    		$sm = $this->getServiceLocator();
    		$this->vcardPropertyTable = $sm->get('PpitContact\Model\VcardPropertyTable');
    	}
    	return $this->vcardPropertyTable;
    }
}
