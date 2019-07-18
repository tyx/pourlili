<?php
namespace App\Contribution\App\Command;

use App\Contribution\Domain\ContributionRepository;

class ConfirmContributionHandler
{
    private $contributionRepository;

    public function __construct(ContributionRepository $contributionRepository)
    {
        $this->contributionRepository = $contributionRepository;
    }

    public function __invoke(ConfirmContribution $command)
    {
        $contribution = $this->contributionRepository->find($command->contributionId());

        if (null === $contribution) {
            throw new \LogicException("Cannot confirm unknown contribution. Id {$command->contributionId()}");
        }

        $contribution->confirm();
        $this->contributionRepository->save($contribution);
    }
}