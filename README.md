# SwiftmailerEventsBundle #

## Overview ##

This is a Symfony2 Bundle that adds additional events to SwiftMailer as well as ability to handle additional data on messages.

The bundle makes use of the Symfony2 Event Dispatcher.

## Installation ##

The best way to install the bundle is via Composer.  

1) Go to the ```require``` section of your composer.json file and add

```json
"tdm/swiftmailer-events-bundle": "0.1.*@dev"
```

to the section, along with other packages you require.  Now run ```composer.phar install``` if this is a new installation, or ```composer.phar update``` if you are updating an existing installation.

2) Add SwiftmailerEventsBundle to your application kernel:

```php
<?php

// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new TDM\SwiftMailerEventBundle\TDMSwiftMailerEventBundle(),
        // ...
    );
}
```

3) The bundle is now installed, and you should see no differences in your application performance.  You still need to write some event listeners to do something useful.

## Usage ##

There are two primary features added by the bundle.  Adding additional data to the message object, and triggering events on mailer send as well as imap transport send.

### Additional Data on Messages ###

When a new message is created via ```$container->get('mailer')->createMessage();``` you will receive a class of ```TDM\SwiftMailerEventBundle\Model\Message``` which is an extension of ```\Swift_Message```.
This class has a key-value store added to it which allows for any data to be attached to a message.  An example of this would be the SMTP connection settings.

The following methods are added:
* getAdditionalDataAll() - Returns an array of all additional data that was added.
* getAdditionalData($key) - Retuns the value stored in additional data with a name of $key.  Returns NULL if the $key does not exist.
* setAdditionalData($key, $value) - Sets (or overwrites) the value stored for $key with $value.
* hasAdditionalData($key) - Returns TRUE or FALSE depending on if the data exists.
* removeAdditionalData($key) - Unsets the value in $key.

### Events ###

Events Dispatching was added to the Mailer and SmtpTransport classes.  The events being dispatched are below.
* MailerSendEvent - Is an event of class ```TDM\SwiftMailerEventBundle\Events\MailerSendEvent``` that allows manipulation or reading of the Message Object.  This event object is dispatched for the following event names.
  * ```tdm.swiftmailer.mailer.pre_send_process``` and ```tdm.swiftmailer.mailer.pre_send_cleanup``` which are dispatched immidiately before the ```send``` method is called on the Mailer Object.  Process is dispatched and then cleanup.
  * ```tdm.swiftmailer.mailer.post_send_process``` and ```tdm.swiftmailer.mailer.post_send_cleanup``` which are dispatched immidiately following the ```send``` method on the Mailer Object.  Process is called prior to cleanup.
* TransportSendEvent - Is an event of class ```TDM\SwiftMailerEventBundle\Events\TransportSendEvent``` that allows manipulation or reading of the Message Object or Transport Object.  This event is only dispatched on the SMTP transport for now.  This event object is dispatched for the following event names.
  * ```tdm.swiftmailer.transport.pre_send_process``` and ```tdm.swiftmailer.transport.pre_send_cleanup``` which are dispatched immidiately before the ```send``` method is called on the Transport Object.  Process is dispatched and then cleanup.
  * ```tdm.swiftmailer.transport.post_send_process``` and ```tdm.swiftmailer.transport.post_send_cleanup``` which are dispatched immidiately following the ```send``` method on the Transport Object.  Process is called prior to cleanup.

## Example ##

If we want to allow the system to specify the SMTP server connection information when a message is created we can do so while still making use of the file spooler.  This is useful for SaaS sites that need to allow for white-labeling.

