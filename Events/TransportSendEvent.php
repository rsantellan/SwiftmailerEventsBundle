<?php

namespace TDM\SwiftMailerEventBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use \Swift_Mime_Message;
use \Swift_Transport;

/**
 * Description of TransportSendEvent
 *
 * @author wpigott
 */
class TransportSendEvent extends Event {

    private $message;
    private $transport;

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

    /**
     * 
     * @return Swift_Transport
     */
    public function getTransport() {
        return $this->transport;
    }

    public function setTransport(Swift_Transport $transport) {
        $this->transport = $transport;
    }

}

?>
