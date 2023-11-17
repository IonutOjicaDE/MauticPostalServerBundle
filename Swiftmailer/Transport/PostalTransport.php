<?php

namespace MauticPlugin\MauticPostalServerBundle\Swiftmailer\Transport;

use Mautic\EmailBundle\Model\TransportCallback;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Mautic\EmailBundle\Swiftmailer\Transport\CallbackTransportInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @var SignatureKey
     */
    private $webhookSigningKey;

    /**
     * PostalTransport constructor.
     */
    public function __construct(TranslatorInterface $translator, TransportCallback $transportCallback, $host = 'localhost', $port = 25, $security = null, $username = null, $password = null, $webhookSigningKey = null)
    {
        $this->translator        = $translator;
        $this->transportCallback = $transportCallback;
        $this->webhookSigningKey = $webhookSigningKey;

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
     * From: https://github.com/Pacerino/MauticPostalServerBundle
     *
     * as an example for transportCallback->addFailureByAddress see: https://forum.mautic.org/t/webhook-postmark-support-management-of-bounces-spam-and-unsubscription/25799/2?u=ionutojicade
     */
    public function processCallbackRequest(Request $request): void
    {
        if (!$this->verifyCallback($request)) {
            throw new HttpException(400, 'Wrong signature');
        }
        $postData = json_decode($request->getContent(), true);

        $event    = $postData['event'];
        $payload  = $postData['payload'];
        $message  = isset($payload['original_message']) ? $payload['original_message'] : $payload['message'];
        $email    = $message['to'];

        if ('MessageDeliveryFailed' == $event || 'MessageBounced' == $event) {
            $output = ('MessageDeliveryFailed' == $event) 
                ? $this->translator->trans('mautic.email.bounce.reason.other') 
                : $this->translator->trans('mautic.email.bounce.reason.hard_bounce');
            $output .= ": " . $payload['output'] . "[Postal: " . $payload['details'] . "]";
            $this->transportCallback->addFailureByAddress($email, $output, DoNotContact::BOUNCED);
        }
    }

    function verifyCallback(Request $request): bool
    {
        if(empty($this->webhookSigningKey)) {
            return false;
        }

        $rsa_key_pem = "-----BEGIN PUBLIC KEY-----\r\n" .
        chunk_split($this->webhookSigningKey, 64) .
        "-----END PUBLIC KEY-----\r\n";
        $rsa_key = openssl_pkey_get_public($rsa_key_pem) ?: '';

        $signature = '';
        $encodedSignature = $request->headers->get('X-POSTAL-SIGNATURE', '');
        if (is_string($encodedSignature)) {
            $signature = base64_decode($encodedSignature);
        }

        /** @var string $body */
        $body = $request->getContent();

        $result = openssl_verify($body, $signature, $rsa_key, OPENSSL_ALGO_SHA1);

        if ($result !== 1) {
            return false;
        }
        return true;
    }
}
