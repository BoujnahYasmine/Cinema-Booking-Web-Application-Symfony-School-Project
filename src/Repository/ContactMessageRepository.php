<?php

namespace App\Repository;

use App\Entity\ContactMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactMessage>
 */
class ContactMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    /**
     * @return ContactMessage[] Returns an array of ContactMessage objects
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('cm')
            ->orderBy('cm.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int Number of unread messages
     */
    public function countUnread(): int
    {
        return $this->createQueryBuilder('cm')
            ->select('count(cm.id)')
            ->where('cm.isRead = :read')
            ->setParameter('read', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

