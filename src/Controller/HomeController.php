<?php


namespace App\Controller;


use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $mailer;
    private $adminService;

    public function __construct(\Swift_Mailer $mailer, AdminService $adminService)
    {
        $this->mailer = $mailer;
        $this->adminService = $adminService;
    }

    /**
     * @Route("/", name="app_home_page")
     */
    public function homepage()
    {
        return $this->render(
            'homepage/homepage.html.twig'
        );
    }

    /**
     * @Route("/sendContactFormData", name="app_contact_form", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function sendContactFormData(Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $subject = $request->get('subject');
        $message = $request->get('message');

        if(empty($name) || empty($email) || empty($subject) || empty($message)){
            $response = new Response(json_encode(
                array(
                    'status' => 0,
                    'text' => 'Missing parameters'
                )
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
            return $response;
        }

        //send email confirmation
        $message = (new \Swift_Message($subject))
            ->setFrom($email)
            ->setTo('vts.stefan.stanic@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/contact_form.html.twig',
                    [
                        'message' => $message,
                        'name' => $name
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'text' => 'Contact form successfully sent.'
            )
        ), Response::HTTP_OK);

        return $response;
    }

    /**
     * @Route("/getHomePageStatistics", name="app_homepage_statitics", methods={"GET"})
     */
    public function getHomePageStatistics()
    {
        $userCount = $this->adminService->get_user_count()[0]['user_count'];
        $accountCount = $this->adminService->get_account_count()[0]['account_count'];
        $billCount = $this->adminService->get_bill_count()[0]['bill_count'];
        $transactionCount = $this->adminService->get_transaction_count()[0]['transaction_count'];

        $response = new Response(json_encode(
            array(
                'status' => 1,
                'users' => $userCount,
                'accounts' => $accountCount,
                'bills' => $billCount,
                'transactions' => $transactionCount
            )
        ), Response::HTTP_OK);

        return $response;

    }


}