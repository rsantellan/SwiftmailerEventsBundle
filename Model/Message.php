<?php

namespace TDM\SwiftMailerEventBundle\Model;

use \Swift_Message;

/**
 * Description of Message
 *
 * @author wpigott
 */
class Message extends Swift_Message {

    private $additionalData = array();

    /**
     * 
     * @return array
     */
    public function getAdditionalData() {
        return $this->additionalData;
    }

    public function setAdditionalData($key, $value) {
        $this->additionalData[$key] = $value;
    }

}

?>
