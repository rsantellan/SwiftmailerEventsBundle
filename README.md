SwiftmailerEventsBundle
=======================

Overview
--------
This is a Symfony2 Bundle that adds additional events to SwiftMailer as well as ability to handle additional data on messages.

The bundle makes use of the Symfony2 Event Dispatcher.

Installation
------------
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



