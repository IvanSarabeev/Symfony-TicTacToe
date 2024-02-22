<?php

namespace App\Controller;

use App\Service\MultiPlayer;
use App\Service\SinglePlayer;
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
    #[Route('/single', name:'app_singlepage', methods: ["GET"])]
    public function singlePlayerPage(SinglePlayer $singlePlayer): Response
    {
        $selectedCell = $_POST['cell'];

        if (is_array($selectedCell)) {
            $rowKeys = array_keys($selectedCell);
            $row = array_shift($rowKeys);

            $cellKeys = array_keys($_POST['cell'][$row]);
            $col = array_shift($cellKeys);

        }

        return $this->render('views/single-player.html.twig');
    }

    // !TODO: Combine the services to work together
    #[Route('/multi', name:'app_multipage', methods: ["GET"])]
    public function multiPlayerPage(MultiPlayer $multiPlayer): Response
    {
        return $this->render('views/multi-player.html.twig');
    }
}