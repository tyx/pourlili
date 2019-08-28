<?php
namespace App\Contribution\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class PaymentUrlProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('payment_url')
            ->fromCategory('contribution')
            ->forEachStream()
            ->when([
                PaymentWasSuccessful::class => function ($state, $event) {
                    return $event->paymentUrl();
                }
            ])
            ->run()
        ;
    }
}
