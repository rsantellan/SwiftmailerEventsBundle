<?php

namespace TDM\SwiftMailerEventBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of OverrideServiceCompilerPass
 *
 * @author wpigott
 */
class OverrideServiceCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $container->setParameter('swiftmailer.class', 'TDM\SwiftMailerEventBundle\Model\Mailer');
        $container->setParameter('swiftmailer.transport.smtp.class', 'TDM\SwiftMailerEventBundle\Model\SmtpTransport');
    }

}

?>
