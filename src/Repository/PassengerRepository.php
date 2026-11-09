<?php

namespace App\Repository;

use App\Entity\Passenger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Passenger>
 */
class PassengerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Passenger::class);
    }

    public function findByFilters(?string $firstName = null, ?string $lastName = null): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($firstName !== null) {
            $qb->andWhere('p.firstName LIKE :firstName')
                ->setParameter('firstName', '%' . $firstName . '%');
        }

        if ($lastName !== null) {
            $qb->andWhere('p.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $lastName . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
