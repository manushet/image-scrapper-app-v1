<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Form\UrlFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{    
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $form = $this->createForm(UrlFormType::class);

        return $this->render('index.html.twig', [
            'urlForm' => $form->createView(),
        ]);
    }
}