<?php
namespace PpitContact\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class VcardProperty implements InputFilterAwareInterface
{
    public $id;
    public $vcard_id;
    public $order;
    public $name;
    public $type;
    public $text_value;
    public $blob_value;
    protected $inputFilter;                       // <-- Add this variable

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['vcard_id'] = (int) $this->vcard_id;
    	$data['order'] = $this->order;
    	$data['name'] = $this->name;
    	$data['type'] = $this->type;
    	$data['text_value'] = $this->text_value;
    	$data['blob_value'] = $this->blob_value;
    
    	return $data;
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
        $this->order = (isset($data['order'])) ? $data['order'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->text_value = (isset($data['text_value'])) ? $data['text_value'] : null;
        $this->blob_value = (isset($data['blob_value'])) ? $data['blob_value'] : null;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
        	$this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
