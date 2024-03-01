<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SignUpController extends AbstractController
{
    #[Route('/signup', name: 'app_sign_page')]
    public function signUpPage(): Response
    {


        return $this->render('signup/signup.html.twig', [

        ]);
    }
}