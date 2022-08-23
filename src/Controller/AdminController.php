<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Filters\NameFilter;
use App\Form\NameFilterType;
use App\Form\CampusType;
use App\Form\UserRegisterType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
* Class AdminController
* @Route(path="/admin", name="admin_")
*/
class AdminController extends AbstractController
{
    //créer un campus
    /**
     * @Route(path="/", name="")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CampusRepository $campusRepository
     * @return Response
     */
    public function campus(EntityManagerInterface $entityManager, Request $request, CampusRepository $campusRepository): Response
    {
        //Instanciation de l'objet campus, et des filtres
        $campus = new Campus();
        $text = new NameFilter();

        //Formulaire de recherche
        $filter = $this->createForm(NameFilterType::class, $text);
        $filter->handleRequest($request);

        $campusList = $campusRepository->findName($text);

        //Formulaire d'ajout
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Persistance des données
            $entityManager->persist($campus);
            $entityManager->flush();
            //Si le formulaire est valide, actualisation de la page
            return $this->redirectToRoute('admin_campus', ['campusList' => $campusList]);
        }
        return $this->render('admin/campus.html.twig',
            [
                'filter' => $filter->createView(),
                'campusForm' => $form->createView(),
                'campusList' => $campusList
            ]);
    }



    //Modifier le nom d'un campus

    /**
     * @Route(path="/{id}" , name="")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function modifierCampus(EntityManagerInterface $entityManager, Request $request): Response
    {
        //Récupération d'un campus, par son ID
        $campus = $entityManager->getRepository(Campus::class)->find($request->get('id'));

        //Formulaire de modification du campus
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        //Si le formulaire est valide, renvoie vers la liste des campus avec message
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le campus a bien été modifié !');

            //Récupération de tous les campus pour renvoyer a la vue
            $campusList = $entityManager->getRepository(Campus::class)->findAll();
            return $this->redirectToRoute('admin_campus', [
                'campusForm' => $campusForm->createView(),
                'campusList' => $campusList
            ]);
        }
        return $this->render("campus/edit.html.twig", [
                'campusForm' => $campusForm->createView()]
        );

    }


    // Supprime un campus

    /**
     * @Route(path="/{id}" , name="")
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteCampus(EntityManagerInterface $entityManager, int $id): RedirectResponse
    {
        //Récupère le campus en fonction de l'ID, puis on le supprime de la BDD
        $entityManager->remove($campus = $entityManager->getRepository(Campus::class)->find($id));
        $entityManager->flush();
        $this->addFlash('success', 'Le campus a bien été supprimé !');

        return $this->redirectToRoute('admin_campus');
    }

    // Ajouter une ville

    /**
     * @Route(path="", name="")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param VilleRepository $villeRepository
     * @return Response
     */
    public function city(EntityManagerInterface $entityManager, Request $request, VilleRepository $villeRepository): Response
    {

        //Formulaire de recherche
        $text = new NameFilter();
        $filter = $this->createForm(NameFilterType::class, $text);
        $filter->handleRequest($request);
        //Si le form de filtre est valid et soumis, je fais la recherche
        $villeList = $villeRepository->findName($text);

        //Formulaire d'ajout d'une ville
        $city = new Ville();
        $form = $this->createForm(VilleType::class, $city);
        $form->handleRequest($request);
        //Si le formulaire d'ajout est valide, actualise la liste des villes + message
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a bien été ajoutée !');
            return $this->redirectToRoute('admin_villes', ['villeList' => $villeList]);
        }
        return $this->render('admin/city.html.twig',
            [
                'filter' => $filter->createView(),
                'villeForm' => $form->createView(),
                'villeList' => $villeList
            ]);
    }

    //Modifier le nom d'une ville

    /**
     * @Route(path="", name="")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function modifierVille(EntityManagerInterface $entityManager, Request $request)
    {
        //Récupération de la ville à modifier, par son ID
        $ville = $entityManager->getRepository(Ville::class)->find($request->get('id'));
        //Formulaire de modification
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        //Si formulaire valide, on renvoie vers la liste, avec message
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La ville a bien été modifiée !');

            //Récupération de la liste des villes pour affichage
            $villeList = $entityManager->getRepository(Ville::class)->findAll();
            return $this->redirectToRoute('admin_villes', [
                'villeForm' => $villeForm->createView()
                , 'villeList' => $villeList]);
        }
        return $this->render('admin/modifierVille.html.twig', ['villeForm' => $villeForm->createView()]);
    }


    //Supprimer une ville

    /**
     * @Route(path="/{id}" , name="")
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return RedirectResponse
     */
    public function deleteCity(EntityManagerInterface $entityManager, $id): RedirectResponse
    {
        //Récupération de la ville a supprimer grace a l'ID puis suppression dans la BDD
        $entityManager->remove($ville = $entityManager->getRepository(Ville::class)->find($id));
        $entityManager->flush();

        $this->addFlash('success', 'le campus a bien été supprimé !');

        return $this->redirectToRoute('admin_villes');
    }



    /**
     * @Route(path="", name="")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function userList(EntityManagerInterface $entityManager): Response
    {
        //Récupération de la liste de tous les utilisateurs
        $allUser = $entityManager->getRepository('Participant::class')->findAll();
        return $this->render('admin/userList.html.twig', ['allUser' => $allUser]);
    }

    //Ajouter un utilisateur

    /**
     * @Route(path="userRegister", name="userRegister" )
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function userRegister(EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //Instanciation
        $user = new Participant();

        //Création du formulaire
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        //Si le formulaire est valide, on définit les paramètres manquants
        if ($form->isSubmitted() && $form->isValid()) {
            //Si admin a été choisit, on définit le role pour Admin
            if ($user->getAdmin() == true) {
                $user->setRoles((array)'ROLE_ADMIN');
            } else {
                //Sinon, automatiquement mit en simple user
                $user->setRoles(["ROLE_USER"]);
            }
            //Encodage du mot de passe de l'utilisateur
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    //Récupération des données remplies dans le champ 'password'
                    $form->get('password')->getData()
                )
            );
            //Envoie des données dans la BDD
            $entityManager->persist($user);
            $entityManager->flush();

            //Message
            $this->addFlash('success', 'Utilisateur ajouté!');
            //Retour à la liste des utilisateurs
            return $this->redirectToRoute('admin_userList');
        }
        //Envoie vers la vue d'ajout d'utilisateurs
        return $this->render('admin/userRegister.html.twig', ['userRegisterForm' => $form->createView()]);
    }


}
