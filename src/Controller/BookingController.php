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

        return $this->render('front/my_account.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * @Route("/account/book/{ticket_id}", name="my_ticket_book")
     */
    public function ticket_book($ticket_id, \Swift_Mailer $mailer)
    {
        // Get requested ticket
        $ticketRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketRepo->find($ticket_id);

        if(!$ticket) {
            $this->addFlash('error', 'Biletul pe care ai incercat sa îl rezervi nu există!');
            return $this->redirectToRoute('tickets');
        }

        // Prepare the anchor name for redirect
        $anchor = strtolower($ticket->getName());
        $anchor = str_replace(' ', '-', $anchor);

        // Checks - ticket time has passed
        if($ticket->getTime()->getTimestamp() <= time()) {
            $this->addFlash('error', 'Nu se mai pot rezerva bilete pentru acest eveniment!');
            $this->addFlash('ticket', $ticket->getId());
            return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
        }

        // Checks - booking already exists
        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);
        $sameTicket = $bookingRepo->findOneBy(['user' => $this->getUser()->getId(), 'ticket' => $ticket]);
        if($sameTicket) {
            $this->addFlash('error', 'Ai mai rezervat acest bilet o dată!');
            $this->addFlash('ticket', $ticket->getId());
            return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
        }

        // Checks - no tickets left
        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);
        $countBookings = $bookingRepo->countAllForTicket($ticket);
        if($countBookings >= $ticket->getMaxTickets()) {
            $this->addFlash('error', 'Nu mai există bilete disponibile!');
            $this->addFlash('ticket', $ticket->getId());
            return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
        }

        // Checks - booking twice in the same category
        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);
        $sameCategoryBookings = $bookingRepo->findBy(['user' => $this->getUser()->getId()]);
        if($sameCategoryBookings) {
            foreach($sameCategoryBookings as $sameCategoryBooking) {
                if($sameCategoryBooking->getTicket()->getCategory() == $ticket->getCategory()) {
                    $this->addFlash('error', 'Ai mai rezervat un bilet din această categorie!');
                    $this->addFlash('ticket', $ticket->getId());
                    return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
                }
            }
        }

        // Create the booking
        $user = $this->getUser();
        $booking = new Booking();
        $booking->setTicket($ticket);
        $booking->setUser($user);
        $booking->setCode(md5("ticket" . $ticket->getId() . $this->getUser()->getEmail() . rand(0,3000)));
        $booking->setTime(new \DateTime());

        // Save the new booking in DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        $entityManager->flush();

        $from = "moft@lsacbucuresti.ro";
        $to = $this->getUser()->getEmail();
        $subject = 'MOFT - Confirmare rezervare';
        $message = 'Biletul tau la ' . $ticket->getName() . '';
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    'emails/booking_confirmation.html.twig',
                    array('ticket' => $ticket, 'confirmation' => $booking->getCode())
                ),
                'text/html'
            );
        $mailer->send($mail);

        // Return success message
        $this->addFlash('success', 'Ai rezervat cu succes biletul!');
        $this->addFlash('ticket', $ticket->getId());
        return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
    }

    /**
     * @Route("/account/cancel/{booking_id}", name="my_ticket_remove")
     */
    public function ticket_remove($booking_id)
    {
        // Get the requested booking
        $repository = $this->getDoctrine()->getRepository(Booking::class);
        $booking = $repository->find($booking_id);

        $ticket = $booking->getTicket();
        
        // Remove the booking from the DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Biletul tău la ' . $ticket->getName() . ' a fost anulat cu succes!');
        return $this->redirectToRoute('my_account');
    }
}
