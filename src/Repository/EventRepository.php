<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
    public function findNotValidated(): array
    {
        return $this->findBy(['validated' => false], ['date' => 'ASC']);
    }
    public function findUpcomingValidated(?string $category = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.validated = :validated')
            ->andWhere('e.date >= :now')
            ->setParameter('validated', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.date', 'ASC');

        if ($category) {
            $qb->andWhere('e.category = :category')
            ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
