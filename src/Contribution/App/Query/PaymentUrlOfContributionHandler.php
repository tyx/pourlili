<?php
namespace App\Contribution\App\Query;

use App\SharedKernel\Projection\Projector;

class PaymentUrlOfContributionHandler
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(PaymentUrlOfContribution $query)
    {
        return $this->projector->load($query->contributionId()->toString(), 'payment_url')->state();
    }
}