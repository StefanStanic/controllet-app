<?php

namespace App\Repository;

use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TransactionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionType[]    findAll()
 * @method TransactionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TransactionType::class);
    }
}
