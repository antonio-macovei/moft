<?php

namespace App\Controller;
use App\Entity\Blacklist;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlackListController extends AbstractController
{

     /**
     * @Route("/admin/blacklist", name="blacklist_list")
     */
    public function blacklist_list()
    {
        $repository = $this->getDoctrine()->getRepository(Blacklist::class);
        $blacklist = $repository->findAll();

        return $this->render('black_list/list.html.twig', [
            'blacklist' => $blacklist,
        ]);
    }

    /**
     * @Route("/admin/blacklist/add/{user_id}", name="blacklist_add")
     */
    public function blacklist_add($user_id)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($user_id);

        if(!$user) {
            $this->addFlash('error', 'User not found!');
            return $this->redirectToRoute('blacklist_list');
        }

        $blacklist = new Blacklist();
        $blacklist->setUser($user);
        $blacklist->setReason("Neprezentare");
        $blacklist->setTime(new \DateTime);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($blacklist);
        $entityManager->flush();
    
        $this->addFlash('success', $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getEmail() . ') was added on the blacklist successfully!');
        return $this->redirectToRoute('blacklist_list');
    }

    /**
     * @Route("/admin/blacklist/remove/{blacklist_id}", name="blacklist_remove")
     */
    public function blacklist_remove($blacklist_id)
    {
        $repository = $this->getDoctrine()->getRepository(Blacklist::class);
        $blacklist = $repository->find($blacklist_id);

        if(!$blacklist) {
            $this->addFlash('error', 'Blacklist item not found!');
            return $this->redirectToRoute('blacklist_list');
        }
        $user = $blacklist->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($blacklist);
        $entityManager->flush();
    
        $this->addFlash('success', $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getEmail() . ') was removed from the blacklist successfully!');
        return $this->redirectToRoute('blacklist_list');
    }
}
