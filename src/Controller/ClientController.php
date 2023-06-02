<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ClientType;
use App\Form\UserType;
use App\Form\UserType2Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/lister', name: 'app_client-lister')]
    public function lister(UserRepository $rep): Response
    {
        return $this->render('client/lister.html.twig', [
            'clients' => $rep->findAll()
        ]);
    }
    #[Route('/test', name: 'app_client-lister')]
    public function listere(UserRepository $rep): Response
    {
        return $this->render('base.html.twig');
    }

    #[Route('/client/{id<\d+>}', name: 'app_client_detail')]
    #[IsGranted("ROLE_ADMIN") ]
    #[IsGranted("ROLE_USER") ]


    public function detail(User $client, $id): Response
    {
        return $this->render('client/detail.html.twig', [
            'client' => $client
        ]);
    }

    #[Route('/admin/client/supprimer/{id<\d+>}', name: 'app_client_supprimer')]
    #[IsGranted("ROLE_ADMIN") ]
    #[IsGranted("ROLE_USER") ]
    public function supprimer(User $client, $id, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($client);
        $em->flush();

        return new Response("Suppression avec succès");
    }

    #[Route('/client/ajouter', name: 'app_ajouter-client')]
    public function ajouter(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request): Response
    {
        $client = new User();
        $form = $this->createForm(UserType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();

            $em = $doctrine->getManager();
            $em->persist($client);
            $em->flush();

            return new Response("Ajout client avec succès");
        }

        return $this->render('client/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/client/modifier/{id<\d+>}', name: 'app_modifier-client')]
    #[IsGranted("ROLE_USER") ]
    public function modifier(\Doctrine\Persistence\ManagerRegistry $doctrine, Request $request, User $client, $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType2Type::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the password field is filled
            $client = $form->getData();

            $em = $doctrine->getManager();
            $em->persist($client);
            $em->flush();
            return $this->redirectToRoute('app_client_detail', ['id' => $client->getId()]);

        }




        return $this->render('client/editform.html.twig', ['form' => $form->createView()]);
    }

}
