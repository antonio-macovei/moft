<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Category;
use App\Entity\Booking;
use App\Form\TicketType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketsController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findAll();

        return $this->render('ticket/dashboard.html.twig', [
            'tickets' => count($tickets),
            'bookings' => count($bookings),
        ]);
    }

    /**
     * @Route("/admin/tickets", name="ticket_list")
     */
    public function ticket_list()
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findAll();

        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * @Route("/admin/tickets/new", name="ticket_new")
     */
    public function ticket_new(Request $request)
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->add('save', SubmitType::class);
        // de facut verificari
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();
            return $this->redirectToRoute('ticket_list');
            // de afisat mesaj
        }

        return $this->render('ticket/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/tickets/edit/{ticket_id}", name="ticket_edit")
     */
    public function ticket_edit($ticket_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);
        // de facut verificari
        $form = $this->createForm(TicketType::class, $ticket);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('ticket_list');
            // de afisat mesaj
        }

        return $this->render('ticket/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/tickets/remove/{ticket_id}", name="ticket_remove")
     */
    public function ticket_remove($ticket_id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);

        // de facut verificari

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ticket);
        $entityManager->flush();
    
        // de afisat mesaj

        return $this->redirectToRoute('ticket_list');
    }

}
