<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

interface ImageUploader
{
    public function upload(string $path, string $name, int $maxHeight);
}
