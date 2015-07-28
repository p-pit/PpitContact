<?php
namespace PpitContact\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;

class VcardTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAdapter()
    {
    	return $this->tableGateway->getAdapter();
    }
    
    public function getSelect()
    {
		$select = new \Zend\Db\Sql\Select();
	    $select->from($this->tableGateway->getTable());
    	return $select;
    }

    public function selectWith($select)
    {
		return $this->tableGateway->selectWith($select);
    }

    public function fetchDistinct($column)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $column = 'id')
    {
    	$rowset = $this->tableGateway->select(array($column => $id));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }

    public function save($entity, $user)
    {
		if ($user->instance_id != 0) {

			$data = array();
	
	        // Specific
	        $data['n_title'] = $entity->n_title;
	        $data['n_first'] = $entity->n_first;
	        $data['n_last'] = $entity->n_last;
	        $data['n_fn'] = $entity->n_fn;
	        
	        $data['instance_id'] = $user->instance_id;
			$data['update_time'] = date("Y-m-d H:i:s");
			$data['update_user'] = $user->user_id;
	        $id = (int)$entity->id;
	        if ($id == 0) {
	        	$data['creation_time'] = date("Y-m-d H:i:s");
	        	$data['creation_user'] = $user->user_id;
	        	$this->tableGateway->insert($data);
	        	return $this->getAdapter()->getDriver()->getLastGeneratedValue();
	        } else {
	            if ($this->get($id)) {
	                $this->tableGateway->update($data, array('id' => $id));
	            } else {
	                throw new \Exception('Form id does not exist');
	            }
	        }
		}
    }

    public function delete($id)
    {
	    if ($this->get($id)->instance_id != 0) {
	    	$this->tableGateway->delete(array('id' => $id));
	    }
    }

    public function multipleDelete($where)
    {
	    $where['instance_id <> ?'] = 0; // Instance 0 is for demo, not updatable
       	$this->tableGateway->delete($where);
    }
}
