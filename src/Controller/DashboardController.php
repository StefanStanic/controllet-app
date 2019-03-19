<?php
/**
 * Created by PhpStorm.
 * User: stefke
 * Date: 2019-03-19
 * Time: 19:37
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard()
    {
        return $this->render('dashboard/dashboard.html.twig');
    }

}