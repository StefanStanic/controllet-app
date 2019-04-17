<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function get_transaction_by_filters($user_id, $account_id, $category_id, $sort = 'DESC')
    {

        $db = $this->createQueryBuilder('transaction')
                        ->where('transaction.user = :user_id')
                        ->setParameter('user_id', $user_id)
                        ->andWhere('transaction.active = 1')
                        ->addOrderBy('transaction.transaction_time', $sort);

        if($account_id != 0){
            $db
                ->innerJoin('transaction.account', 'ta')
                ->andWhere('ta.id = :account_id')
                ->setParameter('account_id', $account_id);
        }
        if($category_id !=0){
            $db
                ->innerJoin('transaction.category', 'tc')
                ->andWhere('tc.id = :category_id')
                ->setParameter('category_id', $category_id);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_total_expenses_by_filters($user_id, $account_id, $category_id)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('SUM(transaction.transaction_amount) as total_expenses')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('tt.id = 1');

        if($account_id != 0){
            $db
                ->innerJoin('transaction.account', 'ta')
                ->andWhere('ta.id = :account_id')
                ->setParameter('account_id', $account_id);
        }
        if($category_id !=0){
            $db
                ->innerJoin('transaction.category', 'tc')
                ->andWhere('tc.id = :category_id')
                ->setParameter('category_id', $category_id);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_total_income_by_filters($user_id, $account_id, $category_id)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('SUM(transaction.transaction_amount) as total_income')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('tt.id = 2');

        if($account_id != 0){
            $db
                ->innerJoin('transaction.account', 'ta')
                ->andWhere('ta.id = :account_id')
                ->setParameter('account_id', $account_id);
        }
        if($category_id !=0){
            $db
                ->innerJoin('transaction.category', 'tc')
                ->andWhere('tc.id = :category_id')
                ->setParameter('category_id', $category_id);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
