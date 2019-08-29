<?php
namespace App\Contribution\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class PaymentUrlOfContributionHandler
{
    private $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(PaymentUrlOfContribution $query)
    {
        return $this->projector->load('payment_url', $query->contributionId()->toString())->state();
    }
}
