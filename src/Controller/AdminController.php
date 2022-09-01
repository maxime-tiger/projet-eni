<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\ParticipantType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{

    /* fonction qui hash le mot de passe */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    //Acceuil admin
    /**
     * @Route(path="/", name="app_admin")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CampusRepository $campusRepository
     * @return Response
     */
    public function index(CampusRepository $campusRepository, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, LieuRepository $lieuRepository, VilleRepository $villeRepository): Response
    {
        
            return $this->render('admin/index.html.twig', [
                'campuses' => $campusRepository->findAll(),
                'sorties' => $sortieRepository->findAll(),
                'participants' => $participantRepository->findAll(),
                'lieux' => $lieuRepository->findAll(),
                'villes' => $villeRepository->findAll()
            ]);
    }


    /* ---------------------------------- campus ---------------------------------- */


    /* afficher les campus */
    #[Route('/campus', name: 'app_admin_campus_index', methods: ['GET'])]
    public function indexCampus(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus.html.twig', [
            'campuses' => $campusRepository->findAll(),
        ]);
    }

    /* créer un campus */
    #[Route('/campus/new', name: 'app_admin_campus_new', methods: ['GET', 'POST'])]
    public function newCampus(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->add($campus, true);

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/campusNew.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /* afficher les details d'un campus */
    #[Route('/campus/{id}/detail', name: 'app_admin_campus_show', methods: ['GET'])]
    public function showCampus(Campus $campus): Response
    {
        return $this->render('admin/campusShow.html.twig', [
            'campus' => $campus,
        ]);
    }

    /* editer un campus */
    #[Route('/campus/{id}/edit', name: 'app_admin_campus_edit', methods: ['GET', 'POST'])]
    public function editCampus(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campusRepository->add($campus, true);

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/campusModifier.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /* supprimer un campus */
    #[Route('/campus/delete/{id}', name: 'app_admin_campus_delete', methods: ['GET', 'POST'])]
    public function deleteCampus(Request $request, Campus $campus, CampusRepository $campusRepository): Response
    {
        $campusRepository->remove($campus, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }


    /* ---------------------------------- participant ---------------------------------- */

    /* afficher les participants */
    #[Route('/participant', name: 'app_admin_participant_index', methods: ['GET'])]
    public function indexParticipant(ParticipantRepository $participantRepository): Response
    {
        return $this->render('admin/participant.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /* créer un participant */
    #[Route('/participant/new', name: 'app_admin_participant_new', methods: ['GET', 'POST'])]
    public function newParticipant(Request $request, ParticipantRepository $participantRepository, ManagerRegistry $doctrine): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($participant->isAdministrateur() === true){
            $participant->setRoles(["ROLE_ADMIN"]); 
        }
        else {
            $participant->setRoles(["ROLE_USER"]); 
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setPassword($this->passwordHasher->hashPassword($participant, $participant->getPassword()));

            $participantRepository->add($participant, true);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/participantNew.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /* afficher les détails d'un participant */
    #[Route('/participant/{id}', name: 'app_admin_participant_show', methods: ['GET'])]
    public function showParticipant(Participant $participant): Response
    {
        return $this->render('admin/participantShow.html.twig', [
            'participant' => $participant,
        ]);
    }

    /* modifier un participant */
    #[Route('/participant/{id}', name: 'app_admin_participant_edit', methods: ['GET', 'POST'])]
    public function editParticipant(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->add($participant, true);

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/participantModifier.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /* supprimer un participant */
    #[Route('/participant/delete/{id}', name: 'app_admin_participant_delete', methods: ['GET', 'POST'])]
    public function deleteParticipant(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        $participantRepository->remove($participant, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /* supprimer une sortie */
    #[Route('/sortie/delete/{id}', name: 'app_admin_sortie_delete', methods: ['GET', 'POST'])]
    public function deleteSortie(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $sortieRepository->remove($sortie, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /* modifier une sortie */
    #[Route('/sortie/edit/{id}', name: 'app_admin_sortie_edit', methods: ['GET', 'POST'])]
    public function editSortie(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);
            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/sortieEdit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /* supprimer un lieu */
    #[Route('/lieu/delete/{id}', name: 'app_admin_lieu_delete', methods: ['GET', 'POST'])]
    public function deleteLieu(Lieu $lieu, LieuRepository $lieuRepository): Response
    {

        $lieuRepository->remove($lieu, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }

    /* creer une ville */
    #[Route('/ville/new', name: 'app_admin_ville_new', methods: ['GET', 'POST'])]
    public function newVille(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville, true);

            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/villeNew.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

     /* modifier une ville */
    #[Route('/ville/edit/{id}', name: 'app_admin_ville_edit', methods: ['GET', 'POST'])]
    public function editVille(Request $request, Ville $ville, VilleRepository $villeRepository): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $villeRepository->add($ville, true);
            return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/villeEdit.html.twig', [
            'sortie' => $ville,
            'form' => $form,
        ]);
    }
    
    /* supprimer une ville */
    #[Route('/ville/delete/{id}', name: 'app_admin_ville_delete', methods: ['GET', 'POST'])]
    public function deleteVille(Ville $ville, VilleRepository $villeRepository): Response
    {

        $villeRepository->remove($ville, true);

        return $this->redirectToRoute('app_admin', [], Response::HTTP_SEE_OTHER);
    }
}
