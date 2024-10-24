<?php

namespace App\Controller;

use App\Entity\Access;
use App\Entity\Content;
use App\Entity\ContentNews;
use App\Entity\News;
use App\Entity\User;
use App\Form\AddNewsForm;
use App\Form\CommentForm;
use App\Form\NewsForm;
use App\Repository\ContentNewsRepository;
use App\Repository\NewsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class NewsController extends AbstractController
{
    public EntityManagerInterface $entityManager;
    private bool $isAuthored = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/news', name: 'news')]
    public function newsAction(Request $request) :Response
    {
        $news = $this->entityManager->getRepository(News::class)->findAllWithOrderBy();

        $registrationForm = $this->createForm(NewsForm::class);
        $registrationForm->handleRequest($request);

        if($registrationForm->isSubmitted() && $registrationForm->isValid())
        {
            $formData = $registrationForm->getData();

            $news = $this->entityManager->getRepository(News::class)->findByCity($formData['name']);
        }

        return $this->render('news/news.html.twig', [
            'sortForm' => $registrationForm,
            'news' => $news
        ]);
    }

    #[Route('/addNews', name: 'addNews')]
    public function addNewsAction(Request $request): Response
    {
        $news = new News();
        $content = new Content();

        $addNewsForm = $this->createForm(addNewsForm::class);
        $addNewsForm->handleRequest($request);

        $idAccess = null;

        // Проверяем, что пользователь аутентифицирован
        if ($this->getUser() !== null) {
            try {
                $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
                $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);

                $this->isAuthored = true;
            } catch (\Exception $e) {
                $idAccess = null;

                $this->isAuthored = false;
            }
        }

        if ($addNewsForm->isSubmitted() && $addNewsForm->isValid()) {
            $addNewsFormData = $addNewsForm->getData();

            $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);

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

            return $this->redirectToRoute('news');
        }

        return $this->render('news/addNews.html.twig', [
            'addNewsForm' => $addNewsForm,
            'isAuthored' => $this->isAuthored,
        ]);
    }

    #[Route('/comments/{id}', name: 'comments')]
    public function commentsAction(string $id, Request $request): Response
    {

        $content = new Content();
        $contentNews = new ContentNews();

        $commentForm = $this->createForm(CommentForm::class);
        $commentForm->handleRequest($request);

        $comments = $this->entityManager->getRepository(ContentNews::class)->findCommentsByNews(
            $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id])
        );

        /*$idAccess = null;

        try {
            $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);
        } catch (\Exception $e) {
            $idAccess = null;
        }*/

        $idAccess = null;

// Проверяем, что пользователь аутентифицирован
        if ($this->getUser() !== null) {
            try {
                $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
                $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);

                $this->isAuthored = true;
            } catch (\Exception $e) {
                $idAccess = null;

                $this->isAuthored = false;
            }
        }

        if($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $addCommentsFormData = $commentForm->getData();

            $content->setAuthor($idUser);
            $content->setMainText($addCommentsFormData['text']);
            $content->setIsDelete(false);
            $content->setDateSending(DateTime::createFromFormat('dd-mm-YY H:i:s', date('dd-mm-YY H:i:s')));
            $this->entityManager->persist($content);
            $this->entityManager->flush();

            $contentNews->setContent($content);
            $contentNews->setNews($this->entityManager->getRepository(News::class)->findOneBy(['id' => $id]));
            $contentNews->setContent($content);
            $this->entityManager->persist($contentNews);
            $this->entityManager->flush();

            $comments = $this->entityManager->getRepository(ContentNews::class)->findCommentsByNews(
                $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id])
            );
        }

        $newData = $this->entityManager->getRepository(News::class)->find($id);

        return $this->render('news/comments.html.twig', [
            'user' => $idAccess,
            'newData' => $newData,
            'commentForm' => $commentForm,
            'comments' => $comments,
            'isAuthored' => $this->isAuthored
        ]);
    }

    #[Route('/comments/remove/{id}', name: 'removeComment')]
    public function deleteCommentsAction(string $id): JsonResponse
    {
        $this->entityManager->persist($this->entityManager->getRepository(Content::class)->findOneBy(['id' => $id])->setIsDelete(true));
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}