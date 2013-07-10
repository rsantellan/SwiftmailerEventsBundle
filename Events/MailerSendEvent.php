<?php

namespace TDM\SwiftMailerEventBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use \Swift_Mime_Message;

/**
 * Description of MailerSendEvent
 *
 * @author wpigott
 */
class MailerSendEvent extends Event {

    private $message;

    /**
     * 
     * @return Swift_Mime_Message
     */
    public function getMessage() {
        return $this->message;
    }

    public function setMessage(Swift_Mime_Message $message) {
        $this->message = $message;
    }

}

?>
