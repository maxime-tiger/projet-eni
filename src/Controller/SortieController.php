<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SortieType;
use App\Entity\Sortie;
use App\Entity\Lieu;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }



    

    /**
     * @Route(path="create", name="create")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        //Instanciation des objets
        $sortie = new Sortie();
        $lieu = new Lieu();
        //Attribue $place a $event
        $sortie->setPlace($lieu);

        //L'organisateur est l'utilisateur connecté
        $organizer = $entityManager->getRepository('App:User')->findOneBy(['username' => $this->getUser()->getUsername()]);
        $sortie->setOrganizer($organizer);

        //Initialisation du nombre d'inscrit a 0
        $sortie->setCurrentSubs(0);

        //Création du formulaire
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        //En fonction du bouton, message et état différent
        $message = '';
        if ($form->isSubmitted() && $form->isValid()) {
            //Si enregister, l'état est "En création"
            if ($form->get('register')->isClicked()) {
                $sortie->setState($state = $entityManager->getRepository('App:EventState')->find(1));
                $message = 'Votre sortie a bien été enregistrée! Vous pouvez toujours la modifier, puis la publier ou la supprimer sur cette page.';

                //Si publier, l'état est "Ouvert"
            } elseif ($form->get('publish')->isClicked()) {
                $sortie->setState($state = $entityManager->getRepository('App:EventState')->find(2));
                $message = 'Votre sortie a bien été publiée! Vous pouvez la modifier jusqu\'au début de l\'événement.';
            }
            $entityManager->persist($lieu);
            $entityManager->persist($sortie);
            $entityManager->flush();
            //Message personnalisé en fonction du bouton choisit
            $this->addFlash('success', $message);

            //Récupération des données, pour envoie vers la page de modification d'event
            $eventDetail = $entityManager->getRepository('App:Event')->findOneBy(['name' => $request->get('name')]);

            //Renvoie vers la page
            return $this->redirectToRoute('sortie_detail', [
                'id' => $event->getId(),
                'sortie' => $eventDetail
            ]);
        }
        //Page de création
        return $this->render('sortie/create.html.twig',
            ['sortieCreateForm' => $form->createView()]);
    }

    /**
     * @Route(path="{id}", name="detail")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function detail(EntityManagerInterface $entityManager, Request $request): Response
    {
        //Récupère l'id de l'event pour l'affichage
        $sortie = $entityManager->getRepository('App:Event')->findOneBy(['id' => $request->get('id')]);

        //Si l'utilisateur est aussi l'organisateur -> affichage du formulaire pour modification et si l'event n'est pas archivé
        if ($this->getParticipant() === $event->getOrganizer() && $sortie->getState()->getId() != 4) {

            //Update l'event si modification
            $updateEventForm = $this->createForm(SortieType::class, $sortie);
            $updateEventForm->handleRequest($request);

            //Update le lieu si modification
            if ($updateEventForm->get('register')->isClicked()) {
                $sortie->setState($state = $entityManager->getRepository('App:EventState')->find(1));
                $entityManager->flush();
            } elseif ($updateEventForm->get('publish')->isClicked()) {
                $sortie->setState($state = $entityManager->getRepository('App:EventState')->find(2));
                $this->addFlash('success', 'Votre sortie est publiée!');
                $entityManager->flush();

                return $this->redirectToRoute('home_index');
            }
            //Récupération des inscrits
            $participants = $sortie->getSubscribers();
            return $this->render('event/editEvent.html.twig', [
                'updateEventForm' => $updateEventForm->createView(),
                'event' => $sortie,
                'participants' => $participants
            ]);
        }
        $participants = $sortie->getSubscribers();

        //Test si l'utilisateur est déjà inscrit ou non
        $subOrNot = false;
        foreach ($participants as $participant) {
            //Si oui, true, pour l'affichage du bouton "Se désinscrire"
            if ($this->getUser() == $participant) {
                $subOrNot = true;
            }
        }
        //Renvoie vers une page de détail sans modification possible
        return $this->render('event/detailEvent.html.twig', [
            'event' => $event,
            'participants' => $participants,
            'subOrNot' => $subOrNot]);
    }

    /**
     * @Route(path="subscribe/{id}", name="subscribe")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     */
    public function subToEvent(EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        //Récupération de l'event via l'URL
        $event = $entityManager->getRepository('App:Event')->findOneBy(['id' => $request->get('id')]);

        //Test si l'événement est ouvert
        if ($event->getState() == $state = $entityManager->getRepository('App:EventState')->find(2)) {

            //Test si la date limite n'est pas dépassé
            if ($event->getLimitDate() > (new DateTime("now"))) {

                //Test si il reste de la place dans l'événement
                if ($event->getCurrentSubs() < $event->getNbrPlace()) {

                    //Ajout de l'utilisateur + incrémentation du nombre de participants
                    $event->addSubscriber($user = $this->getUser());
                    $event->setCurrentSubs($event->getCurrentSubs() + 1);

                    //Fermeture des inscriptions si l'événement est complet
                    if ($event->getCurrentSubs() == $event->getNbrPlace()) {
                        $event->setState($closed = $entityManager->getRepository('App:EventState')->find(3));
                    }

                    $entityManager->flush();
                    $this->addFlash('success', 'Inscription confirmée.');

                } else {
                    $this->addFlash('warning', 'Cet événement est complet. Vous ne pouvez pas vous y inscrire.');
                }
            } else {
                $this->addFlash('warning', 'La date limite pour s\'inscrire à cet événement est dépassé.');
            }
        } else {
            $this->addFlash('warning', 'Les inscriptions pour cet événement ne sont pas ouvertes.');
        }
        return $this->redirectToRoute('event_detail', ['id' => $event->getId()]);
    }

    /**
     * @Route(path="unsubscribe/{id}", name="unsubscribe")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     */
    public function unsubToEvent(EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        //Récupération de l'event via l'URL
        $event = $entityManager->getRepository('App:Event')->findOneBy(['id' => $request->get('id')]);
        //Récupération de l'utilisateur pour le retirer de l'event
        $event->removeSubscriber($user = $this->getUser());

        //Update le nombre de participant
        $event->setCurrentSubs($event->getCurrentSubs() - 1);

        //Si le nombre d'utilisateur permet plus d'inscription, ré ouverture des inscriptions
        if ($event->getCurrentSubs() < $event->getNbrPlace()) {
            $event->setState($opened = $entityManager->getRepository('App:EventState')->find(2));
        }
        $entityManager->flush();
        $this->addFlash('success', 'Vous n\'êtes plus inscrit à cet événement.');
        return $this->redirectToRoute('event_detail', ['id' => $event->getId()]);
    }

    /**
     * @Route(path="cancel/{id}", name="cancel")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function cancel(EntityManagerInterface $entityManager, Request $request): Response
    {
        //Récupération de l'évent
        $event = $entityManager->getRepository('App:Event')->findOneBy(['id' => $request->get('id')]);

        //Création du formulaire pour annuler l'événement
        $cancelForm = $this->createForm(EventCancelFormType::class);
        $cancelForm->handleRequest($request);

        return $this->render('event/cancel.html.twig',
            ['cancelForm' => $cancelForm->createView(),
                'event' => $event]);
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     * @Route(path="delete/{id}", name="delete")
     */
    /*
    public function delete(EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        //Récupération de l'événement
        $event = $entityManager->getRepository(Event::class)->findOneBy(['id' => $request->get('id')]);
        //Changement de l'état pour "Archivé"
        $event->setState($archive = $entityManager->getRepository('App:EventState')->find(4));
        $entityManager->flush();
        //Message
        $this->addFlash('success', 'L\'événement a bien été annulé !');
        return $this->redirectToRoute('home_index');
    }
    */



}
