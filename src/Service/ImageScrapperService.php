<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\DomCrawler\Crawler;
use App\Exception\UnreachableResourceContentException;

class ImageScrapperService
{   
    private array $images = [];

    private string $url;

    private function sanitizeUrl(string $url): string
    {
        return ltrim(rtrim($url, '/\\'), '/\\');
    }

    private function setUrl(string $url): void
    {
        $this->url = $this->sanitizeUrl($url);
    }
    
    private function fetchImageSize($imageUrl): float
    {
        $imageFile = get_headers($imageUrl, true);

        $bytes = $imageFile["Content-Length"];

        return !empty($bytes) ? $bytes/(1024 * 1024) : 0;
    }

    private function handleImages(array $imagePaths): void
    {
        $i = 0;
        foreach ($imagePaths as $imagePath) {

            if ($i >= 5) {
                break;
            }

            $sanitizedImagePath = $this->sanitizeUrl($imagePath);

            $imageUrl = "{$this->url}/{$sanitizedImagePath}";

            $image = new Image($imageUrl);
            
            $image->setSizeMB($this->fetchImageSize($imageUrl));

            $this->images[] = $image;

            $i++;
        } 

        //$imageUrl = 'https://mayak.travel/m/picture/10/36/1240x860.a6463d9b7cabd86ea19d306aeeab8687ea8eaff6f2429a849cb22f7265cc21ec.jpeg';  
    }
    
    public function parse(string $url): void
    {
        
        $this->setUrl($url);
        
        $html = file_get_contents($this->url);

        if ($html === false) {
            throw new UnreachableResourceContentException();
        }

        $imagePaths = (new Crawler($html))->filterXpath('//img')
            ->extract(array('src'));

        $this->handleImages($imagePaths); 
    }

    public function getImages(): array
    {
        return $this->images;
    }
}