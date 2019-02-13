<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Category;
use App\Entity\Booking;
use App\Entity\WaitingList;
use App\Entity\User;
use App\Form\TicketType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

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

        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);

        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
            'bookingRepo' => $bookingRepo,
        ]);
    }

    /**
     * @Route("/admin/tickets/ticket/{ticket_id}", name="list_ticket_users")
     */
    public function list_ticket_users($ticket_id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);
        if(!$ticket) {
            $this->addFlash('error', 'Ticket not found!');
            return $this->redirectToRoute('ticket_list');
        }

        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findBy(array('ticket' => $ticket));

        $repository = $this->getDoctrine()->getRepository(WaitingList::class);
        $waitings = $repository->findBy(array('ticket' => $ticket));

        return $this->render('ticket/list_users.html.twig', [
            'bookings' => $bookings,
            'waitings' => $waitings,
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/admin/tickets/user/{user_id}", name="list_user_tickets")
     */
    public function list_user_tickets($user_id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($user_id);
        if(!$user) {
            $this->addFlash('error', 'User not found!');
            return $this->redirectToRoute('user_list');
        }

        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findBy(array('user' => $user));

        $repository = $this->getDoctrine()->getRepository(WaitingList::class);
        $waitings = $repository->findBy(array('user' => $user));

        return $this->render('ticket/list_user_tickets.html.twig', [
            'bookings' => $bookings,
            'waitings' => $waitings,
            'user' => $user,
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
            $this->addFlash('success', 'Ticket \'' . $ticket->getName() . '\' added successfully!');
            return $this->redirectToRoute('ticket_list');
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
        
        if(!$ticket) {
            $this->addFlash('error', 'Ticket not found!');
            return $this->redirectToRoute('ticket_list');
        }

        $form = $this->createForm(TicketType::class, $ticket);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Ticket \'' . $ticket->getName() . '\' edited successfully!');
            return $this->redirectToRoute('ticket_list');
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

        if(!$ticket) {
            $this->addFlash('error', 'Ticket not found!');
            return $this->redirectToRoute('ticket_list');
        }

        $oldName = $ticket->getName();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ticket);
        $entityManager->flush();
    
        $this->addFlash('success', 'Ticket \'' . $oldName . '\' removed successfully!');

        return $this->redirectToRoute('ticket_list');
    }

    /**
     * @Route("/admin/tickets/switch/{ticket_id}", name="ticket_switch")
     */
    public function ticket_switch($ticket_id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);

        if(!$ticket) {
            $this->addFlash('error', 'Ticket not found!');
            return $this->redirectToRoute('ticket_list');
        }

        $oldAvailability = $ticket->getAvailable();
        $ticket->setAvailable(!$oldAvailability);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        if($oldAvailability) {
            $this->addFlash('error', 'Ticket \'' . $ticket->getName() . '\' is now NOT AVAILABLE!');
        } else {
            $this->addFlash('success', 'Ticket \'' . $ticket->getName() . '\' is now AVAILABLE!');
        }

        return $this->redirectToRoute('ticket_list');
    }

    /**
     * @Route("/admin/tickets/download/{ticket_id}/ticket-users.csv")
     */
    /**
     * @Route("/admin/tickets/download/{ticket_id}/ticket-users.csv", name="download_users_for_ticket")
     */
    public function download_users_for_ticket($ticket_id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);
        if(!$ticket) {
            $this->addFlash('error', 'Ticket not found!');
            return $this->redirectToRoute('ticket_list');
        }

        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findBy(array('ticket' => $ticket));

        $repository = $this->getDoctrine()->getRepository(WaitingList::class);
        $waitings = $repository->findBy(array('ticket' => $ticket));

        $rows = array();
        $data = array("ID", "Nume spectacol", "Nume", "Prenume", "Telefon", "Universitate", "Facultate", "ID Student", "Facebook");
        $rows[] = implode(',', $data);
        
        foreach ($bookings as $booking) {
            $data = array($booking->getId(),
                            $booking->getTicket()->getName(),
                            $booking->getUser()->getLastName(),
                            $booking->getUser()->getFirstName(),
                            $booking->getUser()->getPhone(),
                            $booking->getUser()->getUniversity(),
                            $booking->getUser()->getFaculty(),
                            $booking->getUser()->getStudentId(),
                            $booking->getUser()->getFacebook());
            $rows[] = implode(',', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }

}
