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


    /** Initialise the services and pass them the requestStack object, as an argument for further usage.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->multiService = new MultiService($requestStack);
        $this->singleService = new SingleService($requestStack);
    }

    /** Protected view for auth user's. If the user logged in correctly a message appears,
     * then redirect them to the homepage, else if they aren't logged in they can't access the homepage.
     * @return Response
     */
    #[Route('/', name: 'app_homepage')]
    public function homePage(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'User tried to access a page without Signing in.');

        $this->addFlash('success', 'Successfully Logged!');

        return $this->render('views/homepage.html.twig');
    }

    /** This method checks if a session is started by corresponding name,
     * if the session name matches then clear the session and redirect the user to the homepage.
     * @return Response
     */
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
