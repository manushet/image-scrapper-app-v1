<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\AmpHttpClient;

class HttpClientService
{
    public function __construct(
        private readonly AmpHttpClient $httpClient
    )
    {
    }
    
    private function fetchResponses($urls): array
    {       
        $responses = [];

        foreach ($urls as $url) {            
            $responses[] = $this->httpClient->request('HEAD', $url);
        }

        return $responses;
    }
    
    public function getContentSizes(array $urls): float
    {
        $contentSize = 0;

        $responses = $this->fetchResponses($urls);

        foreach ($this->httpClient->stream($responses) as $response => $chunk) {
            if ($chunk->isFirst()) {
                $contentLength = $response->getHeaders()['content-length'][0] ?? 0;

                $contentSize += ($contentLength / 1024 /1024);
            }
        }

        return round($contentSize, 2);
    }
}