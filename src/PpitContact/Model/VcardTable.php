<?php
namespace PpitContact\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Session\Container;
use Zend\Log\Logger;
use Zend\Log\Writer;

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
    
    public function selectWith($select, $user)
    {
    	$table = $this->tableGateway->getTable();
//		echo $select->getSqlString($this->getAdapter()->getPlatform()).'<br>';
		return $this->tableGateway->selectWith($select);
    }

    public function fetchDistinct($column, $user)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
			   ->where(array('instance_id' => $user->instance_id))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $user, $column = 'id')
    {
    	$where = array($column => $id);
       	/*if ($user->role_id != 'super_admin')*/ $where['instance_id'] = $user->instance_id;
    	$rowset = $this->tableGateway->select($where);
    	$row = $rowset->current();
/*    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}*/
    	return $row;
    }

    public function save($data, $user, $log = false)
    {
		if ($user->instance_id != 0 || $user->role_id == 'super_admin') { // Instance 0 is for demo => Not updatable
	    	if ($user->role_id != 'super_admin') $data['instance_id'] = $user->instance_id;
			$data['update_time'] = date("Y-m-d H:i:s");
			$data['update_user'] = $user->user_id;
	        $id = $data['id'];
	        if ($id == 0) {
	        	$data['creation_time'] = date("Y-m-d H:i:s");
	        	$data['creation_user'] = $user->user_id;
	        	$this->tableGateway->insert($data);
	        	
		        // Write to the log
    			if ($log) {
	    			$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
		            $logger = new Logger();
		            $logger->addWriter($writer);
		            $content = '';
		            foreach ($data as $cell) $content .= ';'.$cell;
		            $logger->info('add;'.$user->user_id.$content);
	        	}

	            return $this->getAdapter()->getDriver()->getLastGeneratedValue();
	        }
	        else {
	            if ($this->get($id, $user)) {

					$where = array('id' => $id);
					if ($user->role_id != 'super_admin') $where['instance_id'] = $user->instance_id;
	            	$this->tableGateway->update($data, $where);
	                    	
	        		// Write to the log
			    	if ($log) {
	    				$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            		$logger = new Logger();
			            $logger->addWriter($writer);
			            $content = '';
			            foreach ($data as $cell) $content .= ';'.$cell;
			            $logger->info('update;'.$user->user_id.$content);
		        	}
		        	return $id;
	            }
	            else {
	                throw new \Exception('Form id does not exist');
	            }
	        }
		}
		else return 0;
    }
    
    public function delete($id, $user, $column = 'id', $log = false)
    {
		if ($user->instance_id != 0 || $user->role_id == 'super_admin') { // Instance 0 is for demo => Not updatable

			$where = array($column => $id);
			if ($user->role_id != 'super_admin') $where['instance_id'] = $user->instance_id;
			$this->tableGateway->delete($where);

			// Write to the log
    		if ($log) {
	    		$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $logger->info('delete;'.$user->user_id.$id);
	        }
		}
    }

    public function multipleDelete($where, $user, $log = false)
    {
		if ($user->instance_id != 0 || $user->role_id == 'super_admin') { // Instance 0 is for demo => Not updatable

			if ($user->role_id != 'super_admin') $where['instance_id'] = $user->instance_id;
	    	$this->tableGateway->delete($where);

	    	// Write to the log
	    	if ($log) {
	    		$writer = new Writer\Stream('data/log/'.$this->tableGateway->getTable().'.txt');
	            $logger = new Logger();
	            $logger->addWriter($writer);
	            $content = '';
	            foreach ($where as $column => $value) $content .= ';'.$column.'='.$value;
	            $logger->info('multiple_delete;'.$user->user_id.$content);
	        }
		}
    }
}
