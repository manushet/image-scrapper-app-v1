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

    private function getHtmlContent(string $url): string
    {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            return curl_exec($ch);
        } catch (\Exception $e) {
            throw new UnreachableResourceContentException();
        }        
    }

    public function parse(string $url): array
    {        
        $html = $this->getHtmlContent($url);

        $imagePaths = (new Crawler($html))->filterXpath('//img')
        ->extract(array('src'));

        $imageUrls = [];

        foreach ($imagePaths as $imagePath) {           
            if (!str_contains($imagePath, "http")) {   
                $imagePath = "{$this->sanitizeUrl($url)}/{$this->sanitizeUrl($imagePath)}";
            }

            if (!empty($imagePath) && ($imagePath !== $url)) {
                $imageExtensions = array('webp', 'svg', 'jpeg', 'jpg', 'gif', 'png');
                
                if (in_array(strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)), $imageExtensions)) {
                    $imageUrls[] = $imagePath;
                }
            }
        }

        return $imageUrls;
    }
}