<?php


namespace App\Controller;


use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $admin_service;

    public function __construct(AdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin()
    {

        $userCount = $this->admin_service->get_user_count()[0]['user_count'];
        $accountCount = $this->admin_service->get_account_count()[0]['account_count'];
        $billCount = $this->admin_service->get_bill_count()[0]['bill_count'];
        $transactionCount = $this->admin_service->get_transaction_count()[0]['transaction_count'];

        return $this->render(
            'admin/admin.html.twig',
            array(
                'user_count' => $userCount,
                'account_count' => $accountCount,
                'transaction_count' => $transactionCount,
                'bill_count' => $billCount
            )
        );
    }

    /**
     * @Route("/users", name="app_users")
     */
    public function users()
    {

        $users = $this->admin_service->get_all_users();

        return $this->render(
            'admin/users.html.twig',
            array(
                'users' => $users
            )
        );
    }

    /**
     * @Route("/userStatusSwitch", name="app_user_status_switch", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function switch_user_status(Request $request)
    {
        $user_id = $request->get('user_id');
        $active = $request->get('active');

        $result = $this->admin_service->change_user_status($user_id, $active);

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
     * @Route("/cashFlow", name="app_cashflow", methods={"POST"})
     * @param Request $request
     * @return bool|Response
     */
    public function get_total_cashflow_by_category(Request $request)
    {
        $type = $request->get('type');

        if(!$type){
            return false;
        }

        $result = $this->admin_service->get_total_cashflow_by_category($type);

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'data' => $result
            )
        ), Response::HTTP_OK);

        return $response;

    }

}