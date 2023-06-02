<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart-add')]


    public function add(SessionInterface $session,$id,ProduitRepository $rep)
    {

        $id=$rep->find($id)->getId();
        $panier = $session->get("panier", []);

        if(!empty($panier[$id]))        {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute('app_home');
    }
    #[Route('/cart/add2/{id}', name: 'cart-add2')]


    public function add2(SessionInterface $session,$id,ProduitRepository $rep)
    {

        $id=$rep->find($id)->getId();
        $panier = $session->get("panier", []);

        if(!empty($panier[$id]))        {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute('cart-show');
    }

    #[Route('/cart/remove/{id}', name: 'cart-remove')]

	public function removebyid(Produit $produit, SessionInterface $session)
    {
        $panier = $session->get("panier", []);
        $id = $produit->getId();

if(!empty($panier[$id]))        {
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute('cart-show');
    }
    #[Route('/cart/removerow/{id}', name: 'cart-removerow')]
    public function removerow(Produit $produit, SessionInterface $session)
    {
$panier = $session->get("panier", []);
$id = $produit->getId();

if(!empty($panier[$id]))        {
unset($panier[$id]);
}
$session->set("panier", $panier);
        return $this->redirectToRoute('cart-show');
}
    #[Route('/cart/removeall', name: 'cart-delete')]
    public function removeall( SessionInterface $session)
    {
       $session->set('panier',[]);
        return $this->redirectToRoute("cart-show");
    }




    #[Route('/cart/show', name: 'cart-show')]

    public function affichercart(SessionInterface $session,ProduitRepository $rep)
    {
       $panier =$session->get("panier",[])  ;
       $datapanier=[];
       $total=0;
       $totalProducts=0;
       foreach ($panier as $id=>$quantite)
       {
           $produit=$rep->find($id);
           $datapanier[]=[
               'produit'=>$produit,
               'qte'=>$quantite
           ];
           $total+=$produit->getPrix()*$quantite;
           $totalProducts++;

       }

        return $this->render('cart/cart.html.twig',['datapanier'=>$datapanier,"total"=>$total,"totalProducts"=>$totalProducts]);
    }

    #[Route('/cart/valider', name: 'cart-valider')]

    public function validercommande(Security $security, SessionInterface $session,ProduitRepository $rep,\Doctrine\Persistence\ManagerRegistry $doctrine)
    {
        $user = $security->getUser();

        if ($user) {
            $panier = $session->get("panier", []);

            $com = [];
            $total = 0;
            $totalProducts = 0;
            foreach ($panier as $id=>$quantite)
            {


                $produit=$rep->find($id);
                $produit->setQteStock($produit->getQteStock()-$quantite);
                $em=$doctrine->getManager();
                $em->persist($produit);

                $com[]=[
                    'produit'=>$produit,
                    'qte'=>$quantite,
                    'datecomm'=>new \DateTime(),
               'user'=>$user

           ];
           $total+=$produit->getPrix()*$quantite;
           $totalProducts++;

       }
            $em->flush();

            $i=0;
            $d=[];

          while($i<count($com))
          {
             $commande=new Commande();
             $commande->setQte($com[$i]['qte'])
                 ->setProduit($com[$i]['produit'])
                 ->setUser($com[$i]['user'])
                 ->setDatecommande($com[$i]['datecomm']);
             $em=$doctrine->getManager();
             $em->persist($commande);
              $i++;

          }
            $em->flush();



            }else
        { return $this->redirectToRoute('app_login');

        }

        $session->set('panier',[]);




return  $this->redirectToRoute('app_home');
    }

    #[Route('/cart/checkout', name: 'cart-checkout')]

    public function checkout(Security $security, SessionInterface $session,ProduitRepository $rep)
    {
        $user = $security->getUser();

        if ($user) {
            $panier = $session->get("panier", []);

            $datapanier = [];
            $total = 0;
            $totalProducts = 0;
            foreach ($panier as $id=>$quantite)
            {

                $produit=$rep->find($id);
                $com[]=[
                    'produit'=>$produit,
                    'qte'=>$quantite,
                    'datecomm'=>new \DateTime(),
                    'user'=>$user

                ];
                $total+=$produit->getPrix()*$quantite;
                $totalProducts++;

            }
            $i=0;
$commandes=[];
            while($i<count($com))
            {
                $commande=new Commande();
                $commande->setQte($com[$i]['qte'])
                    ->setProduit($com[$i]['produit'])
                    ->setUser($com[$i]['user'])
                    ->setDatecommande($com[$i]['datecomm']);

                $i++;
$commandes[]=$commande;
            }




        }else
        { return $this->redirectToRoute('app_login');

        }



        return $this->render('cart/commandeinfo.html.twig',['commandes'=>$commandes,"total"=>$total,"totalProducts"=>$totalProducts]);
    }








}
