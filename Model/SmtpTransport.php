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
        //create an event with the transport and message
        $event = $this->makeTransportSendEvent();
        $event->setMessage($message);
        $event->setTransport($this);

        //dispatch the pre send events
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.pre_send_process', $event);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.pre_send_cleanup', $event);

        //call the parent method
        $return = parent::send($message, $failedRecipients);

        //dispatch the post send events
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.post_send_process', $event);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.transport.post_send_cleanup', $event);

        //return the parent method result
        return $return;
    }

}

?>
