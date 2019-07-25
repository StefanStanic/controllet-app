<?php

namespace App\Repository;

use App\Entity\ActivationKeys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActivationKeys|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivationKeys|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivationKeys[]    findAll()
 * @method ActivationKeys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivationKeysRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivationKeys::class);
    }

    // /**
    //  * @return ActivationKeys[] Returns an array of ActivationKeys objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivationKeys
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
