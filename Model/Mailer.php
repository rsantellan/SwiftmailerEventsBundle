<?php

namespace TDM\SwiftMailerEventBundle\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \Swift_Mailer;
use \Swift_Mime_Message;
use TDM\SwiftMailerEventBundle\Events\MailerSendEvent;

/**
 * Description of Mailer
 *
 * @author wpigott
 */
class Mailer extends Swift_Mailer {

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
     * @return MailerSendEvent
     */
    protected function makeMailerSendEvent() {
        return new MailerSendEvent();
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null) {
        //make a new event so message can be modified.
        $event = $this->makeMailerSendEvent();
        $event->setMessage($message);

        //dispatch the pre send events
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.mailer.pre_send_process', $event);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.mailer.pre_send_cleanup', $event);

        //now call the parent function
        $return = parent::send($message, $failedRecipients);

        //dispatch the post send events
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.mailer.post_send_process', $event);
        $this->getEventDispatcher()->dispatch('tdm.swiftmailer.mailer.post_send_cleanup', $event);

        //return the parent functions result
        return $return;
    }

}

?>
