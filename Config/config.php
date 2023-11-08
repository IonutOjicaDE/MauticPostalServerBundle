<?php
/*
 * @copyright   2021. All rights reserved
 * @author      Lucas Costa<lucascostacruzsilva@gmail.com>
 *
 */

return [
    'name' => 'Mautic Postal Server Mailer Bundle',
    'description' => 'Integrate Swiftmailer transport for Postal Server',
    'author' => 'Lucas Costa & Alexander Cus',
    'version' => '1.0.0',

    'services' => [
        'other' => [
            'mautic.transport.postal' => [
                'class' => \MauticPlugin\MauticPostalServerBundle\Swiftmailer\Transport\PostalTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments'    => [
                    'translator',
                    'mautic.email.model.transport_callback',
                    '%mautic.mailer_host%',
                    '%mautic.mailer_port%',
                    '%mautic.mailer_encryption%',
                    '%mautic.mailer_user%',
                    '%mautic.mailer_password%',
                ],
                'tag' => 'mautic.email_transport',
                'tagArguments' => [
                    \Mautic\EmailBundle\Model\TransportType::TRANSPORT_ALIAS => 'mautic.email.config.mailer_transport.postal',
                    \Mautic\EmailBundle\Model\TransportType::FIELD_HOST   => true,
                    \Mautic\EmailBundle\Model\TransportType::FIELD_USER      => true,
                    \Mautic\EmailBundle\Model\TransportType::FIELD_PASSWORD      => true,
                    \Mautic\EmailBundle\Model\TransportType::FIELD_PORT      => true,
                ],
                'methodCalls'  => [
                    'setUsername' => ['%mautic.mailer_user%'],
                    'setPassword' => ['%mautic.mailer_password%'],
                ],
            ],
        ],
    ],
];
