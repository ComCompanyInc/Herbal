<?php

namespace App\Repository;

use App\Entity\ContentNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContentNews>
 */
class ContentNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentNews::class);
    }

    //    /**
    //     * @return ContentNews[] Returns an array of ContentNews objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ContentNews
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findCommentsByNews($idNews, int $limit, int $page = 1)
    {
        $offset = ($page - 1) * $limit;

        if($offset > $this->getAmountOfPages($limit))
        {
            $offset = $this->getAmountOfPages($limit);
        }

        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.content', 'cont')
            ->join('cont.author', 'author')
            ->join('c.news', 'new')
            ->join('new.content', 'newCont')
            ->where('(c.news = :idNews) AND (cont.isDelete = false)')
            ->setParameter('idNews', $idNews)
            ->orderBy('cont.dateSending', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getAmountOfPages(int $limit): int {
        return ceil(($this->createQueryBuilder('c')
                ->select('COUNT(c)')
                ->getQuery()
                ->getSingleScalarResult()) / ($limit));
    }
}
