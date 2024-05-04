<?php
/*
 * @copyright   2021. All rights reserved
 * @author      Lucas Costa<lucascostacruzsilva@gmail.com>
 *
 */

return [
    'name' => 'Mautic Postal Server Mailer Bundle',
    'description' => 'Integrate Swiftmailer transport for Postal Server',
    'author' => 'Lucas Costa & Alexander Cus & Ionut Ojica',
    'version' => '1.0.0',

    'services' => [
        'other' => [
            'mautic.transport.postal_api' => [
                'class' => \MauticPlugin\MauticPostalServerBundle\Swiftmailer\Transport\PostalApiTransport::class,
                'serviceAlias' => 'swiftmailer.mailer.transport.%s',
                'arguments' => [
                    'mautic.email.model.transport_callback',
                    'mautic.postal.guzzle.client',
                    'translator',
                    '%mautic.mailer_postal_max_batch_limit%',
                    '%mautic.mailer_postal_batch_recipient_count%',
                    '%mautic.mailer_postal_webhook_signing_key%',
                ],
                'methodCalls' => [
                    'setApiKey' => ['%mautic.mailer_api_key%'],
                    'setDomain' => ['%mautic.mailer_host%',],
                    'setRegion' => ['%mautic.mailer_postal_region%'],
                ],
                'tag' => 'mautic.email_transport',
                'tagArguments' => [
                    \Mautic\EmailBundle\Model\TransportType::TRANSPORT_ALIAS => 'mautic.email.config.mailer_transport.postal_api',
                    \Mautic\EmailBundle\Model\TransportType::FIELD_HOST => true,
                    \Mautic\EmailBundle\Model\TransportType::FIELD_API_KEY => true,
                ],
            ],
            'mautic.postal.guzzle.client' => [
                'class' => 'GuzzleHttp\Client',
            ],
        ],
    ],
    'parameters' => [
        'mailer_postal_max_batch_limit' => 4500,
        'mailer_postal_batch_recipient_count' => 1000,
        'mailer_postal_region' => 'us',
/*
 * 1.	Run on the postal Server: postal default-dkim-record
 * 2.	Take the Key between the p= and ;
 * 3.	Paste it into the config mailer_postal_webhook_signing_key below
 */
        'mailer_postal_webhook_signing_key' => '',
    ],
];
