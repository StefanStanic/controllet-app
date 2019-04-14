<?php
/**
 * Created by PhpStorm.
 * User: stefke
 * Date: 2019-03-19
 * Time: 19:37
 */

namespace App\Controller;


use App\Entity\Account;
use App\Form\AccountType;
use App\Service\DashboardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    private $em;
    private $dashboard_service;

    public function __construct(EntityManagerInterface $em, DashboardService $dashboardService)
    {
        $this->em = $em;
        $this->dashboard_service = $dashboardService;
    }

    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard()
    {

        $user = $this->getUser();

        //get all active wallets
        $data['accounts'] = $this->dashboard_service->get_active_wallets_by_user_id($user->getId());
        $data['transactions'] = $this->dashboard_service->get_transactions_by_user_id_sorted($user->getId());
        $data['categories'] = $this->dashboard_service->get_active_categories();

        return $this->render(
            'dashboard/dashboard.html.twig',
            array(
                'accounts' => $data['accounts'],
                'categories' => $data['categories'],
                'transactions' => $data['transactions']
            )
        );
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

        //handle form submititon
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
     * @Route("/transactions/{account_id}", name="app_transactions")
     */
    public function transactions($account_id = false)
    {
        return $this->render(
            'dashboard/transactions.html.twig'
        );
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
        $transaction_note = $request->get('transaction_note');
        $transaction_amount = $request->get('transaction_amount');

        if(empty($transaction_id) || empty($transaction_category) || empty($transaction_note) || empty($transaction_amount)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $result = $this->dashboard_service->update_transaction($transaction_id, $transaction_category, $transaction_note, $transaction_amount);

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
     * @Route("/filterTransactionByAccountId", methods={"POST"})
     */
    public function get_transaction_by_account_id(Request $request)
    {
        $account_id = $request->get('account_id');
        $user_id = $request->get('user_id');

        if((empty($account_id) && $account_id !=0 ) || empty($user_id)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $data['transactions'] = $this->dashboard_service->get_transactions_by_account_id_sorted($user_id, $account_id);
        $data['categories'] = $this->dashboard_service->get_active_categories();

        if(empty($data['transactions'])){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'No transactions'
                )
            ), Response::HTTP_NOT_FOUND);
            return $response;
        }

        $transactions = $this->render(
            'dashboard/transaction_list.html.twig',
            [
                'transactions' => $data['transactions'],
                'categories' => $data['categories']
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
     * @Route("/filterTransactionByCategoryId", methods={"POST"})
     */
    public function get_transaction_by_category_id(Request $request)
    {
        $category_id = $request->get('category_id');
        $user_id = $request->get('user_id');

        if((empty($category_id) && $category_id !=0 ) || empty($user_id)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        $data['transactions'] = $this->dashboard_service->get_transactions_by_category_id_sorted($user_id, $category_id);
        $data['categories'] = $this->dashboard_service->get_active_categories();

        if(empty($data['transactions'])){
            $response = new Response(json_encode(
                array(
                    'status' => 1,
                    'html' => ''
                )
            ), Response::HTTP_OK);
            return $response;
        }

        $transactions = $this->render(
            'dashboard/transaction_list.html.twig',
            [
                'transactions' => $data['transactions'],
                'categories' => $data['categories']
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