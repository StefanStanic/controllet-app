<?php

namespace App\Repository;

use App\Entity\Budget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Budget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Budget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Budget[]    findAll()
 * @method Budget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    /**
     * @param $user_id
     * @return ArrayCollection
     */
    public function get_budgets_by_user_id($user_id)
    {
        $db = $this->createQueryBuilder('budget')
            ->select('budget, ac')
            ->innerJoin('budget.account', 'ac')
            ->where('ac.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('ac.active = 1')
            ->andWhere('budget.active = 1');
        $result =  $db
            ->getQuery()
            ->getResult();
        return new ArrayCollection($result);
    }
}
