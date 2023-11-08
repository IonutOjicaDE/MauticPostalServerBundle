<?php

namespace MauticPlugin\MauticPostalServerBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Model\TransportCallback;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class PostalTransport extends \Swift_SmtpTransport implements CallbackTransportInterface
{

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var TransportCallback
     */
    private $transportCallback;

    /**
     * PostalTransport constructor.
     */
    public function __construct(TranslatorInterface $translator, TransportCallback $transportCallback, $host = 'localhost', $port = 25, $security = 'tls', $username = null, $password = null)
    {
        $this->translator        = $translator;
        $this->transportCallback = $transportCallback;

        parent::__construct($host, $port, $security);
        $this->setAuthMode('login');
        $this->setUsername($username);
        $this->setPassword($password);
    }

    /**
     * Returns a "transport" string to match the URL path /mailer/{transport}/callback.
     *
     * @return mixed
     */
    public function getCallbackPath()
    {
        return 'postal';
    }

    /**
     * Handle bounces & complaints from Postal.
     */
    public function processCallbackRequest(Request $request)
    {
        $postData = json_decode($request->getContent(), true);

        $event    = $postData['event'];
        $payload  = $postData['payload'];
        $message  = isset($payload['original_message']) ? $payload['original_message'] : $payload['message'];
        $email    = $message['to'];

        if ('MessageDeliveryFailed' == $event) {
            $this->transportCallback->addFailureByAddress($email, $this->translator->trans('mautic.email.bounce.reason.other'));
        } elseif ('MessageBounced' == $event) {
            $this->transportCallback->addFailureByAddress($email, $this->translator->trans('mautic.email.bounce.reason.hard_bounce'));
        }
    }
}
