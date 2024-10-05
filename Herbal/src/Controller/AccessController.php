<?php

namespace App\Controller;

use App\Entity\Access;
use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessController extends AbstractController
{

    #[Route('/registration', name: 'registration')]
    function registrationAction(Request $request): Response
    {
        //$access = new Access();

        $registrationForm = $this->createForm(registrationForm::class/*, $access*/);
        $registrationForm->handleRequest($request);

        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $registrationForm
        ]);
    }
}