<?php

namespace TDM\SwiftMailerEventBundle\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \Swift_SmtpTransport;
use \Swift_Mime_Message;
use TDM\SwiftMailerEventBundle\Events\TransportSendEvent;

/**
 * Description of SmtpTransport
 *
 * @author wpigott
 */
class SmtpTransport extends Swift_SmtpTransport {

    private $eventDispatcher;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * 
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher() {
        return $this->eventDispatcher;
    }

    /**
     * 
     * @return TransportSendEvent
     */
    protected function makeTransportSendEvent() {
        return new TransportSendEvent();
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        $event = $this->makeTransportSendEvent();
        $event->setMessage($message);
        $event->setTransport($this);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.pre_send', $event);
        $return = parent::send($message, $failedRecipients);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.post_send', $event);
        return $return;
    }

}

?>
