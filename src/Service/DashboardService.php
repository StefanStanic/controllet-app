<?php

namespace App\Service;


use App\Entity\Account;
use App\Entity\Bills;
use App\Entity\Budget;
use App\Entity\Category;
use App\Entity\Currency;
use App\Entity\SubCategory;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Entity\User;
use App\Form\AccountType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use RRule\RRule;

class DashboardService
{
    private $em;
    private $guzzle;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->guzzle = new Client();
    }

    /**
     * @param $user_id
     * @return Account[]|Collection
     */
    public function get_active_wallets_by_user_id($user_id)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        return $user->getAccount();
    }

    /**
     * @return Category[]|object[]
     */
    public function get_active_categories()
    {
        $categories = $this->em->getRepository(Category::class)->findAll();

        return $categories;
    }

    /**
     * @return SubCategory[]|object[]
     */
    public function get_active_subcategories()
    {
        $subcategory= $this->em->getRepository(SubCategory::class)->findAll();

        return $subcategory;
    }

    /**
     * @return TransactionType[]|object[]
     */
    public function get_active_transaction_types()
    {
        $transaction_types = $this->em->getRepository(TransactionType::class)->findAll();

        return $transaction_types;
    }

    public function get_all_currencies()
    {
        $currencies = $this->em->getRepository(Currency::class)->findAll();

        return $currencies;
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
    public function get_transaction_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort = "DESC", $start_date, $end_date)
    {
        $transactions = $this->em->getRepository(Transaction::class)->get_transaction_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort, $start_date, $end_date);
        return $transactions;
    }


    /**
     * @param $user_id
     * @param $data_type
     * @param $start_date
     * @param $end_date
     * @return bool|mixed
     */
    public function get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $chart_data = $this->em->getRepository(Transaction::class)->get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        if($chart_data){
            return $chart_data;
        }

        return false;
    }

    /**
     * @param $user_id
     * @param $data_type
     * @param $start_date
     * @param $end_date
     * @return bool|mixed
     */
    public function get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date)
    {
        $chart_data = $this->em->getRepository(Transaction::class)->get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        if($chart_data){
            return $chart_data;
        }

        return false;
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @return mixed
     */
    public function get_total_expenses_by_filters($user_id, $account_id, $category_id, $default_currency)
    {
        $total_expenses = $this->em->getRepository(Transaction::class)->get_total_expenses_by_filters($user_id, $account_id, $category_id, $default_currency);
        return $total_expenses;
    }

    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @return mixed
     */
    public function get_total_income_by_filters($user_id, $account_id, $category_id, $default_currency)
    {
        $total_income = $this->em->getRepository(Transaction::class)->get_total_income_by_filters($user_id, $account_id, $category_id, $default_currency);
        return $total_income;
    }

    /**
     * @param $user_id
     * @return ArrayCollection
     */
    public function get_active_budgets_by_user_id($user_id)
    {
        $budgets = $this->em->getRepository(Budget::class)->get_budgets_by_user_id($user_id);
        return $budgets;
    }

    /**
     * @param $category_id
     * @return SubCategory[]|object[]
     */
    public function get_subcategories($category_id)
    {
        $category = $this->em->getRepository(Category::class)->find($category_id);

        $subcategories = $this->em->getRepository(SubCategory::class)->findBy(
            ['category' => $category]
        );

        return $subcategories;
    }

    /**
     * @param $user_id
     * @param $account_name
     * @param $account_type
     * @param $account_currency
     * @param $account_balance
     * @return bool
     */
    public function add_account($user_id, $account_name, $account_type, $account_currency, $account_balance)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        $account_currency = $this->em->getRepository(Currency::class)->find($account_currency);

        $account = new Account();
        $date = date('Y-m-d H:i:s');

        //create a transaction
        $account
            ->setUser($user)
            ->setActive(1)
            ->setAccountBalance($account_balance)
            ->setAccountName($account_name)
            ->setAccountType($account_type)
            ->setCurrency($account_currency)
            ->setLastUsedDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date));

        $this->em->persist($account);
        $this->em->flush();

        return $user_id;

    }


    /**
     * @param $account_name
     * @param $account_balance
     * @param $account_id
     * @param $user_id
     * @return mixed
     */
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

    /**
     * @param $account_id
     * @return mixed
     */
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

    /**
     * @param $budget_id
     * @return mixed
     */
    public function delete_budget($budget_id)
    {
        $budget = $this->em->getRepository(Budget::class)->find($budget_id);

        if ($budget) {

            //set wallet to active 0 for later restoration capabilities
            $budget->setActive(0);
            $this->em->flush();

            return $budget_id;
        }
    }


    /**
     * @param $user_id
     * @param $category_id
     * @param $account_id
     * @param $budget_amount
     * @param $budget_name
     * @return Budget
     */
    public function add_budget($user_id, $category_id, $account_id, $budget_amount, $budget_name)
    {
        $budget = new Budget();
        $account = $this->em->getRepository(Account::class)->find($account_id);
        $category = $this->em->getRepository(Category::class)->find($category_id);

        $budget
            ->setCategory($category)
            ->setAccount($account)
            ->setBudgetAmount($budget_amount)
            ->setName($budget_name)
            ->setActive(1);
        ;

        $this->em->persist($budget);
        $this->em->flush();

        return $budget;
    }

    /**
     * @param $budget_id
     * @param $account_id
     * @param $category_id
     * @param $budgetName
     * @param $budgetAmount
     * @return mixed
     */
    public function update_budget($budget_id, $account_id, $category_id, $budgetName, $budgetAmount)
    {
        $account = $this->em->getRepository(Account::class)->find($account_id);
        $category = $this->em->getRepository(Category::class)->find($category_id);
        $budget = $this->em->getRepository(Budget::class)->find($budget_id);

        if($budget){
            $budget
                ->setName($budgetName)
                ->setAccount($account)
                ->setBudgetAmount($budgetAmount)
                ->setCategory($category);

            $this->em->flush();

            return $budget_id;
        }
    }

    /**
     * @param $name
     * @param $amount
     * @param $note
     * @param $date_due
     * @param $category
     * @param $subcategory
     * @param $account
     * @param $recurring_bill
     * @return Bills
     */
    public function add_bill($name, $amount, $note, $date_due, $category, $subcategory, $account, $recurring_bill)
    {
        $bill = new Bills();
        $category = $this->em->getRepository(Category::class)->find($category);
        $subcategory = $this->em->getRepository(SubCategory::class)->find($subcategory);
        $account = $this->em->getRepository(Account::class)->find($account);
        $date = date('Y-m-d H:i:s');
        $date_due = date('Y-m-d', strtotime($date_due));

        //generate group tag for recurring deletion feature
        $group_tag = uniqid();

        if($recurring_bill == 1){
            $rrule = new RRule([
                'FREQ' => 'MONTHLY',
                'INTERVAL' => 1,
                'DTSTART' => $date_due,
                'COUNT' => 24
            ]);

            foreach ( $rrule as $occurrence ) {
                $recurring_date = $occurrence->format('Y-m-d');

                $bill = new Bills();
                $bill
                    ->setName($name)
                    ->setAmount($amount)
                    ->setNote($note)
                    ->setDateDue(\DateTime::createFromFormat('Y-m-d', $recurring_date))
                    ->setCategory($category)
                    ->setSubcategory($subcategory)
                    ->setAccount($account)
                    ->setActive(1)
                    ->setGroupTag($group_tag)
                    ->setDateAdded(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
                    ->setDateUpdated(\DateTime::createFromFormat('Y-m-d H:i:s', $date));

                $this->em->persist($bill);
            }

            $this->em->flush();
            return $bill;
        }

        $bill
            ->setName($name)
            ->setAmount($amount)
            ->setNote($note)
            ->setDateDue(\DateTime::createFromFormat('Y-m-d', $date_due))
            ->setCategory($category)
            ->setSubcategory($subcategory)
            ->setAccount($account)
            ->setGroupTag($group_tag)
            ->setActive(1)
            ->setDateAdded(\DateTime::createFromFormat('Y-m-d H:i:s', $date))
            ->setDateUpdated(\DateTime::createFromFormat('Y-m-d H:i:s', $date));

        $this->em->persist($bill);
        $this->em->flush();

        return $bill;
    }

    /**
     * @param $bill_id
     * @param $bill_group_id
     * @return mixed
     */
    public function delete_bill($bill_id, $bill_group_id)
    {
        $bills = $this->em->getRepository(Bills::class)->findBy(
            [
                'group_tag' => $bill_group_id
            ]
        );
        foreach ($bills as $bill) {
            //set bill to active 0 for later restoration capabilities
            $bill->setActive(0);
            $this->em->persist($bill);
        }
        $this->em->flush();

        return $bill_id;
    }

    /**
     * @param $bill_id
     * @param $bill_name
     * @param $bill_category
     * @param $bill_subcategory
     * @param $bill_account
     * @param $bill_note
     * @param $bill_amount
     * @return mixed
     */
    public function update_bill($bill_id, $bill_name, $bill_category, $bill_subcategory, $bill_account, $bill_note, $bill_amount)
    {
        $bill = $this->em->getRepository(Bills::class)->find($bill_id);
        $account = $this->em->getRepository(Account::class)->find($bill_account);
        $category = $this->em->getRepository(Category::class)->find($bill_category);
        $subcategory = $this->em->getRepository(SubCategory::class)->find($bill_subcategory);

        if($bill){
            $bill
                ->setName($bill_name)
                ->setAccount($account)
                ->setSubCategory($subcategory)
                ->setCategory($category)
                ->setNote($bill_note)
                ->setAmount($bill_amount);

            $this->em->flush();
            return $bill_id;
        }
    }


    /**
     * @param $user_id
     * @param $account_id
     * @param $category_id
     * @param $subcategory_id
     * @param string $sort
     * @param $date_from
     * @param $date_to
     * @return mixed
     */
    public function get_bills_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort = "DESC", $date_from, $date_to)
    {
        $bills = $this->em->getRepository(Bills::class)->get_bills_by_filters($user_id, $account_id, $category_id, $subcategory_id, $sort, $date_from, $date_to);
        return $bills;
    }

    /**
     * @param $user_id
     * @param $transaction_name
     * @param $transaction_account_type
     * @param $transaction_type
     * @param $transaction_category
     * @param $transaction_subcategory
     * @param $transaction_amount
     * @param $transaction_note
     * @return bool
     */
    public function add_transaction($user_id, $transaction_name, $transaction_account_type, $transaction_type, $transaction_category, $transaction_subcategory, $transaction_amount, $transaction_note, $transaction_currency)
    {
        $user = $this->em->getRepository(User::class)->find($user_id);
        $account = $this->em->getRepository(Account::class)->find($transaction_account_type);
        $transaction_t = $this->em->getRepository(TransactionType::class)->find($transaction_type);
        $category = $this->em->getRepository(Category::class)->find($transaction_category);
        $subcategory = $this->em->getRepository(SubCategory::class)->find($transaction_subcategory);
        $currency = $this->em->getRepository(Currency::class)->find($transaction_currency);

        $transaction = new Transaction();
        $date = date('Y-m-d H:i:s');

        //convert currency if needed
        if($account->getCurrency()->getId() != $currency->getId()){
            $transaction_amount = $this->convert_currency($currency->getCurrencyLabel(), $account->getCurrency()->getCurrencyLabel(), $transaction_amount);
        }

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

    /**
     * @param $transaction_id
     * @param $transaction_category
     * @param $transaction_subcategory
     * @param $transaction_note
     * @param $transaction_amount
     * @return mixed
     */
    public function update_transaction($transaction_id, $transaction_category, $transaction_subcategory, $transaction_note, $transaction_amount)
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

    /**
     * @param $transaction_id
     * @return mixed
     */
    public function delete_transaction($transaction_id)
    {
        $transaction = $this->em->getRepository(Transaction::class)->find($transaction_id);

        if($transaction){
            $transaction->setActive(0);
            $this->em->flush();
        }

        return $transaction_id;
    }

    /**
     * @param $user_id
     * @return bool|mixed
     */
    public function get_total_balance($user_id)
    {
        $balance = $this->em->getRepository(Account::class)->get_total_balance($user_id);

        if($balance){
            return $balance;
        }

        return false;
    }

    /**
     * @param $year
     * @param $month
     * @param $account
     * @return mixed
     */
    public function getTotalExpensesByYearMonthAccount($year, $month, $account, $category)
    {
        $total_expenses = $this->em->getRepository(Transaction::class)->get_total_expenses_by_year_month_account($year, $month, $account, $category);
        return $total_expenses;
    }

    /**
     * @param $currency_from
     * @param $currency_to
     * @param $amount
     * @return float|int
     */
    public function convert_currency($currency_from, $currency_to, $amount)
    {

        $result = $this->guzzle->get('https://free.currconv.com/api/v7/convert', [
            'query' => [
                'q' => $currency_from . '_' . $currency_to,
                'compact' => 'ultra',
                'apiKey' => 'a34b33d8e8738cb32ee5'
            ]
        ])->getBody()->getContents();

        $result = json_decode($result, true);

        $value = $amount * $result[$currency_from . '_' . $currency_to];

        return $value;
    }
}