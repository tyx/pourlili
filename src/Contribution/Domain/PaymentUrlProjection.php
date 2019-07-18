<?php
namespace App\Contribution\Domain;

use App\SharedKernel\Projection\Projector;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PaymentUrlProjection implements MessageSubscriberInterface
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function handlePaymentUrl(PaymentWasSuccessful $event)
    {
        $this->projector->updateProjection('payment_url', $event, function ($state, $event) {
            return $event->paymentUrl();
        });
    }

    public static function getHandledMessages(): iterable
    {
        yield PaymentWasSuccessful::class => [
            'method' => 'handlePaymentUrl',
            'bus' => 'event.bus',
        ];
    }
}