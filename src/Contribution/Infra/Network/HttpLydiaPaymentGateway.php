<?php

namespace App\Contribution\Infra\Network;

use App\Contribution\Domain\PaymentError;
use App\Contribution\Domain\PaymentGateway;
use App\Contribution\Domain\PaymentResult;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpLydiaPaymentGateway implements PaymentGateway
{
    const PROD_URL = 'https://lydia-app.com/api/request/do.json';

    const STAGING_URL = 'https://homologation.lydia-app.com/api/request/do.json';

    private $httpClient;

    private $router;

    private $phone;

    private $debug;

    public function __construct(
        HttpClientInterface $httpClient, 
        UrlGeneratorInterface $router,
        LoggerInterface $logger,
        $phone = null, 
        $debug = false
    ) {
        $this->httpClient = $httpClient;
        $this->router = $router;
        $this->logger = $logger;
        $this->phone = $phone;
        $this->debug = $debug;
    }

    public function checkout(string $contributionId, float $amount, string $recipient)
    {
        try {
            $payload = $this->httpClient
                ->request(
                    'POST',
                    $this->debug ? self::STAGING_URL : self::PROD_URL,
                    [
                        'body' => [
                            'phone' => $this->phone,
                            'recipient' => $recipient,
                            'type' => 'email',
                            'amount' => $amount,
                            'currency' => 'EUR',
                            'order_ref' => $contributionId.time(),
                            'threeDSecure' => 'no',
                            'confirm_url' => $this->router->generate(
                                'contribution_confirm', 
                                ['contributionId' => base64_encode($contributionId)], 
                                UrlGeneratorInterface::ABSOLUTE_URL
                            ),
                            'browser_success_url' => $this->router->generate('contribution_thanks', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        ]
                    ]
                )
                ->toArray()
            ;

            // "{"error":"0","request_id":"32369","request_uuid":"adc84c2195d86d95c523cfd8a635ac54","message":"Votre demande a \u00e9t\u00e9 envoy\u00e9e. Vous receverez une confirmation par email d\u00e8s que le destinataire l'aura accept\u00e9e.","order_ref":"da7ad5a6-79b6-4425-8cf3-335e0dd529a71565637429","mobile_url":"https:\/\/homologation.lydia-app.com\/collect\/payment\/adc84c2195d86d95c523cfd8a635ac54\/auto"} ◀"

            return new PaymentResult(
                $payload['mobile_url'] ?? '',
                '0' === ($payload['error'] ?? '0'),
                $payload
            );
        } catch (HttpExceptionInterface $exception) {
            $this->logger->error($exception->getMessage(), ['contribution_id' => $contributionId, 'amount' => $amount, 'recipient' => $recipient]);

            throw PaymentError::transport($exception);
        }
    }
}
