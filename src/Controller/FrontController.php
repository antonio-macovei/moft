<?php

namespace App\Controller;
use App\Entity\Ticket;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findAll();

        return $this->render('front/tickets.html.twig', [
            'tickets' => $tickets,
        ]);   
    }

    /**
     * @Route("/sponsors", name="sponsors")
     */
    public function sponsors()
    {
        return $this->render('front/sponsors.html.twig', [
            
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->add('save', SubmitType::class);
        // de facut verificari
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('category_list');
            // de afisat mesaj
        }

        return $this->render('front/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
