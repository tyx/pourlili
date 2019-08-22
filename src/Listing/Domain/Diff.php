<?php
declare(strict_types=1);

namespace App\Listing\Domain;

final class Diff
{
    private $additions = [];

    private $deletions = [];

    public function __construct(array $additions, array $deletions)
    {
        $this->additions = $additions;
        $this->deletions = $deletions;
    }

    public static function compute(array $current, array $new)
    {
        dump($current);
        dump($new);
        return new static(
            array_diff($current, $new),
            array_diff($new, $current)
        );
    }

    public function additions(): array
    {
        return $this->additions;
    }

    public function deletions(): array
    {
        return $this->deletions;
    }
}
