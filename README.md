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

If we want to allow 
