<?php
/**
 * Created by PhpStorm.
 * User: stefke
 * Date: 2019-03-19
 * Time: 19:37
 */

namespace App\Controller;


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

        return $this->render(
            'dashboard/dashboard.html.twig',
            array(
                'accounts' => $data['accounts']
            )
        );
    }

    /**
     * @Route("/deleteAccount", name="delete_account", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteWallet(Request $request)
    {
        $account_id = $request->get('account_id');

        $wallet_id = $this->dashboard_service->delete_account($account_id);

        if($wallet_id){
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




}