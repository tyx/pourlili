<?php
namespace App\Contribution\App\Command;

use App\Contribution\Domain\Contribution;
use App\Contribution\Domain\ContributionRepository;
use App\Contribution\Domain\PaymentGateway;

class ContributeToListHandler
{
    private $paymentGateway;

    private $contributionRepository;

    public function __construct(ContributionRepository $contributionRepository, PaymentGateway $paymentGateway)
    {
        $this->contributionRepository = $contributionRepository;
        $this->paymentGateway = $paymentGateway;
    }

    public function __invoke(ContributeToList $command)
    {
        $contribution = Contribution::checkout(
            $command->contributionId(),
            $command->listId(),
            $command->basketId(),
            $command->amount(),
            $command->email()
        );
        $contribution->pay($this->paymentGateway);
        $contribution->sign($command->signature(), $command->message());

        $this->contributionRepository->save($contribution);
    }
}