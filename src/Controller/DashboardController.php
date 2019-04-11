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
use Symfony\Component\Form\FormError;
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

        return $this->render(
            'dashboard/dashboard.html.twig',
            array(
                'accounts' => $data['accounts']
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
}