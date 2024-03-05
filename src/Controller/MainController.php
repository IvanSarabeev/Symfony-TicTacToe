<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiService;
use App\Service\SinglePlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Error;

#[AllowDynamicProperties] class MainController extends AbstractController
{
    private MultiService $multiPlayerRepository;
    private SinglePlayerRepository $singlePlayerRepository;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->multiPlayerRepository = new MultiService($requestStack);
        $this->singlePlayerRepository = new SinglePlayerRepository($requestStack);
    }

    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        return $this->render('views/homepage.html.twig');
    }

    #[Route('/remove-session', name: 'remove-game-session')]
    public function removeGameSession(): Response
    {
        if ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBoard')) {
            $this->singlePlayerRepository->removeGameSession();
        } elseif ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBot')) {
            $this->multiPlayerRepository->removeGameSession();
        } else {
            throw new Error('Session cound\'t be removed');
        }

        return $this->redirectToRoute('app_homepage');
    }
}
