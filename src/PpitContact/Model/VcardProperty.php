<?php
namespace PpitContact\Model;

use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class VcardProperty implements InputFilterAwareInterface
{
    public $type;
    public $text_value;

    // Additional fields
    public $name;
    public $caption;
    
    // Deprecated fields
    public $id;
    public $instance_id;
    public $vcard_id;
    public $order;
    public $blob_value;
    
    protected $inputFilter;                       // <-- Add this variable

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->text_value = (isset($data['text_value'])) ? $data['text_value'] : null;

        // Additional fields
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        
    	// Deprecated fields
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
        $this->order = (isset($data['order'])) ? $data['order'] : null;
        $this->blob_value = (isset($data['blob_value'])) ? $data['blob_value'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['type'] = $this->type;
    	$data['text_value'] = $this->text_value;

/*    	$data['name'] = $this->name;
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['vcard_id'] = (int) $this->vcard_id;
    	$data['order'] = (int) $this->order;
    	$data['blob_value'] = $this->blob_value;*/
    
    	return $data;
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
}
