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

    public function get_transaction_by_user_id_sorted($user_id, $sort = 'DESC')
    {
        return $this->createQueryBuilder('transaction')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->addOrderBy('transaction.transaction_time', $sort)
            ->getQuery()
            ->execute();
    }

    public function get_transaction_by_account_id_sorted($user_id, $account_id, $sort = 'DESC')
    {
        if($account_id == 0){
            return $this->createQueryBuilder('transaction')
                ->innerJoin('transaction.account', 'ta')
                ->where('transaction.user = :user_id')
                ->setParameter('user_id', $user_id)
                ->andWhere('transaction.active = 1')
                ->addOrderBy('transaction.transaction_time', $sort)
                ->getQuery()
                ->execute();
        }
        return $this->createQueryBuilder('transaction')
            ->innerJoin('transaction.account', 'ta')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('ta.id = :account_id')
            ->setParameter('account_id', $account_id)
            ->addOrderBy('transaction.transaction_time', $sort)
            ->getQuery()
            ->execute();
    }

    public function get_transaction_by_category_id_sorted($user_id, $category_id, $sort = 'DESC')
    {
        if($category_id == 0){
            return $this->createQueryBuilder('transaction')
                ->innerJoin('transaction.category', 'ta')
                ->where('transaction.user = :user_id')
                ->setParameter('user_id', $user_id)
                ->andWhere('transaction.active = 1')
                ->addOrderBy('transaction.transaction_time', $sort)
                ->getQuery()
                ->execute();
        }
        return $this->createQueryBuilder('transaction')
            ->innerJoin('transaction.category', 'tc')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('tc.id = :category_id')
            ->setParameter('category_id', $category_id)
            ->addOrderBy('transaction.transaction_time', $sort)
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
