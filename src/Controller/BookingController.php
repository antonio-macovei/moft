<?php

namespace App\Controller;
use App\Entity\Booking;
use App\Entity\Ticket;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking", name="booking")
     */
    public function index()
    {
        return $this->render('booking/index.html.twig', [
            'controller_name' => 'BookingController',
        ]);
    }

     /**
     * @Route("/tickets/my-tickets", name="my_ticket_list")
     */
    public function my_ticket_list()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findBy(array("user" => $user));

        return $this->render('booking/list.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("/tickets/book/{ticket_id}", name="my_ticket_book")
     */
    public function my_ticket_book($ticket_id)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($ticket_id);
        $user = $this->getUser();
        $booking = new Booking();
        $booking->setTicket($ticket);
        $booking->setUser($user);
        $booking->setCode("test".rand(0,500));
        $booking->setTime(new \DateTime());
       
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        $entityManager->flush();
        return $this->redirectToRoute('my_ticket_list');
    }

    /**
     * @Route("/tickets/cancel/{booking_id}", name="my_ticket_remove")
     */
    public function my_ticket_remove($booking_id)
    {
        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $booking = $repository->find($booking_id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($booking);
        $entityManager->flush();

        return $this->redirectToRoute('my_ticket_list');
    }
}
