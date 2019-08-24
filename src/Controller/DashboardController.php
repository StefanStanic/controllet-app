<?php

namespace App\Controller;


use App\Entity\Account;
use App\Form\AccountType;
use App\Service\DashboardService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private $em;
    private $dashboard_service;
    private $user_service;

    public function __construct(EntityManagerInterface $em, DashboardService $dashboardService, UserService $userService)
    {
        $this->em = $em;
        $this->dashboard_service = $dashboardService;
        $this->user_service = $userService;
    }

    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard()
    {

        $user = $this->getUser();

        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user->getId());
        $data['transactions'] = $this->dashboard_service->get_transaction_by_filters($user->getId(), 0, 0, 0,  'DESC', '', '');
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['transaction_types'] = $this->dashboard_service->get_active_transaction_types();

        //basic input calculation data
        $data['total_balance'] = $this->dashboard_service->get_total_balance($user->getId());
        $data['total_expenses'] = $this->dashboard_service->get_total_expenses_by_filters($user->getId(), 0, 0);
        $data['total_income'] = $this->dashboard_service->get_total_income_by_filters($user->getId(), 0, 0);

        return $this->render(
            'dashboard/dashboard.html.twig',
            array(
                'accounts' => $data['accounts'],
                'categories' => $data['categories'],
                'transactions' => $data['transactions'],
                'transaction_types' => $data['transaction_types'],
                'total_balance' => $data['total_balance'][0]['total_balance'],
                'total_expenses' => $data['total_expenses'][0]['total_expenses'],
                'total_income' => $data['total_income'][0]['total_income']
            )
        );
    }

    /**
     * @Route("/transactions", name="app_transactions")
     */
    public function transactions()
    {
        $user = $this->getUser();

        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user->getId());
        $data['transactions'] = $this->dashboard_service->get_transaction_by_filters($user->getId(), 0, 0, 0, 'DESC', '', '');
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['subcategories'] = $this->dashboard_service->get_active_subcategories();
        $data['transaction_types'] = $this->dashboard_service->get_active_transaction_types();

        return $this->render(
            'dashboard/transactions.html.twig',
            array(
                'accounts' => $data['accounts'],
                'categories' => $data['categories'],
                'subcategories' => $data['subcategories'],
                'transactions' => $data['transactions'],
                'transaction_types' => $data['transaction_types']
            )
        );
    }


    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile()
    {
        return $this->render(
            'profile/profile.html.twig'
        );
    }


    /**
     * @Route("/budget", name="app_budget")
     */
    public function budget()
    {
        $user = $this->getUser();

        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user->getId());
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['budgets'] = $this->dashboard_service->get_active_budgets_by_user_id($user->getId());

        return $this->render(
            'dashboard/budget.html.twig',
            array(
                'accounts' => $data['accounts'],
                'categories' => $data['categories'],
                'budgets' => $data['budgets']
            )
        );
    }

    /**
     * @Route("/addBudget", name="app_add_budget", methods={"POST"})
     */
    public function add_budget(Request $request)
    {
        $user_id = $request->get('user_id');
        $category_id = $request->get('category');
        $account_id = $request->get('account_type');
        $budget_amount = $request->get('budget_amount');
        $budget_name = $request->get('budget_name');

        if(empty($user_id) || empty($category_id) || empty($account_id) || empty($budget_amount) || empty($budget_name))
        {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Please make sure to fill all the required fields.'
                )
            ), Response::HTTP_OK);
            return $response;
        }

        $budget = $this->dashboard_service->add_budget($user_id, $category_id, $account_id, $budget_amount, $budget_name);

        if($budget){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'text' => 'Budget successfully added.'
                )
            ), Response::HTTP_OK);

            return $response;
        }else {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Something went wrong, try again later.'
                )
            ), Response::HTTP_OK);

            return $response;
        }

    }


    /**
     * @Route("/updateBudget", name="app_update_budget", methods={"POST"})
     */
    public function updateBudget(Request $request)
    {
        $account = $request->get('accountType');
        $category = $request->get('category');
        $budgetName = $request->get('budgetName');
        $budgetAmount = $request->get('budgetAmount');
        $budget_id = $request->get('budget_id');


        if(empty($account) || empty($category) || empty($budgetName) || empty($budgetAmount)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Please make sure to fill all the required fields.'
                )
            ), Response::HTTP_OK);
            return $response;
        }

        $budget = $this->dashboard_service->update_budget($budget_id, $account, $category, $budgetName, $budgetAmount);


        if($budget){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'text' => 'Budget successfully added.'
                )
            ), Response::HTTP_OK);

            return $response;
        }
        else {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Something went wrong, try again later.'
                )
            ), Response::HTTP_OK);

            return $response;
        }

    }

    /**
     * @Route("/updateProfile", name="app_update_profile", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function update_profile(Request $request)
    {
        $user_id = $request->get('user_id');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $date_of_birth = $request->get('date_of_birth');
        $company = $request->get('company');
        $city = $request->get('city');
        $country = $request->get('country');


        if(empty($user_id) || empty($first_name) || empty($last_name) || empty($date_of_birth) ||
            empty($company) || empty($city) || empty($country))
        {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Please make sure to fill all the required fields.'
                )
            ), Response::HTTP_OK);
            return $response;
        }


        $profile = $this->user_service->update_profile_by_user_id($user_id, $first_name, $last_name, $date_of_birth, $company, $city, $country);

        if($profile){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'text' => 'Profile successfully added.'
                )
            ), Response::HTTP_OK);

            return $response;
        }
        else {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Something went wrong, try again later.'
                )
            ), Response::HTTP_OK);

            return $response;
        }
    }

    /**
     * @Route("/addAccount/{user_id}")
     * @param $user_id
     * @param Request $request
     * @return Response
     */
    public function addAccount($user_id, Request $request)
    {
        $user = $this->getUser();
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);

        //handle form submit
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $user->getId() == $user_id) {

            $date = date('Y-m-d H:i:s');

            //setup account
            $account->setUser($user);
            $account->setLastUsedDate(\DateTime::createFromFormat('Y-m-d H:i:s', $date));
            $account->setActive(1);

            $this->em->persist($account);
            $this->em->flush();


            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render(
            'dashboard/addAccount.html.twig',
            array(
                'user_id' => $user_id,
                'form' => $form->createView()
            )
        );
    }

    /**
     * @Route("/updateAccount", name="app_updateAccount", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function updateAccount(Request $request)
    {
        $account_name    = $request->get('accountName');
        $account_balance = $request->get('accountBalance');
        $user_id         = $request->get('user_id');
        $account_id      = $request->get('account_id');

        if(empty($account_name) || empty($account_balance) || empty($user_id) || empty($account_id)){
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }

        $result = $this->dashboard_service->update_account($account_name, $account_balance, $account_id, $user_id);

        if($result)
        {
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }

    }

    /**
     * @Route("/deleteAccount", name="delete_account", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteAccount(Request $request)
    {
        $account_id = $request->get('account_id');

        $result = $this->dashboard_service->delete_account($account_id);

        if($result){
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }

    /**
     * @Route("/deleteBudget", name="delete_account", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteBudget(Request $request)
    {
        $budget_id = $request->get('budget_id');

        $result = $this->dashboard_service->delete_budget($budget_id);

        if($result){
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }
//
//    /**
//     * @Route("/transactions/{account_id}", name="app_transactions")
//     */
//    public function transactions($account_id = false)
//    {
//        return $this->render(
//            'dashboard/transactions.html.twig'
//        );
//    }

    /**
     * @Route("/addTransaction", name="app_addTransaction", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function add_transaction(Request $request)
    {
        $user_id = $request->get('user_id');
        $transaction_name = $request->get('transactionName');
        $transaction_account_type = $request->get('transactionAccountType');
        $transaction_type = $request->get('transactionType');
        $transaction_category = $request->get('transactionCategory');
        $transaction_subcategory = $request->get('transactionSubCategory');
        $transaction_amount = $request->get('transactionAmount');
        $transaction_note = $request->get('transactionNote');


        if(empty($user_id) || empty($transaction_name) || empty($transaction_account_type) || empty($transaction_type) ||
           empty($transaction_category) || empty($transaction_amount) || empty($transaction_subcategory))
        {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Please make sure to fill all the required fields.'
                )
            ), Response::HTTP_OK);
            return $response;
        }


        $transaction = $this->dashboard_service->add_transaction($user_id, $transaction_name, $transaction_account_type, $transaction_type, $transaction_category, $transaction_subcategory, $transaction_amount, $transaction_note);

        if($transaction){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'text' => 'Transaction successfully added.'
                )
            ), Response::HTTP_OK);

            return $response;
        }
        else {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Not enough money on selected account.'
                )
            ), Response::HTTP_OK);

            return $response;
        }
    }

    /**
     * @Route("/updateTransaction", name="update_account", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function update_transaction(Request $request)
    {
        //get input data
        $transaction_id = $request->get('transaction_id');
        $transaction_category = $request->get('transaction_category');
        $transaction_subcategory = $request->get('transaction_subcategory');
        $transaction_note = $request->get('transaction_note');
        $transaction_amount = $request->get('transaction_amount');

        if(empty($transaction_id) || empty($transaction_category) || empty($transaction_note) || empty($transaction_amount) || empty($transaction_subcategory)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $result = $this->dashboard_service->update_transaction($transaction_id, $transaction_category, $transaction_subcategory,  $transaction_note, $transaction_amount);

        if($result){
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }

    /**
     * @Route("/deleteTransaction", name="delete_transction", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function delete_transaction(Request $request)
    {
        $transaction_id = $request->get('transaction_id');

        if(empty($transaction_id)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $result = $this->dashboard_service->delete_transaction($transaction_id);

        if($result){
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }


    /**
     * @Route("/filterTransactions", methods={"POST"})
     */
    public function get_transaction_by_filters(Request $request)
    {
        $account_id = $request->get('account_id');
        $category_id = $request->get('category_id');
        $subcategory_id = $request->get('subcategory_id');
        $user_id = $request->get('user_id');
        $start_date = $request->get('date_from');
        $end_date = $request->get('date_to');

        if((empty($account_id) && $account_id !=0) || (empty($category_id) && $category_id !=0) || empty($user_id)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $data['transactions'] = $this->dashboard_service->get_transaction_by_filters($user_id, $account_id, $category_id, $subcategory_id,  '', $start_date, $end_date);
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user_id);
        $data['subcategories'] = $this->dashboard_service->get_active_subcategories();
        $data['transaction_types'] = $this->dashboard_service->get_active_transaction_types();


        $transactions = $this->render(
            'dashboard/transaction_list.html.twig',
            [
                'transactions' => $data['transactions'],
                'categories' => $data['categories'],
                'subcategories' => $data['subcategories'],
                'accounts' => $data['accounts'],
                'transaction_types' => $data['transaction_types']
            ]
        )->getContent();

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'html' => $transactions
            )
        ), Response::HTTP_OK);

        return $response;
    }

    /**
     * @Route("/getChartDataByFiltersAndType", methods={"POST"})
     * @param Request $request
     * @return bool|Response
     */
    public function get_chart_data_by_filters_and_type(Request $request)
    {

        $data_type = $request->get('data_type');
        $user_id = $request->get('user_id');
        $start_date = $request->get('date_from');
        $end_date = $request->get('date_to');

        if(empty($start_date) || empty($end_date)){
            return false;
        }

        $result = $this->dashboard_service->get_chart_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'data' => $result
            )
        ), Response::HTTP_OK);

        return $response;

    }

    /**
     * @Route("/getPieDataByFiltersAndType", methods={"POST"})
     * @param Request $request
     * @return bool|Response
     */
    public function get_pie_data_by_filters_and_type(Request $request)
    {
        $data_type = $request->get('data_type');
        $user_id = $request->get('user_id');
        $start_date = $request->get('date_from');
        $end_date = $request->get('date_to');

        if(empty($start_date) || empty($end_date)){
            return false;
        }

        $result = $this->dashboard_service->get_pie_data_by_filters_and_type($user_id, $data_type, $start_date, $end_date);

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'data' => $result
            )
        ), Response::HTTP_OK);

        return $response;

    }

    /**
     * @Route("/subCategories", methods={"POST"})
     */
    public function getSubCategories(Request $request)
    {
        $category_id = $request->get('category_id');

        $result = $this->dashboard_service->get_subcategories($category_id);

        $html = $this->render(
            'components/subcategory.html.twig',
            [
                'subcategories' => $result
            ]
        )->getContent();

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'html' => $html
            )
        ), Response::HTTP_OK);

        return $response;

    }

    /**
     * @Route("/getExpenseByAccountTotal", name="app_expense_total", methods={"POST"})
     */
    public function getExpenseByAccountTotal(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $account_id = $request->get('account_id');

        if(empty($account_id)){
            return false;
        }

        if(empty($year) || empty($month)){
            $year = date('Y');
            $month = date('m');
        }

        $result = $this->dashboard_service->getTotalExpensesByYearMonthAccount($year, $month, $account_id);

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'data' => $result
            )
        ), Response::HTTP_OK);

        return $response;

    }

    /**
     * @Route("/bills")
     */
    public function bills()
    {
        $user = $this->getUser();

        $data['bills'] = $this->dashboard_service->get_bills_by_filters($user->getId(), 0, 0, 0,  'DESC', '', '');
        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user->getId());
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['subcategories'] = $this->dashboard_service->get_active_subcategories();

        return $this->render(
            'bills/bills.html.twig',
            array(
                'bills' => $data['bills'],
                'accounts' => $data['accounts'],
                'categories' => $data['categories'],
                'subcategories' => $data['subcategories']
            )
        );


    }


    /**
     * @Route("/addBill", name="app_add_bill", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addBill(Request $request)
    {
        $name = $request->get('name');
        $amount = $request->get('amount');
        $note = $request->get('note');
        $date_due = $request->get('date_due');
        $category = $request->get('category');
        $subcategory = $request->get('subcategory');
        $account = $request->get('account');
        $recurring_bill = $request->get('recurring_bill');

        if(empty($name) || empty($amount) || empty($note) || empty($date_due) || empty($category) || empty($subcategory) || empty($account) || !in_array($recurring_bill, array(0,1))){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $bill = $this->dashboard_service->add_bill($name, $amount, $note, $date_due, $category, $subcategory, $account, $recurring_bill);

        if($bill){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'text' => 'Bill successfully added.'
                )
            ), Response::HTTP_OK);

            return $response;
        }else {
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Something went wrong, try again later.'
                )
            ), Response::HTTP_OK);

            return $response;
        }

    }


    /**
     * @Route("/deleteBill", name="delete_bill", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteBill(Request $request)
    {
        $bill_id = $request->get('bill_id');

        $result = $this->dashboard_service->delete_bill($bill_id);

        if ($result) {
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        } else {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }


    /**
     * @Route("/updateBill", name="update_account", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function update_bill(Request $request)
    {
        //get input data
        $bill_id = $request->get('id');
        $bill_name = $request->get('name');
        $bill_category = $request->get('category');
        $bill_subcategory = $request->get('subcategory');
        $bill_account = $request->get('account');
        $bill_note = $request->get('note');
        $bill_amount = $request->get('amount');

        if(empty($bill_id) || empty($bill_name) || empty($bill_category) || empty($bill_subcategory) || empty($bill_account) || empty($bill_note) || empty($bill_amount)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $result = $this->dashboard_service->update_bill($bill_id, $bill_name, $bill_category,  $bill_subcategory, $bill_account, $bill_note, $bill_amount);

        if($result){
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }
        else
        {
            $response = new Response();
            $response->setStatusCode(400);

            return $response;
        }
    }

    /**
     * @Route("/filterBills", methods={"POST"})
     */
    public function get_bills_by_filters(Request $request)
    {
        $account_id = $request->get('account_id');
        $category_id = $request->get('category_id');
        $subcategory_id = $request->get('subcategory_id');
        $user_id = $request->get('user_id');
        $start_date = $request->get('date_from');
        $end_date = $request->get('date_to');

        if((empty($account_id) && $account_id !=0) || (empty($category_id) && $category_id !=0) || empty($user_id)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $data['bills'] = $this->dashboard_service->get_bills_by_filters($user_id, $account_id, $category_id, $subcategory_id,  '', $start_date, $end_date);
        $data['categories'] = $this->dashboard_service->get_active_categories();
        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user_id);
        $data['subcategories'] = $this->dashboard_service->get_active_subcategories();
        $data['transaction_types'] = $this->dashboard_service->get_active_transaction_types();


        $transactions = $this->render(
            'bills/bills_list.html.twig',
            [
                'bills' => $data['bills'],
                'categories' => $data['categories'],
                'subcategories' => $data['subcategories'],
                'accounts' => $data['accounts'],
                'transaction_types' => $data['transaction_types']
            ]
        )->getContent();

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'html' => $transactions
            )
        ), Response::HTTP_OK);

        return $response;
    }

}