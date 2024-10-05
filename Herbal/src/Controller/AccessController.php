<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\User;
use App\Form\RegistrationForm;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccessController extends AbstractController
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/registration', name: 'registration')]
    function registrationAction(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;

        $registrationForm = $this->createForm(registrationForm::class);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $registrationData = $registrationForm->getData();

            $access = new Access();
            $user = new User();

            //dd($registrationData['email']);

            if(
                is_null($this->entityManager->getRepository(Access::class)->findOneBy(
                        ['email' => $registrationData['email']]
                    )
                )
            ) {
                $access->setEmail($registrationData['email']);
                $access->setPassword($passwordHasher->hashPassword(new Access(), $registrationData['password']));
                $this->entityManager->persist($access);
                $this->entityManager->flush();

                $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(
                    ['email' => $registrationData['email']]
                );

                $user->setAccess($idAccess);
                $user->setCountry($registrationData['country']);
                $user->setName($registrationData['name']);
                $user->setSurname($registrationData['surname']);
                $user->setPatronumic($registrationData['patronymic']);
                $user->setDateOfBirth($registrationData['dateOfBirth']);
                $user->setDateOfRegistration(DateTime::createFromFormat('dd-mm-YY', date('dd-mm-YY')));
                $user->setIsBlocked(false);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $notification = "Вы успешно зарегестрированны!";
            } else {
                //TODO: генерируем сообщение что нельзя создать аккаунт с такой почтой
                return new Response('аккаунт с такой почтой уже существует!');
            }
        }

        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $registrationForm,
            'notification' => $notification,
        ]);
    }
}