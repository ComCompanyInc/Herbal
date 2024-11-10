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
    public function getNews(EntityManager $entityManager, Request $request, FormInterface $registrationForm): array
    {
        $news = $entityManager->getRepository(News::class)->findAllWithOrderBy();

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $formData = $registrationForm->getData();

            $news = $entityManager->getRepository(News::class)->findByCity($formData['name']);
        }

        return [
            'news' => $news,
            'registrationForm' => $registrationForm
        ];
    }
}