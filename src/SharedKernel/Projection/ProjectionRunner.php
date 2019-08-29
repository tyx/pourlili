<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

interface ProjectionRunner
{
    public function __invoke(Projector $projector);
}
