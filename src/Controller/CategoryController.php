<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories", name="category_list")
     */
    public function category_list()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/categories/new", name="category_new")
     */
    public function category_new(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->add('save', SubmitType::class);
        // de facut verificari
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category \'' . $category->getName() . '\' added successfully!');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categories/edit/{category_id}", name="category_edit")
     */
    public function category_edit($category_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($category_id);
        
        if(!$category) {
            $this->addFlash('error', 'Category not found!');
            return $this->redirectToRoute('category_list');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Category \'' . $category->getName() . '\' edited successfully!');
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/categories/remove/{category_id}", name="category_remove")
     */
    public function category_remove($category_id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($category_id);

        if(!$category) {
            $this->addFlash('error', 'Category not found!');
            return $this->redirectToRoute('category_list');
        }
        
        $oldName = $category->getName();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
    
        $this->addFlash('success', 'Category \'' . $oldName . '\' removed successfully!');

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
