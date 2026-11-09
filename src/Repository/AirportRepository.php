<?php

namespace App\Repository;

use App\Entity\Airport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Airport>
 */
class AirportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Airport::class);
    }

    public function findByFilters(?string $code = null, ?string $name = null): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($code !== null) {
            $qb->andWhere('a.code = :code')
                ->setParameter('code', $code);
        }

        if ($name !== null) {
            $qb->andWhere('a.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
