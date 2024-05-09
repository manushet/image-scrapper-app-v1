<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Form\UrlFormType;
use App\Service\HttpClientService;
use App\Service\ImageScrapperService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ResultsController extends AbstractController
{
    public function __construct(
        private readonly ImageScrapperService $imageScrapper,
        private readonly HttpClientService $httpClientService
    )
    {
    }  
    
    #[Route('/results', name: 'results', methods: ['GET', 'POST'])]
    public function showResults(Request $request): Response
    {
        $form = $this->createForm(UrlFormType::class);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $url = $form->getData()['url'];

            $imageUrls = $this->imageScrapper->parse($url);

            $imagesResults = $this->httpClientService->getContentSizes($imageUrls);
            
            return $this->render('images.html.twig', [
                'url'=> $url,                
                'images' => $imageUrls,
                'imagesQtt' => $imagesResults['qtt'],
                'imagesSize' => $imagesResults['size'],
            ]);
        }

        return $this->redirectToRoute('index');
    }
}