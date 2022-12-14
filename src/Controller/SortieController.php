<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SortieType;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/sortie')]
class SortieController extends AbstractController
{


    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository ): Response
    {
        return $this->render('sortie/index.html.twig',
        [
            'sorties' => $sortieRepository->findAll()
        ]);
    }

#[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
public function new(Request $request, SortieRepository $sortieRepository): Response
{
    $sortie = new Sortie();
    $form = $this->createForm(SortieType::class, $sortie);
    $form->handleRequest($request);

    $idutil = $this->getUser();

    $sortie->setOrganisateur($idutil);
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
public function show(Sortie $sortie, Request $request, SortieRepository $sortieRepository): Response
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

#[Route('/delete/{id}', name: 'app_sortie_delete', methods: ['GET', 'POST'])]
public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
{
    if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
        $sortieRepository->remove($sortie, true);
    }
    return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
}



#[Route('/{id}/{participant}', name: 'app_sortie_participer', methods: ['GET'])]
public function participer(Sortie $sortie, Participant $participant, EntityManagerInterface $em): Response
{
    $sortie->addParticipant($participant);
    $em->persist($sortie);
    $em->flush();
    return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
}



#[Route('sortie/{id}/{participant}', name: 'app_sortie_desister', methods: ['GET'])]
    public function desister(Sortie $sortie, Participant $participant, EntityManagerInterface $em): Response
    {

        
        $sortie->removeParticipant($participant);

        $em->persist($sortie);
        $em->flush();


        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

}
