<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WaitingListController extends AbstractController
{
    /**
     * @Route("/waiting/list", name="waiting_list")
     */
    public function index()
    {
        return $this->render('waiting_list/index.html.twig', [
            'controller_name' => 'WaitingListController',
        ]);
    }
}
