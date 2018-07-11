<?php
namespace PpitContact\Model;

use PpitContact\Model\Vcard;
use PpitCore\Model\Context;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class ContactMessage implements InputFilterAwareInterface
{
    public $id;
	public $type;
	public $status;
	public $to;
	public $cc;
	public $cci;
	public $subject;
	public $from_mail;
	public $from_name;
	public $body;
	public $image;
    public $emission_time;
    public $update_time;
    
    // Depreciated
    public $volume;
    public $cost;
    public $accepted;
    public $rejected;

    protected $inputFilter;
    protected $devisInputFilter;

    // Static fields
    private static $table;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        
        // Json decode for lists of "to", "cc", "cci", "accepted" and "rejected"
        $this->to = (isset($data['to'])) ? json_decode($data['to'], true) : null;
        $this->cc = (isset($data['cc'])) ? json_decode($data['cc'], true) : null;
        $this->cci = (isset($data['cci'])) ? json_decode($data['cci'], true) : null;
        $this->subject = (isset($data['subject'])) ? $data['subject'] : null;
        $this->from_mail = (isset($data['from_mail'])) ? $data['from_mail'] : null;
        $this->from_name = (isset($data['from_name'])) ? $data['from_name'] : null;
        $this->body = (isset($data['body'])) ? $data['body'] : null;
        $this->emission_time = (isset($data['emission_time'])) ? $data['emission_time'] : null;
        $this->update_time = (isset($data['updated_time'])) ? $data['updated_time'] : null;
        
    	// Depreciated
        $this->volume = (isset($data['volume'])) ? $data['volume'] : null;
        $this->cost = (isset($data['cost'])) ? $data['cost'] : null;
        $this->accepted = (isset($data['accepted'])) ? ((json_decode($data['accepted'])) ? json_decode($data['accepted']) : array()) : null;
        $this->rejected = (isset($data['rejected'])) ? ((json_decode($data['rejected'])) ? json_decode($data['rejected']) : array()) : null;
    }

    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['type'] = $this->type;
    	$data['status'] = $this->status;
    	$data['to'] = $this->to;
	    $data['cc'] = $this->cc;
    	$data['cci'] = $this->cci;
    	$data['subject'] = $this->subject;
    	$data['from_mail'] = $this->from_mail;
    	$data['from_name'] = $this->from_name;
    	$data['body'] = $this->body;
    	$data['emission_time'] = $this->emission_time;
    	
    	// Depreciated
    	$data['volume'] = (int) $this->volume;
    	$data['cost'] = (float) $this->cost;
    	$data['accepted'] = json_encode($this->accepted);
    	$data['rejected'] = json_encode($this->rejected);
    	 
    	return $data;
    }

    public function toArray()
    {
    	$data = $this->getProperties();
	    $data['to'] = json_encode($this->to);
	    $data['cc'] = json_encode($this->cc);
    	$data['cci'] = json_encode($this->cci);
    	return $data;
    }
    
    public static function getList($type, $params, $major = 'emission_time', $dir = 'DESC', $mode = 'todo', $limitation = 300)
    {
    	$select = ContactMessage::getTable()->getSelect()
	    	->order(array($major.' '.$dir, 'emission_time DESC'));
		if ($limitation) $select->limit($limitation);
	    
		$where = new Where;
    	if ($type) $where->equalTo('type', $type);

    	// Todo list vs search modes
    	if ($mode == 'todo') {
    		$where->isNull('emission_time');
    	}
    	else {
    		// Set the filters
    		foreach ($params as $propertyId => $property) {
    			if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('contact_message.'.substr($propertyId, 4), $params[$propertyId]);
    			elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('contact_message.'.substr($propertyId, 4), $params[$propertyId]);
    			else $where->like('contact_message.'.$propertyId, '%'.$params[$propertyId].'%');
    		}
    	}
    	$select->where($where);
    
    	$cursor = ContactMessage::getTable()->selectWith($select);
    	$contactMessages = array();
    	foreach ($cursor as $contactMessage) {
    		$contactMessage->properties = $contactMessage->getProperties();
       		$contactMessages[$contactMessage->id] = $contactMessage;
    	}
    	return $contactMessages;
    }

    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
    	$contactMessage = ContactMessage::getTable()->get($id, $column);
    
    	if ($contactMessage) {
    		$contactMessage->properties = $contactMessage->getProperties();
    	}
    
    	return $contactMessage;
    }
    
    public static function instanciate($type = 'email')
    {
    	$message = new ContactMessage;
    	$message->status = 'new';
    	$message->to = array();
    	$message->cc = array();
    	$message->cci = array();
    	return $message;
    }
    
    public function loadData($data) {

    	$context = Context::getCurrent();
    	
    	if (array_key_exists('to', $data)) $this->to = $data['to'];
    	if (array_key_exists('cc', $data)) $this->cc = $data['cc'];
    	if (array_key_exists('cci', $data)) $this->cci = $data['cci'];

    	if (array_key_exists('subject', $data)) {
    		$this->subject = trim(strip_tags($data['subject']));
    		if (!$this->subject || strlen($this->subject) > 65535) return 'Integrity';
    	}

    	if (array_key_exists('from_mail', $data)) {
    		$this->from_mail = trim(strip_tags($data['from_mail']));
    		if (!$this->from_mail || strlen($this->from_mail) > 255) return 'Integrity';
    	}

    	if (array_key_exists('from_name', $data)) {
    		$this->from_name = trim(strip_tags($data['from_name']));
    		if (!$this->from_name || strlen($this->from_name) > 255) return 'Integrity';
    	}
    	 
    	if (array_key_exists('body', $data)) {
	    	$this->body = $data['body'];
    		if (!$this->body) return 'Integrity';
    	}

    	if (array_key_exists('emission_time', $data)) $this->emission_time = $data['emission_time'];
    	 
    	return 'OK';
    }

    public function send()
    {
    	$context = Context::getCurrent();
    	$settings = $context->getConfig();
    
    	if ($settings['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // instance 0 is for demo
    		$text = new MimePart($this->body);
    		$text->type = "text/plain; charset = UTF-8";
    		$body = new MimeMessage();
    		$body->setParts(array($text));
    		 
    		$mail = new Mail\Message();
    		$mail->setEncoding("UTF-8");
    		$mail->setBody($body);
    		$mail->setFrom($this->from_mail, $this->from_name);
    		$mail->setSubject($this->subject);
    
    		foreach ($this->to as $toMail => $toName) $mail->addTo($toMail, $toName);
    		foreach ($this->cc as $ccEmail => $ccName) $mail->addCc($ccEmail, $ccName);
    		if ($settings['mailProtocol'] == 'Smtp') {
    			$transport = new Mail\Transport\Smtp();
    		}
    		elseif ($settings['mailProtocol'] == 'Sendmail') {
    			$transport = new Mail\Transport\SendMail();
    		}
    
    		if ($settings['mailProtocol']) $transport->send($mail);
    
    		if ($settings['isTraceActive']) {
    
    			// Write to the log
    			$writer = new Writer\Stream('data/log/mailing.txt');
    			$logger = new Logger();
    			$logger->addWriter($writer);
    			$logger->info('to: '.explode(', ', $this->to).' - subject: '.$subject.' - body: '.$textContent);
    		}
    	}
    }
    
    function sendHtmlMail()
    {
    	$context = Context::getCurrent();
    	$settings = $context->getConfig();
    	
    	if ($settings['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // instance 0 is for demo
    		$body = $this->body;

    		$text = new MimePart('Le message ci-après s\'affichera correctement sur un navigateur qui supporte le format HTML.');
    		$text->type = "text/plain";
    		$text->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
    		
    		$html = new MimePart($body);
    		$html->type = \Zend\Mime\Mime::TYPE_HTML;
    		$html->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;

    		$body = new MimeMessage();
    		$body->setParts(array($text, $html));
    		
    		$mail = new Mail\Message();
    		$mail->setEncoding("UTF-8");
			$mail->getHeaders()->addHeaderLine('Content-Transfer-Encoding', 'quoted-printable');
			$mail->setBody($body);
    		$mail->setFrom($this->from_mail, $this->from_name);
    		$mail->setSubject($this->subject);
    		
	    	foreach ($this->to as $toMail => $toName) $mail->addTo($toMail, (($toName) ? $toName : $toMail));
	    	foreach ($this->cc as $ccEmail => $ccName) $mail->addCc($ccEmail, (($ccName) ? $ccName : $ccEmail));
	    	foreach ($this->cci as $cciEmail => $cciName) $mail->addBcc($cciEmail, (($cciName) ? $cciName : $cciEmail));
    		if ($settings['mailProtocol'] == 'Smtp') {
    			$transport = new Mail\Transport\Smtp();
    		}
    		elseif ($settings['mailProtocol'] == 'Sendmail') {
    			$transport = new Mail\Transport\SendMail();
    		}

    		if ($settings['mailProtocol']) $transport->send($mail);
    	}
    }
   
    public static function sendMail($email, $textContent, $subject, $cc = null)
    {
    	$context = Context::getCurrent();
		$settings = $context->getConfig();

    	if ($settings['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // instance 0 is for demo
    		$text = new MimePart($textContent);
    		$text->type = "text/plain; charset = UTF-8";
    		$body = new MimeMessage();
    		$body->setParts(array($text));
    			
    		$mail = new Mail\Message();
    		$mail->setEncoding("UTF-8");
    		$mail->setBody($body);
    		$mail->setFrom($settings['mailAdmin'], $settings['nameAdmin']);
    		$mail->setSubject($subject);

    		// Send the mail to a test mailbox if a 'mailTo' setting is set (test environment) otherwise in the given mail (production)
    		if ($settings['mailTo']) $mail->addTo($settings['mailTo'], $settings['mailTo']);
    		else $mail->addTo($email, $email);
    		if ($cc) foreach ($cc as $ccEmail => $ccName) $mail->addCc($ccEmail, ($ccName) ? $ccName : $ccEmail);
    		if ($settings['mailProtocol'] == 'Smtp') {
    			$transport = new Mail\Transport\Smtp();
    		}
    		elseif ($settings['mailProtocol'] == 'Sendmail') {
    			$transport = new Mail\Transport\SendMail();
    		}

    		if ($settings['mailProtocol']) $transport->send($mail);
    
    		if ($settings['isTraceActive']) {
    
    			// Write to the log
    			$writer = new Writer\Stream('data/log/mailing.txt');
    			$logger = new Logger();
    			$logger->addWriter($writer);
    			$logger->info('from: '.$settings['nameAdmin'].' ('.$settings['mailAdmin'].') - to: '.$email.' - subject: '.$subject.' - body: '.$textContent);
    		}
    	}
    }

    public function add()
    {
    	$this->id = null;
		ContactMessage::getTable()->save($this);
		return 'OK';
    }

    public function update($update_time)
    {
    	$contactMessage = ContactMessage::get($this->id);
    	if ($update_time && $contactMessage->update_time > $update_time) return 'Isolation';
    	ContactMessage::getTable()->save($this);
    
    	return ('OK');
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
    	if (!ContactMessage::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		ContactMessage::$table = $sm->get('PpitContact\Model\ContactMessageTable');
    	}
    	return ContactMessage::$table;
    }
}