<?php

namespace App\Controller;
use App\Entity\Sponsor;
use App\Form\SponsorType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SponsorController extends AbstractController
{
    /**
     * @Route("/admin/sponsors", name="sponsor_list")
     */
    public function sponsor_list()
    {
        $repository = $this->getDoctrine()->getRepository(Sponsor::class);
        $sponsors = $repository->findAll();

        return $this->render('sponsor/list.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    /**
     * @Route("/admin/sponsors/new", name="sponsor_new")
     */
    public function sponsor_new(Request $request)
    {
        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->add('save', SubmitType::class);
        // de facut verificari
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sponsor);
            $entityManager->flush();
            $this->addFlash('success', 'Sponsor \'' . $sponsor->getName() . '\' added successfully!');
            return $this->redirectToRoute('sponsor_list');
        }

        return $this->render('sponsor/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/sponsors/edit/{sponsor_id}", name="sponsor_edit")
     */
    public function sponsor_edit($sponsor_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Sponsor::class);
        $sponsor = $repository->find($sponsor_id);
        
        if(!$sponsor) {
            $this->addFlash('error', 'Sponsor not found!');
            return $this->redirectToRoute('sponsor_list');
        }

        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Sponsor \'' . $sponsor->getName() . '\' edited successfully!');
            return $this->redirectToRoute('sponsor_list');
        }

        return $this->render('sponsor/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/sponsors/remove/{sponsor_id}", name="sponsor_remove")
     */
    public function sponsor_remove($sponsor_id)
    {
        $repository = $this->getDoctrine()->getRepository(Sponsor::class);
        $sponsor = $repository->find($sponsor_id);

        if(!$sponsor) {
            $this->addFlash('error', 'Sponsor not found!');
            return $this->redirectToRoute('sponsor_list');
        }

        $oldName = $sponsor->getName();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sponsor);
        $entityManager->flush();
    
        $this->addFlash('success', 'Sponsor \'' . $oldName . '\' removed successfully!');

        return $this->redirectToRoute('sponsor_list');
    }
}
