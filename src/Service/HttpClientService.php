<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\AmpHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientService
{
    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
        $this->httpClient = new AmpHttpClient();
    }
    
    private function fetchResponses($urls): array
    {       
        $responses = [];

        foreach ($urls as $url) {            
            $responses[] = $this->httpClient->request('HEAD', $url);
        }

        return $responses;
    }
    
    public function getContentSizes(array $urls): array
    {
        $contentSize = 0;
        $contentQtt = 0;

        $responses = $this->fetchResponses($urls);

        foreach ($this->httpClient->stream($responses) as $response => $chunk) {           
            try {
                if ($chunk->isFirst()) {
                
                    $contentLength = $response->getHeaders()['content-length'][0] ?? 0;
                    
                    $contentQtt += 1;

                    $contentSize += ($contentLength / 1024 /1024);   
                }
            } catch (\Throwable $e) { }
        }

        return [
            'size' => round($contentSize, 2),
            'qtt' => $contentQtt,
        ];
    }
}