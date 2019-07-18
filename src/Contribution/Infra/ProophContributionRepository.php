<?php
declare(strict_types=1);

namespace App\Contribution\Infra;

use App\Contribution\Domain\Contribution;
use App\Contribution\Domain\ContributionRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\Uuid;

class ProophContributionRepository extends AggregateRepository implements ContributionRepository
{
    public function save(Contribution $list): void
    {
        $this->saveAggregateRoot($list);
    }

    public function find(Uuid $id): ?Contribution
    {
        return $this->getAggregateRoot($id->toString());
    }
}
