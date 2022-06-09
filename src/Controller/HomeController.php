<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;



class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage_index")
     * 
     */
    public function homepage():Response
    {
        return $this->render('default/homepage.html.twig');
    }

}
