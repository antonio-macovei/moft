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
     * @Route("/account/my-account", name="my_account")
     */
    public function my_account()
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $repository->findBy(array("user" => $user));

        return $this->render('front/my_tickets.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("/account/book/{ticket_id}", name="my_ticket_book")
     */
    public function ticket_book($ticket_id)
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
     * @Route("/account/cancel/{booking_id}", name="my_ticket_remove")
     */
    public function ticket_remove($booking_id)
    {
        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $booking = $repository->find($booking_id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($booking);
        $entityManager->flush();

        return $this->redirectToRoute('my_ticket_list');
    }
}
