<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Service\DashboardService;
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
    private $dashboardService;

    public function __construct(RegistryInterface $registry, DashboardService $dashboardService)
    {
        parent::__construct($registry, Transaction::class);
        $this->dashboardService = $dashboardService;
    }


    /**
     * @param $year
     * @param $month
     * @param $account
     * @param $category
     * @return mixed
     */
    public function get_total_expenses_by_year_month_account($year, $month, $account, $category)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('COALESCE(SUM(transaction.transaction_amount), 0) as total_expenses')
            ->where('transaction.account = :account')
            ->setParameter('account', $account)
            ->andWhere('YEAR(transaction.transaction_time) = :year')
            ->setParameter('year', $year)
            ->andWhere('transaction.category = :category')
            ->setParameter('category', $category)
            ->andWhere('MONTH(transaction.transaction_time) = :month')
            ->setParameter('month', $month)
            ->andWhere('transaction.active = 1');

        return $db
            ->getQuery()
            ->execute();
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
    public function get_transaction_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort = 'DESC', $start_date, $end_date)
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
        if($category_id !=0){
            $db
                ->innerJoin('transaction.subCategory', 'ts')
                ->andWhere('ts.id = :subcategory_id')
                ->setParameter('subcategory_id', $subcategory_id);
        }
        if(!empty($start_date)){
            //convert date to understandable format
            $start_date = \DateTime::createFromFormat("d-m-Y", $start_date);
            $start_date = $start_date->format('Y-m-d').' 00:00:00';

            $db
                ->andWhere('transaction.transaction_time >= :datefrom')
                ->setParameter('datefrom', $start_date);
        }
        if(!empty($end_date)){
            //convert date to understandable format
            $end_date = \DateTime::createFromFormat("d-m-Y", $end_date);
            $end_date = $end_date->format('Y-m-d'). ' 23:59:59';

            $db
                ->andWhere('transaction.transaction_time <= :dateto')
                ->setParameter('dateto', $end_date);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @return mixed
     */
    public function get_total_expenses_by_filters($user_id, $account_id, $category_id, $default_currency)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('transaction.transaction_amount as total_expenses, currency.currencyLabel')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->innerJoin('transaction.account', 'account')
            ->innerJoin('account.currency', 'currency')
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

        $results = $db
            ->getQuery()
            ->execute();

        $total_expense = 0;

        foreach($results as $transaction){
            if($transaction['currencyLabel'] != $default_currency){
                $total_expense+=(int)$this->dashboardService->convert_currency($transaction['currencyLabel'], $default_currency, $transaction['total_expenses']);
            } else {
                $total_expense+=(int)$transaction['total_expenses'];
            }
        }

        $return = [
            ['total_expenses' => $total_expense]
        ];

        return $return;
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @return mixed
     */
    public function get_total_income_by_filters($user_id, $account_id, $category_id, $default_currency)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('transaction.transaction_amount as total_income, currency.currencyLabel')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->innerJoin('transaction.account', 'account')
            ->innerJoin('account.currency', 'currency')
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

        $results = $db
            ->getQuery()
            ->execute();

        $total_income = 0;

        foreach($results as $transaction){
            if($transaction['currencyLabel'] != $default_currency){
                $total_income+=(int)$this->dashboardService->convert_currency($transaction['currencyLabel'], $default_currency, $transaction['total_income']);
            } else {
                $total_income+=(int)$transaction['total_income'];
            }
        }

        $return = [
            ['total_income' => $total_income]
        ];

        return $return;
    }

    /**
     * @param $user_id
     * @param $data_type
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $db = $this->createQueryBuilder('transaction')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('tt.id = :data_type')
            ->setParameter('data_type', $data_type)
            ->groupBy('transaction_month')
            ->groupBy('transaction_day');

        if($data_type = 1){
            $db->select("SUM(transaction.transaction_amount) as total_daily_expense, YEAR(transaction.transaction_time) as transaction_year, DATE_FORMAT(transaction.transaction_time, '%m') as transaction_month, DATE_FORMAT(transaction.transaction_time, '%d') as transaction_day");
        }else{
            $db->select("SUM(transaction.transaction_amount) as total_daily_income, YEAR(transaction.transaction_time) as transaction_year, DATE_FORMAT(transaction.transaction_time, '%m') as transaction_month, DATE_FORMAT(transaction.transaction_time, '%d') as transaction_day");
        }

        if(!empty($start_date)){
            //convert date to understandable format
            $start_date = \DateTime::createFromFormat("d-m-Y", $start_date);
            $start_date = $start_date->format('Y-m-d').' 00:00:00';

            $db
                ->andWhere('transaction.transaction_time >= :datefrom')
                ->setParameter('datefrom', $start_date);
        }

        if(!empty($end_date)){
            //convert date to understandable format
            $end_date = \DateTime::createFromFormat("d-m-Y", $end_date);
            $end_date = $end_date->format('Y-m-d'). ' 23:59:59';

            $db
                ->andWhere('transaction.transaction_time <= :dateto')
                ->setParameter('dateto', $end_date);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    /**
     * @param $user_id
     * @param $data_type
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $db = $this->createQueryBuilder('transaction')
            ->innerJoin('transaction.category', 'cc')
            ->innerJoin('transaction.subCategory', 'sc')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->where('transaction.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->andWhere('transaction.active = 1')
            ->andWhere('tt.id = :data_type')
            ->setParameter('data_type', $data_type)
            ->groupBy('sc.subCategoryName');

            $db->select("sc.subCategoryName as category_name, SUM(transaction.transaction_amount) as category_amount");

        if(!empty($start_date)){
            //convert date to understandable format
            $start_date = \DateTime::createFromFormat("d-m-Y", $start_date);
            $start_date = $start_date->format('Y-m-d').' 00:00:00';

            $db
                ->andWhere('transaction.transaction_time >= :datefrom')
                ->setParameter('datefrom', $start_date);
        }

        if(!empty($end_date)){
            //convert date to understandable format
            $end_date = \DateTime::createFromFormat("d-m-Y", $end_date);
            $end_date = $end_date->format('Y-m-d'). ' 23:59:59';

            $db
                ->andWhere('transaction.transaction_time <= :dateto')
                ->setParameter('dateto', $end_date);
        }

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_transaction_count()
    {
        $db = $this->createQueryBuilder('transaction')
            ->select('COUNT(transaction) as transaction_count')
            ->where('transaction.active = 1');

        return $db
            ->getQuery()
            ->execute();
    }

    public function get_total_cashflow_by_category($data_type)
    {
        $db = $this->createQueryBuilder('transaction')
            ->select("sc.subCategoryName as category_name, SUM(transaction.transaction_amount) as category_amount")
            ->innerJoin('transaction.category', 'cc')
            ->innerJoin('transaction.subCategory', 'sc')
            ->innerJoin('transaction.transaction_type', 'tt')
            ->andWhere('transaction.active = 1')
            ->andWhere('tt.id = :data_type')
            ->setParameter('data_type', $data_type)
            ->groupBy('sc.subCategoryName');

        return $db
            ->getQuery()
            ->execute();
    }
}
