<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findTrending(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.isTrending = :trending')
            ->setParameter('trending', true)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Movie[] Returns an array of Movie objects
     */
    public function findComingSoon(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.isComingSoon = :comingSoon')
            ->setParameter('comingSoon', true)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

