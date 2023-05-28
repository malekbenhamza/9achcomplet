<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/admin/cat/ajouter', name: 'app_ajouter-cat')]
    #[IsGranted("ROLE_ADMIN") ]
    public function ajouter(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request): Response
    {
        $categorie=new Categorie();
        $form=$this->createForm(CategorieType::class,$categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() )
        {
            $categorie=$form->getData();

            $em=$doctrine->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("lister-cat");

        }
        return $this->render('categorie/form.html.twig',['form'=>$form]);
    }
    #[Route('/admin/cat/modifier/{id<\d+>}', name: 'app_modifier-cat')]
    #[IsGranted("ROLE_ADMIN") ]


    public function modifier(Categorie $categorie ,Request $request,\Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $form=$this->createForm(CategorieType::class,$categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() )
        {
            $categorie=$form->getData();

            $em=$doctrine->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("lister-cat");

        }
        return $this->render('categorie/form.html.twig',['form'=>$form]);
    }
    #[Route('/admin/cat/lister', name: 'lister-cat')]
    #[IsGranted("ROLE_ADMIN") ]


    public function lister(CategorieRepository $rep ): Response
    {
        return $this->render("categorie/lister.html.twig", ['categories' => $rep->findAll()]);
    }
    #[Route('/admin/cat/supprimer/{id<\d+>}', name: 'supp-cat')]
    #[IsGranted("ROLE_ADMIN") ]


    public function supprimer(Categorie $cat, \Doctrine\Persistence\ManagerRegistry $doc ): Response
    {
        $em=$doc->getManager();
        $em->remove($cat);
        $em->flush();
        return $this->redirectToRoute("lister-cat");
    }



}

