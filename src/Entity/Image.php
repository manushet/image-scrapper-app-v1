<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Image
{
    private float $sizeMB;

    public function __construct(private string $url)
    {
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getSizeMB(): ?float
    {
        return $this->sizeMB;
    }

    public function setSizeMB(float $sizeMB): self
    {
        $this->sizeMB = $sizeMB;

        return $this;
    }
}