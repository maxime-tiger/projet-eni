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
                //refresh pour ne pas etre deconnecte immediattement
                $EntityManager->refresh($user);
                $entityManager->flush();
                //Message
                $this->addflash('sucess','votre profil a ete modifie avec succes!');

            }
            return $this->render('user/profilConnectedUser.html.twig', ['userProfilForm'=>$form->createView()]);
        }
        //si l user souhaite accedeer au profil d un utilisateur
        $user = $entityManager->getRepository('App:User')->findOneby(['username' =>$request->get('username')]);
        return $this->render('user/profil.html.twig', ['user'=>$user]);

    }

}