1) We create a listener file that houses all the aspects of our listener.  For our purposes we are going to assume that the variable ```$connectionSettings``` is an array with a bunch of connection settings in it.  This would need to be passed in or derived from a user or a domain in the real world.
```php
<?php

namespace Namespace\Bundle\Listener;

use TDM\SwiftMailerEventBundle\Events\MailerSendEvent;
use TDM\SwiftMailerEventBundle\Events\TransportSendEvent;
use TDM\SwiftMailerEventBundle\Model\Message;
use TDM\SwiftMailerEventBundle\Model\SmtpTransport;

class EmailSendListener {

    private $storedValues = array();

    public function mailerSend(MailerSendEvent $event) {
        $message = $event->getMessage();
        if (!$message instanceof Message)
            return;

        $message->setAdditionalData('AuthMode', $connectionSettings['AuthMode']);
        $message->setAdditionalData('Encryption', $connectionSettings['Encryption']);
        $message->setAdditionalData('Host', $connectionSettings['Host']);
        $message->setAdditionalData('Password', $connectionSettings['Password']);
        $message->setAdditionalData('Port', $connectionSettings['Port']);
        $message->setAdditionalData('UserName', $connectionSettings['UserName']);

        $message->setFrom(array(
            $connectionSettings['FromAddress'] => $connectionSettings['FromName'],
        ));
    }

    public function mailerSendCleanup(MailerSendEvent $event) {
        $message = $event->getMessage();
        if (!$message instanceof Message)
            return;

        //remove any data you don't want to be serialized here.  For our purposes, we don't need to remove anything now.
    }

    public function transportSendPre(TransportSendEvent $event) {
        $message = $event->getMessage();
        if (!$message instanceof Message)
            return;

        $transport = $event->getTransport();
        if (!$transport instanceof SmtpTransport)
            return;

        //make sure all settings are added to message.
        foreach (array('AuthMode', 'Encryption', 'Host', 'Password', 'Port', 'UserName') as $settingName)
            if (!$message->hasAdditionalData($settingName))
                return;

        //clear the stored values
        $this->storedValues = array();

        //make a copy of the existing values
        $this->storedValues['AuthMode'] = $transport->getAuthMode();
        $this->storedValues['Encryption'] = $transport->getEncryption();
        $this->storedValues['Host'] = $transport->getHost();
        $this->storedValues['Password'] = $transport->getPassword();
        $this->storedValues['Port'] = $transport->getPort();
        $this->storedValues['UserName'] = $transport->getUsername();

        //change the values to the settings
        $transport->setAuthMode($message->getAdditionalData('AuthMode'));
        $transport->setEncryption($message->getAdditionalData('Encryption'));
        $transport->setHost($message->getAdditionalData('Host'));
        $transport->setPassword($message->getAdditionalData('Password'));
        $transport->setPort($message->getAdditionalData('Port'));
        $transport->setUsername($message->getAdditionalData('UserName'));

        //stop and start the transport so it uses the new values and connects to new server
        $transport->stop();
        $transport->start();
    }

    public function transportSendPost(TransportSendEvent $event) {
        $transport = $event->getTransport();
        if (!$transport instanceof SmtpTransport)
            return;

        //make sure all settings are available.
        foreach (array('AuthMode', 'Encryption', 'Host', 'Password', 'Port', 'UserName') as $settingName)
            if (!array_key_exists($settingName, $this->storedValues))
                return;

        //reset the transport values
        $transport->setAuthMode($this->storedValues['AuthMode']);
        $transport->setEncryption($this->storedValues['Encryption']);
        $transport->setHost($this->storedValues['Host']);
        $transport->setPassword($this->storedValues['Password']);
        $transport->setPort($this->storedValues['Port']);
        $transport->setUsername($this->storedValues['UserName']);

        //stop and start the transport so it uses the new values and connects to new server
        $transport->stop();
        $transport->start();
    }

}

?>
```

2) We then need to connect our listener methods to the events using some container configuration.

In ```services.yml``` for example.

```yml
services:
  email_send_listener:
    class: Namespace\Bundle\Listener\EmailSendListener
    tags:
      - { name: kernel.event_listener, event: tdm.swiftmailer.mailer.pre_send_process, method: mailerSend }
      - { name: kernel.event_listener, event: tdm.swiftmailer.mailer.pre_send_cleanup, method: mailerSendCleanup }
      - { name: kernel.event_listener, event: tdm.swiftmailer.transport.pre_send_process, method: transportSendPre }
      - { name: kernel.event_listener, event: tdm.swiftmailer.transport.post_send_cleanup, method: transportSendPost }
```

Now when we send a message in the code, the listener adds some additional data to the message.  When the spool if flushed later, the listener checks if the needed additional data is supplied and edits the transport object.  It also stores the original values from the trasport object so that the transport object can be returned to its original state once the single message is sent.


