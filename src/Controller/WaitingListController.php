<?php

namespace App\Controller;
use App\Entity\Booking;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\WaitingList;

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

    /**
     * @Route("/waiting-list/add/{user_id}/{ticket_id}", name="waiting_list_add")
     */
    public function waiting_list_add($user_id, $ticket_id)
    {
        // Get requested ticket
        $ticketRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketRepo->find($ticket_id);

        // Prepare the anchor name for redirect
        $anchor = strtolower($ticket->getName());
        $anchor = str_replace(' ', '-', $anchor);

        // Get requested user
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->find($user_id);

        // Create the waiting list slot
        $slot = new WaitingList();
        $slot->setTicket($ticket);
        $slot->setUser($user);
        $slot->setCode(md5("ticket" . $ticket->getId() . $this->getUser()->getEmail() . rand(0,3000)));
        $slot->setTime(new \DateTime());

        // Save the new slot in DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($slot);
        $entityManager->flush();

        // Return success message
        $this->addFlash('notice', 'Ai fost adăugat pe lista de așteptare. De îndată ce se
                        va elibera un loc, vei fi notificat pe email cu datele biletulu tău!');
        $this->addFlash('ticket', $ticket->getId());
        return $this->redirectToRoute('tickets', ['_fragment' => $anchor]);
    }

    /**
     * @Route("/waiting-list/move/{ticket_id}", name="waiting_list_move")
     */
    public function waiting_list_move($ticket_id, \Swift_Mailer $mailer)
    {
        // Get requested ticket
        $ticketRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketRepo->find($ticket_id);

        // Get requested user
        $waitingListRepo = $this->getDoctrine()->getRepository(WaitingList::class);
        $waitingSlots = $waitingListRepo->findBy(array('ticket' => $ticket), array('time' => "DESC"));
        if(!$waitingSlots || count($waitingSlots) == 0) {
            // There are no users on the waiting list so nothing to be done
            // Return success message to original user
            $this->addFlash('success', 'Biletul tău la ' . $ticket->getName() . ' a fost anulat cu succes!');
            return $this->redirectToRoute('my_account');
        }

        $slot = reset($waitingSlots); // Get the first in the waiting list

        // Create the booking
        $booking = new Booking();
        $booking->setTicket($slot->getTicket());
        $booking->setUser($slot->getUser());
        $booking->setCode($slot->getCode());
        $booking->setTime(new \DateTime());

        // Save the new booking in DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        $entityManager->remove($slot);
        $entityManager->flush();

        // Send confirmation email
        $from = "moft@lsacbucuresti.ro";
        $to = $slot->getUser()->getEmail();
        $subject = 'MOFT - Confirmare rezervare (lista de așteptare)';
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->renderView(
                    'emails/booking_confirmation_from_waiting.html.twig',
                    array('ticket' => $ticket, 'confirmation' => $booking->getCode())
                ),
                'text/html'
            );
        $mailer->send($mail);

        // Return success message to original user
        $this->addFlash('success', 'Biletul tău la ' . $ticket->getName() . ' a fost anulat cu succes!');
        return $this->redirectToRoute('my_account');
    }

    /**
     * @Route("/waiting-list/cancel/{waiting_id}", name="waiting_list_remove")
     */
    public function waiting_list_remove($waiting_id)
    {
        // Get the requested waiting slot
        $repository = $this->getDoctrine()->getRepository(WaitingList::class);
        $waiting = $repository->find($waiting_id);

        $ticket = $waiting->getTicket();
        
        // Remove the waiting from the DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($waiting);
        $entityManager->flush();

        $this->addFlash('success', 'Biletul tău în așteptare la ' . $ticket->getName() . ' a fost anulat cu succes!');
        return $this->redirectToRoute('my_account');
    }
}
