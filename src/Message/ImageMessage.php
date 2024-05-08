<?php

declare(strict_types=1);

namespace App\Message;

class ImageMessage
{
    public function __construct(
        private string $url
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}