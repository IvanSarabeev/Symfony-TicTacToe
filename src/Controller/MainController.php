<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        return $this->render('views/homepage.html.twig');
    }

    // !TODO: Combine the services to work together
    #[Route('/single', name:'app_singlepage')]
    public function singlePlayerPage(): Response
    {
        return $this->render('views/single-player.html.twig');
    }

    // !TODO: Combine the services to work together
    #[Route('/multi', name:'app_multipage')]
    public function multiPlayerPage(): Response
    {
        return $this->render('views/multi-player.html.twig');
    }
}