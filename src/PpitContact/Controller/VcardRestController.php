<?php 
namespace PpitContact\Controller;

use PpitCore\Controller\Functions;
use PpitCore\Controller\PpitRestController;
use PpitContact\Model\Vcard;
use PpitContact\Model\VcardProperty;
use Zend\View\Model\JsonModel;
 
class VcardRestController extends PpitRestController
{
	
	public function getList()
    {
    	// Retrieve the current user
    	$currentUser = Functions::getUser($this);
    	$currentUser->retrieveHabilitations($this);
    	
    	$filter = $this->params()->fromQuery('filter', NULL);
    	$select = $this->getVcardTable()->getSelect();
    	$select->where->like('n_last', $filter.'%');
    	$select->order(array('n_last'));
    	$cursor = $this->getVcardTable()->selectWith($select, $currentUser);
	    $data = array();
	    foreach($cursor as $contact) {
	        $data[] = $contact;
	    }
	    return new JsonModel(array('data' => $data));
    }
 
    public function get($id)
    {
    	// Retrieve the current user
    	$currentUser = Functions::getUser($this);
    	$currentUser->retrieveHabilitations($this);
    	
    	$contact = $this->getVcardTable()->get($id, $currentUser);
    	$contact->retrieveProperties($this->getVcardPropertyTable(), $currentUser);
    	return new JsonModel(array("data" => $contact));
    }
 
    public function create($data)
    {
	    
	    return new JsonModel(array(
	        'id' => $id,
	    ));
    }
 
    public function update($id, $data)
    {
        # code...
    }
 
    public function delete($id)
    {
        # code...
    }
}
