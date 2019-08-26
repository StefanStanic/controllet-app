<?php

namespace App\Repository;

use App\Entity\Bills;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bills|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bills|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bills[]    findAll()
 * @method Bills[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bills::class);
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @param $subcategory_id
     * @param string $sort
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_bills_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort = 'DESC', $start_date, $end_date)
    {

        $db = $this->createQueryBuilder('bills')
            ->select('bills, ac')
            ->innerJoin('bills.account', 'ac')
            ->where('ac.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('bills.active = 1')
            ->addOrderBy('bills.date_added', $sort);

        if($account_id != 0){
            $db
                ->innerJoin('bills.account', 'ta')
                ->andWhere('ta.id = :account_id')
                ->setParameter('account_id', $account_id);
        }
        if($category_id !=0){
            $db
                ->innerJoin('bills.category', 'tc')
                ->andWhere('tc.id = :category_id')
                ->setParameter('category_id', $category_id);
        }
        if($category_id !=0){
            $db
                ->innerJoin('bills.subcategory', 'ts')
                ->andWhere('ts.id = :subcategory_id')
                ->setParameter('subcategory_id', $subcategory_id);
        }
        if(!empty($start_date)){
            //convert date to understandable format
            $start_date = \DateTime::createFromFormat("d-m-Y", $start_date);
            $start_date = $start_date->format('Y-m-d');

            $db
                ->andWhere('bills.date_due >= :datefrom')
                ->setParameter('datefrom', $start_date);
        }
        if(!empty($end_date)){
            //convert date to understandable format
            $end_date = \DateTime::createFromFormat("d-m-Y", $end_date);
            $end_date = $end_date->format('Y-m-d');

            $db
                ->andWhere('bills.date_due <= :dateto')
                ->setParameter('dateto', $end_date);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_bill_count()
    {
        $db = $this->createQueryBuilder('bills')
            ->select('COUNT(bills) as bill_count')
            ->where('bills.active = 1');

        return $db
            ->getQuery()
            ->execute();
    }
}
