<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Booking;
use App\Entity\WaitingList;
use App\Form\UserType;
use App\Form\User2Type;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/admin/users", name="user_list")
     */
    public function user_list()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    ///**
    // * @Route("/admin/users/new", name="user_new")
    // */
    /*
    public function user_new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('save', SubmitType::class);
        // de facut verificari
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
            // de afisat mesaj
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
            'new_user' => true,
        ]);
    }
    */

    ///**
    // * @Route("/admin/users/edit/{user_id}", name="user_edit")
    // */
    /*public function user_edit($user_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($user_id);
        // de facut verificari
        $form = $this->createForm(User2Type::class, $user);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('user_list');
            // de afisat mesaj
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/

    /**
     * @Route("/admin/users/remove/{user_id}", name="user_remove")
     */
    public function user_remove($user_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($user_id);

        if(!$user) {
            $this->addFlash('error', 'User not found!');
            return $this->redirectToRoute('user_list');
        }

        $bookingRepo = $this->getDoctrine()->getRepository(Booking::class);
        $bookings = $bookingRepo->findOneBy(['user' => $user->getId()]);
        $waitingRepo = $this->getDoctrine()->getRepository(WaitingList::class);
        $waitings = $waitingRepo->findOneBy(['user' => $user->getId()]);
        if($bookings || $waitings) {
            $this->addFlash('error', 'This user has active tickets or he is on waiting lists. Remove them before deleting him!');
            return $this->redirectToRoute('user_list');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    
        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('user_list');
    }
}
