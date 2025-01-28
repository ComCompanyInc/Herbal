<?php

namespace App\Service;

use App\Controller\NewsController;
use App\Entity\News;
use App\Form\NewsForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class NewsService
{
    const AMOUNT_OF_NEWS = 20;

    public function getNews(EntityManager $entityManager, Request $request, FormInterface $registrationForm, int $page = 1): array
    {
        $offset = ($page - 1) * self::AMOUNT_OF_NEWS;

        $news = $entityManager->getRepository(News::class)->findAllWithOrderBy($offset, self::AMOUNT_OF_NEWS);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $formData = $registrationForm->getData();

            $news = $entityManager->getRepository(News::class)->findByCity($formData['name'], $formData['author']);
        }

        return [
            'news' => $news,
            'registrationForm' => $registrationForm
        ];
    }
}