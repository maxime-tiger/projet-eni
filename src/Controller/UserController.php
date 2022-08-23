<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function profil(Request $request ,EntityManagerInterface $EntityManager, UserPasswordEncoderInterface $passwordEncoder, 
    GuardAuthentificatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        //recuperre les données de l'user en fonction d el url
        $user =$entityManager->getRepository('App:Participant')->findOneBy(['username'=> $request->get('username')]);
        //si l'utilisateur connecte souhaite acceder a son profil
        if($request->get('username')===$this->getUser()->getUsername()){

            //creation du formulaire
            $form = $this->createForm(UserProfilType::class , this->getUser());
            $form = handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //Encodage du mot de passe
                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            }
        }

    }



}
