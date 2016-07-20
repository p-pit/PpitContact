<?php 
namespace PpitContact\Controller;

use PpitCore\Controller\Functions;
use PpitCore\Model\Context;
use PpitContact\Model\Vcard;
use PpitContact\Model\VcardProperty;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
 
class VcardRestController extends AbstractRestfulController
{
	
	public function getList()
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    
		$instance_id = (int) $this->params()->fromRoute('instance_id', 0);
		$community_id = (int) $this->params()->fromRoute('community_id', 0);

		$data = array();
		$filter = $this->params()->fromQuery('filter', NULL);
//		if ($filter != '') {
			$vcards = Vcard::getList($instance_id, $community_id, 'n_last', 'ASC', $filter);
			foreach ($vcards as $vcard) {
				$data[] = array(
					'id' => $vcard->id,
					'label' => $vcard->n_fn.' - '.(($vcard->email) ? $vcard->email : (($vcard->tel_cell) ? $vcard->tel_cell : $vcard->tel_work)),
					'n_title' => $vcard->n_title,
					'n_last' => $vcard->n_last,
					'n_first' => $vcard->n_first,
					'email' => $vcard->email,
					'tel_work' => $vcard->tel_work,
					'tel_cell' => $vcard->tel_cell,
					'n_title' => $vcard->n_title,
					'properties' => $vcard->properties,
				);
			}
//		}
   	    return new JsonModel(array('data' => $data));
    }
 
    public function get($id)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$contact = Vcard::getTable()->get($id);
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
