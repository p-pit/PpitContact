<?php
namespace PpitContact\Model;

use PpitContact\Model\Vcard;
use PpitCore\Model\Context;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ContactMessage implements InputFilterAwareInterface
{
    public $id;
	public $type;
	public $to = array();
	public $cc;
	public $cci;
	public $subject;
	public $from;
	public $body;
    public $emission_time;
    public $volume;
    public $cost;
    public $accepted;
    public $rejected;
    
    // Additional field
    public $update_time;

    // Transient fields
    public $credits;

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

        // Json decode for lists of "to", "cc", "cci", "accepted" and "rejected"
        $this->to = (isset($data['to'])) ? ((json_decode($data['to'])) ? json_decode($data['to']) : array()) : null;
        $this->cc = (isset($data['cc'])) ? ((json_decode($data['cc'])) ? json_decode($data['cc']) : array()) : null;
        $this->cci = (isset($data['cci'])) ? ((json_decode($data['cci'])) ? json_decode($data['cci']) : array()) : null;
        $this->accepted = (isset($data['accepted'])) ? ((json_decode($data['accepted'])) ? json_decode($data['accepted']) : array()) : null;
        $this->rejected = (isset($data['rejected'])) ? ((json_decode($data['rejected'])) ? json_decode($data['rejected']) : array()) : null;
        
        $this->subject = (isset($data['subject'])) ? $data['subject'] : null;
        $this->from = (isset($data['from'])) ? $data['from'] : null;
        $this->body = (isset($data['body'])) ? $data['body'] : null;
        $this->emission_time = (isset($data['emission_time'])) ? $data['emission_time'] : null;
        $this->volume = (isset($data['volume'])) ? $data['volume'] : null;
        $this->cost = (isset($data['cost'])) ? $data['cost'] : null;
        
    	// Additional field
        $this->update_time = (isset($data['updated_time'])) ? $data['updated_time'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['type'] = $this->type;

		// Json encode for lists of "to", "cc", "cci" and "rejected"
    	if ($this->to) {
	    	$data['to'] = json_encode($this->to);
	    	$this->to = json_decode($data['to']);
    	}
    	else $data['to'] = '';

    	if ($this->cc) {
    		$data['cc'] = json_encode($this->cc);
    		$this->cc = json_decode($data['cc']);
    	}
    	else $data['cc'] = '';

    	if ($this->cci) {
    		$data['cci'] = json_encode($this->cci);
    		$this->cci = json_decode($data['cci']);
    	}
    	else $data['cci'] = '';

    	if ($this->accepted) {
    		$data['accepted'] = json_encode($this->accepted);
    		$this->accepted = json_decode($data['accepted']);
    	}
    	else $data['accepted'] = '';
    	 
    	if ($this->rejected) {
    		$data['rejected'] = json_encode($this->rejected);
    		$this->rejected = json_decode($data['rejected']);
    	}
    	else $data['rejected'] = '';
    	 
    	$data['subject'] = $this->subject;
    	$data['from'] = $this->from;
    	$data['body'] = $this->body;
    	$data['emission_time'] = $this->emission_time;
    	$data['volume'] = (int) $this->volume;
    	$data['cost'] = (float) $this->cost;

    	return $data;
    }

    public function loadData($data, $type, $target) {

		// To
		$this->to = $target->to;

    	if ($type != 'SMS') {
			// Cc
    		$this->cc = trim(strip_tags($data['cc']));
	    	if (!$this->cc || strlen($this->cc) > 255) return 400;

			// Cci
	    	$this->cci = trim(strip_tags($data['cci']));
	    	if (!$this->cci || strlen($this->cci) > 255) return 400;

			// Body
	    	$this->body = trim(strip_tags($data['body']));
    	}

		// Subject
    	$this->subject = trim(strip_tags($data['subject']));
    	if (!$this->subject || strlen($this->subject) > 160) return 400;

    	return 200;
    }

    public function loadDataFromRequest($request, $type, $target) {
    	$data = array();
		$return = $target->loadDataFromRequest($request);
		if ($return != 200) return $return;
    	$data['to'] = $request->getPost('to');
    	if ($type != 'SMS') {
	    	$data['cc'] = $request->getPost('cc');
	    	$data['cci'] = $request->getPost('cci');
	    	$data['body'] = $request->getPost('body');
    	}
	    $data['subject'] = $request->getPost('subject');
    	return $this->loadData($data, $type, $target);
    }

    public static function getToList($community_id, $role_id)
    {
    	$select = Vcard::getTable()->getSelect()
	    	->where(array('community_id' => $community_id));
    	$cursor = Vcard::getTable()->selectWith($select);
    	$result = array();
    	foreach($cursor as $vcard) {
    		if (array_search($role, $vcard->roles)) $result[$vcard->email] = $vcard;
    	}
    	return $result;
    }

    public static function sendMail($email, $textContent, $subject, $cc = null)
    {
    	$context = Context::getCurrent();
		$settings = $context->getConfig();

    	if ($context->getInstanceId() != 0) { // instance 0 is for demo
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
    			$logger->info('to: '.$email.' - subject: '.$subject.' - body: '.$textContent);
    		}
    	}
    }

    function sendMultipartMail($htmlBody, $textBody, $subject, $from, $to)
    {
    	$htmlPart = new MimePart($htmlBody);
    	$htmlPart->type = "text/html";
    
    	$textPart = new MimePart($textBody);
    	$textPart->type = "text/plain";
    
    	$body = new MimeMessage();
    	$body->setParts(array($textPart, $htmlPart));
    
    	$message = new MailMessage();
    	$message->setFrom($from);
    	$message->addTo($to);
    	$message->setSubject($subject);
    
    	$message->setEncoding("UTF-8");
    	$message->setBody($body);
    	$message->getHeaders()->get('content-type')->setType('multipart/alternative');
    
    	$transport = new Mail\Transport\Sendmail();
    	$transport->send($message);
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