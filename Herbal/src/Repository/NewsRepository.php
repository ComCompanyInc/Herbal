<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    //    /**
    //     * @return News[] Returns an array of News objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?News
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByCity(string $cityName)
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->join('n.content', 'c')
            ->join('c.author', 'a')
            ->join('a.country', 'country')
            ->where('country.name = :cityName')
            ->setParameter('cityName', $cityName)
            ->orderBy('c.dateSending', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithOrderBy(int $offset, int $limit)
    {
        if($offset > $this->getAmountOfPages($limit))
        {
            $offset = $this->getAmountOfPages($limit);
        }

        return $this->createQueryBuilder('n')
            ->select('n')
            ->join('n.content', 'c')
            ->orderBy('c.dateSending', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getAmountOfPages(int $limit): int {
        return ceil(($this->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->getQuery()
            ->getSingleScalarResult()) / ($limit));
    }
}
