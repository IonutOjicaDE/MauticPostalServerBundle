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
                    'monolog.logger.mautic',
                    'mautic.email.model.transport_callback',
                    '%mautic.mailer_host%',
                    '%mautic.mailer_port%',
                    '%mautic.mailer_encryption%',
                ],
                'methodCalls'  => [
                    'setUsername' => ['%mautic.mailer_user%'],
                    'setPassword' => ['%mautic.mailer_password%'],
                ],
            ],
        ],
    ],
];
