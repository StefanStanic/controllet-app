<?php

namespace App\Repository;

use App\Entity\Budget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use DoctrineExtensions\Query\Mysql\Month;
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

    public function get_total_budget_for_current_month($current_date, $user_id, $account_id)
    {

        $current_month = date('m', strtotime($current_date));

        $db = $this->createQueryBuilder('budget')
            ->select('SUM(budget.budget_amount) as total_budget')
            ->innerJoin('budget.account', 'ac')
            ->where('ac.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('MONTH(budget.budget_date) = :date')
            ->setParameter('date', $current_month)
            ->andWhere('budget.account = :account_id')
            ->setParameter('account_id', $account_id)
            ->andWhere('ac.active = 1');

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_budget_by_category_and_date($current_date, $user_id, $account_id, $category_id)
    {
        $current_month = date('m', strtotime($current_date));

        $db = $this->createQueryBuilder('budget')
            ->select('budget.id as budget_id')
            ->innerJoin('budget.account', 'ac')
            ->where('ac.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('MONTH(budget.budget_date) = :date')
            ->setParameter('date', $current_month)
            ->andWhere('budget.account = :account_id')
            ->setParameter('account_id', $account_id)
            ->andWhere('budget.category = :category_id')
            ->setParameter('category_id', $category_id)
            ->andWhere('ac.active = 1');

        return $db
            ->getQuery()
            ->execute();
    }

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

    // /**
    //  * @return Budget[] Returns an array of Budget objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Budget
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
