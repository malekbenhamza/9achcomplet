<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\RecherchebynomType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

    #[Route('/admin/prd/lister', name: 'app_produit-lister')]
    #[IsGranted("ROLE_ADMIN") ]



    public function lister(Request $request, ProduitRepository $rep): Response
    {
        $form = $this->createForm(RecherchebynomType::class);
        $form->handleRequest($request);

        $produits = $rep->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
            $produits = $rep->findByNom($nom);
        }

        return $this->render('produit/lister.html.twig', [
            'form' => $form,
            'produits' => $produits
        ]);
    }



    #[Route('/prd/{id<\d+>}', name: 'app_produit_detail')]
    #[IsGranted("ROLE_ADMIN") ]


    public function detail(Produit $prd,$id): Response
    {
        return $this->render('produit/detail.html.twig', [
            'produit' => $prd
        ]);
    }
    #[Route('/admin/prd/supprimer/{id<\d+>}', name: 'app_produit_supprimer')]
    #[IsGranted("ROLE_ADMIN") ]


    public function supprimer(Produit $prd,$id,\Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $em=$doctrine->getManager();
        $em->remove($prd);
        $em->flush();
        return $this->redirectToRoute('app_produit-lister');
    }
    #[Route('/admin/prd/ajouter', name: 'app_ajouter')]
    #[IsGranted("ROLE_ADMIN") ]

    public function ajouter(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request): Response
    {
        $produit=new Produit();
        $form=$this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);

        if
        ($form->isSubmitted() && $form->isValid() )
        {
            $produit=$form->getData();

            $em=$doctrine->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit-lister');

        }
        return $this->render('produit/form.html.twig',['form'=>$form]);
    }
    #[Route('/admin/prd/modifier/{id<\d+>}', name: 'app_modifier-produit')]
    #[IsGranted("ROLE_ADMIN") ]


    public function modifier(\Doctrine\Persistence\ManagerRegistry $doctrine,Request $request,Produit $produit,$id): Response
    {
        $form=$this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);

        if
        ($form->isSubmitted() && $form->isValid() )
        {
            $produit=$form->getData();

            $em=$doctrine->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit-lister');
        }
        return $this->render('produit/form.html.twig',['form'=>$form]);
    }




}
