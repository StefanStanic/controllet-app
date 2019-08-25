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
}
