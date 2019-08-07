<?php

namespace App\Service;


use App\Entity\Account;
use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function get_active_wallets_by_user_id($user_id)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        return $user->getAccount();
    }

    public function get_active_categories()
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $categories;
    }

    public function get_active_subcategories()
    {
        $subcategory= $this->em->getRepository(SubCategory::class)->findAll();

        return $subcategory;
    }

    public function get_active_transaction_types()
    {
        $transaction_types = $this->em->getRepository(TransactionType::class)->findAll();

        return $transaction_types;
    }

    public function get_transaction_by_filters($user_id, $account_id, $category_id, $sort = "DESC", $start_date, $end_date)
    {
        $transactions = $this->em->getRepository(Transaction::class)->get_transaction_by_filters($user_id, $account_id, $category_id, $sort, $start_date, $end_date);
        return $transactions;
    }


    public function get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $chart_data = $this->em->getRepository(Transaction::class)->get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        if($chart_data){
            return $chart_data;
        }

        return false;
    }

    public function get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $chart_data = $this->em->getRepository(Transaction::class)->get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        if($chart_data){
            return $chart_data;
        }

        return false;
    }

    public function get_total_expenses_by_filters($user_id, $account_id, $category_id)
    {
        $total_expenses = $this->em->getRepository(Transaction::class)->get_total_expenses_by_filters($user_id, $account_id, $category_id);
        return $total_expenses;
    }

    public function get_total_income_by_filters($user_id, $account_id, $category_id)
    {
        $total_income = $this->em->getRepository(Transaction::class)->get_total_income_by_filters($user_id, $account_id, $category_id);
        return $total_income;
    }

    public function get_active_budgets_by_user_id($user_id)
    {
        $budgets = $this->em->getRepository(Budget::class)->get_budgets_by_user_id($user_id);
        return $budgets;
    }

    public function get_subcategories($category_id)
    {
        $category = $this->em->getRepository(Category::class)->find($category_id);

        $subcategories = $this->em->getRepository(SubCategory::class)->findBy(
            ['category' => $category]
        );

        return $subcategories;
    }

    public function update_account($account_name, $account_balance, $account_id, $user_id)
    {
        $account = $this->em->getRepository(Account::class)->find($account_id);
        $date = date('Y-m-d H:i:s');

        if($account){
            $account
                ->setAccountName($account_name)
                ->setAccountBalance($account_balance)
                ->setLastUsedDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $this->em->flush();

            return $account_id;
        }
    }

    public function delete_account($account_id)
    {
        $account = $this->em->getRepository(Account::class)->find($account_id);

        if ($account) {

            //set wallet to active 0 for later restoration capabilities
            $account->setActive(0);
            $this->em->flush();

            return $account_id;
        }
    }


    public function add_budget($user_id, $category_id, $account_id, $budget_amount)
    {
        $date = date('Y-m-d');

        $budget = new Budget();
        $user = $this->em->getRepository(User::class)->find($user_id);
        $account = $this->em->getRepository(Account::class)->find($account_id);
        $category = $this->em->getRepository(Category::class)->find($category_id);
        $current_budget_amount = $this->em->getRepository(Budget::class)->get_total_budget_for_current_month($date, $user_id, $account_id);
        $does_budget_with_category_exists = $this->em->getRepository(Budget::class)->get_budget_by_category_and_date($date, $user, $account_id, $category_id);

        //check if budget passes the total account balance
        if($budget_amount <= ($account->getAccountBalance() - $current_budget_amount[0]['total_budget']) && empty($does_budget_with_category_exists)){
            $budget
                ->setCategory($category)
                ->setAccount($account)
                ->setBudgetAmount($budget_amount)
                ->setOriginalBudgetAmount($budget_amount)
                ->setBudgetDate(\DateTime::createFromFormat('Y-m-d', $date))
                ->setActive(1);
            ;

            $this->em->persist($budget);
            $this->em->flush();

            return $user_id;

        }else{
            return false;
        }
    }

    public function add_transaction($user_id, $transaction_name, $transaction_account_type, $transaction_type, $transaction_category, $transaction_subcategory, $transaction_amount, $transaction_note)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        $account = $this->em->getRepository(Account::class)->find($transaction_account_type);
        $transaction_t = $this->em->getRepository(TransactionType::class)->find($transaction_type);
        $category = $this->em->getRepository(Category::class)->find($transaction_category);
        $subcategory = $this->em->getRepository(SubCategory::class)->find($transaction_subcategory);

        $transaction = new Transaction();
        $date = date('Y-m-d H:i:s');


        //transaction type logic
        if($transaction_t->getTransactionType() == 'Income'){
            $account->setAccountBalance( $account->getAccountBalance() + $transaction_amount);
        } else {
            //check if enough money to make the transaction
            if($account->getAccountBalance() >= $transaction_amount){
                //update account balance
                $account->setAccountBalance( $account->getAccountBalance() - $transaction_amount);
            }else {
                return false;
            }
        }

        //create a transaction
        $transaction
            ->setUser($user)
            ->setCategory($category)
            ->setSubCategory($subcategory)
            ->setAccount($account)
            ->setTransactionName($transaction_name)
            ->setTransactionAmount($transaction_amount)
            ->setNote($transaction_note)
            ->setTransactionTime(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
            ->setActive(1)
            ->setTransactionType($transaction_t);

        $this->em->persist($transaction);
        $this->em->flush();

        return $user_id;

    }

    public function update_transaction($transaction_id, $transaction_category, $transaction_subcategory,  $transaction_note, $transaction_amount)
    {
        $transaction = $this->em->getRepository(Transaction::class)->find($transaction_id);
        $category = $this->em->getRepository(Category::class)->find($transaction_category);
        $subcategory = $this->em->getRepository(SubCategory::class)->find($transaction_subcategory);

        if($transaction){
            $transaction
                ->setSubCategory($subcategory)
                ->setCategory($category)
                ->setNote($transaction_note)
                ->setTransactionAmount($transaction_amount);

            $this->em->flush();
            return $transaction_id;
        }
    }

    public function delete_transaction($transaction_id)
    {
        $transaction = $this->em->getRepository(Transaction::class)->find($transaction_id);

        if($transaction){
            $transaction->setActive(0);
            $this->em->flush();
        }

        return $transaction_id;
    }

    public function get_total_balance($user_id)
    {
        $balance = $this->em->getRepository(Account::class)->get_total_balance($user_id);

        if($balance){
            return $balance;
        }

        return false;
    }
}