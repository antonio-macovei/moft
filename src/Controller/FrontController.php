<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('front/index.html.twig', [
            
        ]);
    }

    /**
     * @Route("/tickets", name="tickets")
     */
    public function tickets()
    {
        return $this->render('front/index.html.twig', [
            
        ]);
    }

    /**
     * @Route("/sponsors", name="sponsors")
     */
    public function sponsors()
    {
        return $this->render('front/index.html.twig', [
            
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('front/index.html.twig', [
            
        ]);
    }
}
