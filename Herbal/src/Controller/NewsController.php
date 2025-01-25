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
use App\Service\NewsService;
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
    public $userRole = null; // переменная, хранящая роль пользователя
    private $idAccess = null; // id доступа пользователя
    private $idUser = null; // id пользователя

    private const AMOUNT_OF_COMMENTS  = 30;
    public $routeFragment = '';

    const ACCESS_TYPES = [
      'Пользователь',
      'Администратор'
    ];

    public EntityManagerInterface $entityManager;
    private bool $isAuthored = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'main')]
    public function mainPage(): Response
    {
        return $this->render('main/main.html.twig');
    }

    #[Route('/news/{page}', name: 'news')]
    public function newsAction(Request $request, int $page) :Response
    {
        $this->routeFragment = 'news';

        $currentUserRole = null; // текущая роль пользователя

        $user = $this->getUser();
        $currentVerifyUser = null;

        $newsService = new NewsService();
        $result = [];

        $registrationForm = $this->createForm(NewsForm::class);

        //если у текущего пользователя в БД is_verified == false, то выходим из аккаунта
        if ($user) {
            $currentVerifyUser = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()])->getIsVerified();

            if ($currentVerifyUser == false) {
                return $this->redirectToRoute('app_logout');
            } else {
                $result = $newsService->getNews($this->entityManager, $request, $registrationForm, $page);

                // если пользователь атентифицирован
                if($this->verifiedUser()) {
                    //TODO: Сделать логику для вывода информации о пользователе

//                    if($userRole = self::ACCESS_TYPES[1]) { // если пользователь является адмимнистратором
//
//                    }
                    $currentUserRole = $this->userRole;
                }
            }
        } else {
            $result = $newsService->getNews($this->entityManager, $request, $registrationForm);
        }

        return $this->render('news/news.html.twig', [
            'sortForm' => $result['registrationForm'],//$registrationForm,
            'news' => $result['news'],//$news
            'userRole' => $currentUserRole,
            'ACCESS_TYPES' => self::ACCESS_TYPES,
            'curUser' => $this->idUser,
            'routeFragment' => $this->routeFragment,
            ]);
    }

    #[Route('/addNews/{idNews}', name: 'addNews')]
    public function addNewsAction(Request $request, string $idNews = null): Response
    {
        $news = new News();
        $content = new Content();

        $addNewsForm = $this->createForm(addNewsForm::class);
        $addNewsForm->handleRequest($request);

        //$idAccess = null;

        // Проверяем, что пользователь аутентифицирован
        $this->verifiedUser();
//        if ($this->getUser() !== null) {
//            try {
//                $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
//                $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);
//
//                $this->isAuthored = true;
//            } catch (\Exception $e) {
//                $idAccess = null;
//
//                $this->isAuthored = false;
//            }
//        }

        //заголовок и текст для редактирования новости
        $titleForEdit = "";
        $textForEdit = "";

        if ($idNews != null) {
            $titleForEdit = $this->entityManager->getRepository(News::class)->findOneBy(['content' => $idNews])->getTitle();
            $textForEdit = $this->entityManager->getRepository(News::class)->findOneBy(['content' => $idNews])->getContent()->getMainText();
            $ImageForEdit = $this->entityManager->getRepository(News::class)->findOneBy(['content' => $idNews])->getImageData();
        }

        //если нажата кнопка "Сохранить новость" - сохраняем новость
        if (($addNewsForm->isSubmitted() && $addNewsForm->isValid()) && ($idNews == null || $idNews == "")) {
            $addNewsFormData = $addNewsForm->getData();
            $imgData = $addNewsForm->get('imageData')->getData();

            $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);

            $content->setAuthor($idUser);
            $content->setMainText($addNewsFormData['text']);
            $content->setIsDelete(true);
            $content->setDateSending(DateTime::createFromFormat('dd-mm-YY H:i:s', date('dd-mm-YY H:i:s')));
            $this->entityManager->persist($content);
            $this->entityManager->flush();

            $news->setTitle($addNewsFormData['title']);
            if (isset($imgData)) {
                $news->setImageData(file_get_contents($imgData->getPathname())); //!
            }

            $news->setContent($content);
            $this->entityManager->persist($news);
            $this->entityManager->flush();

            return $this->redirectToRoute('news', ['page' => 1]);
        } else if (($addNewsForm->isSubmitted() && $addNewsForm->isValid()) && ($idNews != null || $idNews != "")) {
            // Редактирование новости
            $addNewsFormData = $addNewsForm->getData();
            $imgData = $addNewsForm->get('imageData')->getData(); // Получаем загруженный файл
        
            $currentNews = $this->entityManager->getRepository(News::class)->findOneBy(['content' => $idNews]);
            $currentNews->setTitle($addNewsFormData['title'] . ' (ред.)');
            $currentNews->getContent()->setMainText($addNewsFormData['text']);
        
            // Если загружено новое фото, обновляем его
            if ($imgData) {
                $currentNews->setImageData(file_get_contents($imgData->getPathname()));
            }
        
            $this->entityManager->persist($currentNews);
            $this->entityManager->flush();
        
            return $this->redirectToRoute('comments', ['id' => $this->entityManager->getRepository(News::class)->findOneBy(['content' => $idNews])->getId(), 'page' => 1]);
        }
        
        return $this->render('news/addNews.html.twig', [
            'addNewsForm' => $addNewsForm,
            'isAuthored' => $this->isAuthored,
            'idNews' => $idNews,
            'titleForEdit' => $titleForEdit,
            'textForEdit' => $textForEdit,
            'imageForEdit' => $ImageForEdit,
        ]);
    }

    #[Route('/comments/{id}/{page}', name: 'comments')]
    public function commentsAction(string $id, int $page, Request $request): Response
    {
        $this->routeFragment = 'comments';

        $content = new Content();
        $contentNews = new ContentNews();

        $commentForm = $this->createForm(CommentForm::class);
        $commentForm->handleRequest($request);

        $comments = $this->entityManager->getRepository(ContentNews::class)->findCommentsByNews(
            $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id]), self::AMOUNT_OF_COMMENTS, $page
        );

        /*$idAccess = null;

        try {
            $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
            $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);
        } catch (\Exception $e) {
            $idAccess = null;
        }*/

        $idAccess = null;

