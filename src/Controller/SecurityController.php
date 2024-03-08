<?php

namespace App\Controller;

use App\Service\LogoutSuccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{
    /** Authentication for users that exist in the DB
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get error if the auth isn't successful
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error
        ]);
    }

    /** Users who log out from the system are redirect to the login page and a message appears.
     * @param Request $request
     * @return RedirectResponse
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): RedirectResponse
    {
        $this->addFlash('warning', 'You have logged out!');

        return $this->redirectToRoute('app_login');
    }
}
