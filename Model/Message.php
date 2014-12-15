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
    public function getAdditionalDataAll() {
        return $this->additionalData;
    }

    public function getAdditionalData($key) {
        if ($this->hasAdditionalData($key))
            return $this->additionalData[$key];
        return NULL;
    }

    public function setAdditionalData($key, $value) {
        $this->additionalData[$key] = $value;
    }

    public function hasAdditionalData($key) {
        return array_key_exists($key, $this->additionalData);
    }

    public function removeAdditionalData($key) {
        if ($this->hasAdditionalData($key))
            unset($this->additionalData[$key]);
    }

    public function generateId() {
      return rand(0, PHP_INT_MAX);
    }
}

?>