//// Проверяем, что пользователь аутентифицирован
        $this->verifiedUser();
//        if ($this->getUser() !== null) {
//            try {
//                $idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
//                $idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $idAccess]);
//
//                $this->userRole = $idUser->getAccess()->getRole()->getType(); //берем тип роли пользователя
//
//                $this->isAuthored = true;
//            } catch (\Exception $e) {
//                $idAccess = null;
//
//                $this->isAuthored = false;
//            }
//        }

        if($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $addCommentsFormData = $commentForm->getData();

            $content->setAuthor($this->idUser);
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
                $this->entityManager->getRepository(News::class)->findOneBy(['id' => $id]), self::AMOUNT_OF_COMMENTS, $page
            );
        }

        $newData = $this->entityManager->getRepository(News::class)->find($id);

        $imageData = null;
        $finfo = null;
        $mimeType = null;

        if ($newData->getImageData() != null) {
            $imageData = stream_get_contents($newData->getImageData());

            // Определяем MIME-тип
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageData);
        }

        //dd($this->userRole);

        return $this->render('news/comments.html.twig', [
            'user' => $this->idAccess,
            'newData' => $newData,
            'commentForm' => $commentForm,
            'comments' => $comments,
            'isAuthored' => $this->isAuthored,
            'userRole' => $this->userRole,
            'ACCESS_TYPES' => self::ACCESS_TYPES,
            'curUser' => $this->entityManager->getRepository(News::class)->findOneBy(['id' => $newData])->getContent()->getAuthor(),//$this->idUser,
            'routeFragment' => $this->routeFragment,
            'imageData' => base64_encode($imageData),
            'mimeType' => $mimeType
        ]);
    }

    #[Route('/remove_comments/{id}', name: 'removeComment')]
    public function deleteCommentsAction(string $id): JsonResponse
    {
        $this->entityManager->persist($this->entityManager->getRepository(Content::class)->findOneBy(['id' => $id])->setIsDelete(true));
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/publishing/{id}', name: 'publishing')]
    public function publishingNewsAction(string $id): Response
    {
        $this->entityManager->persist($this->entityManager->getRepository(Content::class)->findOneBy(['id' => $id])->setIsDelete(false));
        $this->entityManager->flush();

        return $this->redirectToRoute('news/1');
    }

    //функция с проверкой на аутентификацию пользователя
    public function verifiedUser(): bool
    {
        // Проверяем, что пользователь аутентифицирован
        if ($this->getUser() !== null) {
            try {
                $this->idAccess = $this->entityManager->getRepository(Access::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
                $this->idUser = $this->entityManager->getRepository(User::class)->findOneBy(['access' => $this->idAccess]);

                $this->userRole = $this->idUser->getAccess()->getRole()->getType(); //берем тип роли пользователя

                $this->isAuthored = true;
            } catch (\Exception $e) {
                $idAccess = null;

                $this->isAuthored = false;
            }
        }

        return $this->isAuthored;
    }
}