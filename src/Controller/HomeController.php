<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home_page")
     */
    public function homepage()
    {
        return $this->render(
            'homepage/homepage.html.twig'
        );
    }
}