<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route('/cart/removeall', name: 'cart-removeall')]
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

}
