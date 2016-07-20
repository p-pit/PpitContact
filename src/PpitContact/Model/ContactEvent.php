<?php
namespace PpitContact\Model;

use PpitCore\Model\Context;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContactEvent implements InputFilterAwareInterface
{
    public $id;
    public $contact_id;
    public $type;
    public $date;
    public $caption;
    public $description;
    public $comment;
    
    protected $inputFilter;

    // Static fields
    private static $table;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['contact_id'] = (int) $this->contact_id;
    	$data['type'] = $this->type;
    	$data['date'] = $this->date;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['comment'] = $this->comment;
    	
    	return $data;
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->contact_id = (isset($data['contact_id'])) ? $data['contact_id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!ContactEvent::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		ContactEvent::$table = $sm->get('PpitCore\Model\ContactEventTable');
    	}
    	return ContactEvent::$table;
    }
}