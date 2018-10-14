<?php

namespace App\Controller;
use App\Entity\Ticket;
use App\Entity\Booking;
use App\Entity\Sponsor;
use App\Entity\WaitingList;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Sponsor::class);
        $sponsors = $repository->findAll();
        return $this->render('front/index.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    /**
     * @Route("/tickets", name="tickets")
     */
    public function tickets()
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy([], ['category' => 'ASC']);

        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);
        $waitingListRepo = $this->getDoctrine()->getRepository(WaitingList::class);

        return $this->render('front/tickets.html.twig', [
            'tickets' => $tickets,
            'bookingRepo' => $bookingRepo,
            'waitingListRepo' => $waitingListRepo,
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
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createFormBuilder()
            ->add('email', TextType::class, array(
                'attr' => array('placeholder' => 'Email'),
                'constraints' => new Length(array('min' => 3)),
            ))
            ->add('subject', TextType::class, array(
                'attr' => array('placeholder' => 'Subiect'),
                'constraints' => new Length(array('min' => 3)),
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array('placeholder' => 'Mesajul tău...'),
                'constraints' => new Length(array('min' => 5)),
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Trimite mesajul',
            ))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $to = "antoniomacovei@yahoo.com"; // de pus moft@lsacbucuresti.ro

            $data = $form->getData();
            $from = $data['email'];
            $subject = $data['subject'];
            $message = $data['message'];

            $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($message);
            $mailer->send($mail);
            $this->addFlash('success', 'Mesajul tau a fost trimis cu succes!');
            return $this->redirectToRoute('contact');
        }

        return $this->render('front/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
