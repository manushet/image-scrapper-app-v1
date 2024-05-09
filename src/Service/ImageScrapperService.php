<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use App\Exception\UnreachableResourceContentException;

class ImageScrapperService
{      
    private function sanitizeUrl(string $url): string
    {
        return ltrim(rtrim($url, '/\\'), '/\\');
    }

    public function parse(string $url): array
    {        
        try {
            $html = file_get_contents($url);
        } catch (\Exception $e) {
            throw new UnreachableResourceContentException();
        }

        $imagePaths = (new Crawler($html))->filterXpath('//img')
        ->extract(array('src'));

        $imageUrls = array_map(
            fn ($imagePath) => "{$this->sanitizeUrl($url)}/{$this->sanitizeUrl($imagePath)}", 
            $imagePaths
        );

        return !empty($imageUrls) ? $imageUrls : [];
    }
}