<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Content;
use App\Entity\News;
use App\Entity\User;
use App\Form\AddNewsForm;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class NewsController extends AbstractController
{
    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/news', name: 'news')]
    public function newsAction() :Response
    {
        return $this->render('news/news.html.twig');
    }

    #[Route('/addNews', name: 'addNews')]
    public function addNewsAction(Request $request): Response
    {
        //dump($this->getUser()->getId());

        $news = new News();
        $content = new Content();

        $addNewsForm = $this->createForm(addNewsForm::class);
        $addNewsForm->handleRequest($request);

        if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {
            $addNewsFormData = $addNewsForm->getData();

            $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);
            //dd($idUser);

            $content->setAuthor($idUser);
            $content->setMainText($addNewsFormData['text']);
            $content->setIsDelete(false);
            $content->setDateSending(DateTime::createFromFormat('dd-mm-YY H:i:s', date('dd-mm-YY H:i:s')));
            $this->entityManager->persist($content);
            $this->entityManager->flush();

            $news->setTitle($addNewsFormData['title']);
            $news->setContent($content);
            $this->entityManager->persist($news);
            $this->entityManager->flush();
        }

        return $this->render('news/addNews.html.twig', [
            'addNewsForm' => $addNewsForm,
        ]);
    }
}