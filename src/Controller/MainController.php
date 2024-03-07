<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\MultiService;
use App\Service\SingleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Error;

#[AllowDynamicProperties] class MainController extends AbstractController
{
    private MultiService $multiService;
    private SingleService $singleService;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->multiService = new MultiService($requestStack);
        $this->singleService = new SingleService($requestStack);
    }

    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        $this->addFlash('success', 'Successfully Logged!');

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without Signing in.');

        return $this->render('views/homepage.html.twig');
    }

    #[Route('/remove-session', name: 'remove-game-session')]
    public function removeGameSession(): Response
    {
        if ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBoard')) {
            $this->singleService->removeGameSession();
        } elseif ($this->requestStack->getSession()->isStarted() && $this->requestStack->getSession()->has('gameBot')) {
            $this->multiService->removeGameSession();
        } else {
            throw new Error('Session cound\'t be removed');
        }

        return $this->redirectToRoute('app_homepage');
    }
}
