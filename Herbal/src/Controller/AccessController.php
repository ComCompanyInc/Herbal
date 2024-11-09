<?php

namespace App\Controller;

/*use App\Entity\Access;
use App\Entity\User;
use App\Form\RegistrationForm;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;*/

use App\Entity\Access;
use App\Entity\Country;
use App\Entity\User;
use App\Form\RegistrationForm;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccessController extends AbstractController
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/registration', name: 'registration')]
    public function registrationAction(Request $request, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $notification = null;

        $registrationForm = $this->createForm(RegistrationForm::class);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $registrationData = $registrationForm->getData();

            $access = new Access();
            $user = new User();

            if (is_null($this->entityManager->getRepository(Access::class)->findOneBy(['email' => $registrationData['email']]))) {
                $access->setEmail($registrationData['email']);
                $access->setPassword($passwordHasher->hashPassword($access, $registrationData['password']));

                // Генерация токена
                $token = $tokenGenerator->generateToken();
                $access->setRegistrationToken($token);

                $this->entityManager->persist($access);
                $this->entityManager->flush();

                //запись пользователя
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

                // Отправка письма с токеном
                $email = (new Email())
                    ->from('mailbox33m@mail.ru')
                    ->to($registrationData['email'])
                    ->subject('Подтверждение регистрации')
                    ->html($this->renderView('emails/registration.html.twig', [
                        'token' => $token,
                    ]));

                $mailer->send($email);

                $notification = "Письмо с подтверждением отправлено на ваш email.";
            } else {
                $notification = "Аккаунт с такой почтой уже существует!";
            }
        }

        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'notification' => $notification,
        ]);
    }

    #[Route('/verify-email/{token}', name: 'verify_email')]
    public function verifyEmail(string $token): Response
    {
        $access = $this->entityManager->getRepository(Access::class)->findOneBy(['registrationToken' => $token]);

        if (!$access) {
            throw $this->createNotFoundException('Неверный токен.');
        }

        // Подтверждение регистрации
        $access->setRegistrationToken(null);
        $access->setIsVerified(true);

        $this->entityManager->flush();

        $this->addFlash('success', 'Ваш email успешно подтвержден.');

        return $this->redirectToRoute('app_login');
    }
}