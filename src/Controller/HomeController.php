<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SessionInterface $session,ProduitRepository $rep): Response
    {
        $panier =$session->get("panier",[])  ;
        $totalProducts=0;
        foreach ($panier as $id=>$quantite)
        {

            $totalProducts++;

        }

        return $this->render('home/index.html.twig', [
            'produits' => $rep->findAll(),
            'totalProducts'=>$totalProducts
        ]);
    }
    #[Route('/home/listerall', name: 'app_home-listerall')]
    public function lister(ProduitRepository $rep,SessionInterface $session): Response
    {
        $panier =$session->get("panier",[])  ;
        $totalProducts=0;
        foreach ($panier as $id=>$quantite)
        {

            $totalProducts++;

        }
        return $this->render('home/listerprd.html.twig', [
            'produits' => $rep->findAll(),
            'totalProducts'=>$totalProducts
        ]);
    }
    #[Route('/home/detail/{id<\d+>}', name: 'product_details')]
    public function detail(Produit $prod,SessionInterface $session): Response
    {

        $panier =$session->get("panier",[])  ;
        $totalProducts=0;
        foreach ($panier as $id=>$quantite)
        {

            $totalProducts++;

        }
        return $this->render('home/detailprd.html.twig', [
            'product' => $prod,'totalProducts'=>$totalProducts
        ]);
    }
    #[Route('/home/cat/{id<\d+>}', name:' produitparcategorie')]

    public function produitparcategorie($id,Categorie $cat,SessionInterface $session): Response
    {
        $produits=$cat->getProduits();

        $panier =$session->get("panier",[])  ;
        $totalProducts=0;
        foreach ($panier as $id=>$quantite)
        {

            $totalProducts++;

        }

        return $this->render('home/listerprd.html.twig', ['totalProducts' =>$totalProducts,]);
    }

    #[Route('/home/cats', name:' cats-lister')]

    public function categorielister(CategorieRepository $rep ): Response
    {
        $categories=$rep->findAll();

        return $this->render('home/navbar2.html.twig', ['categories' => $categories]);
    }


    #[Route('/listercat/{id<\d+>}', name: 'lister-cat-id')]

    public function listcat(SessionInterface $session,Categorie $cat ): Response
    {
        $panier =$session->get("panier",[])  ;
        $totalProducts=0;
        foreach ($panier as $id=>$quantite)
        {

            $totalProducts++;

        }



        return $this->render('home/listerprd.html.twig',['produits'=>$cat->getProduits(),'totalProducts'=>$totalProducts]) ;   }

}
