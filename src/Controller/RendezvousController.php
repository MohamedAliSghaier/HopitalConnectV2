<?php

namespace App\Controller;

use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RendezvousController extends AbstractController
{
    #[Route('/rendezvous/details/{id}', name: 'rendezvous_details')]
    public function showRendezVousDetails(RendezvousRepository $repository, $id): Response
    {
        $rendezvous = $repository->find($id);
        return $this->render('rendez_vous/details.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/rendezvous/list', name: 'rendezvous_list')]
    public function listRendezVous(RendezvousRepository $repository): Response
    {
        $rendezvousList = $repository->findAll();
        return $this->render('rendez_vous/list.html.twig', [
            'rendezvous_list' => $rendezvousList,
        ]);
    }

    #[Route('/rendezvous/add', name: 'rendezvous_add')]
    public function addRendezVous(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $newRendezvous = new Rendezvous();
        $form = $this->createForm(RendezvousType::class, $newRendezvous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($newRendezvous);
            $entityManager->flush();

            return $this->redirectToRoute('rendezvous_list');
        }

        return $this->render('rendez_vous/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendezvous/delete/{id}', name: 'rendezvous_delete')]
    public function deleteRendezVous(RendezvousRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $rendezvous = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($rendezvous);
        $entityManager->flush();
        
        return $this->redirectToRoute('rendezvous_list');
    }

    #[Route('/rendezvous/update/{id}', name: 'rendezvous_update')]
    public function updateRendezVous(Request $request, RendezvousRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $rendezvous = $repository->find($id);
        if (!$rendezvous) {
            throw $this->createNotFoundException('Rendezvous not found');
        }

        $form = $this->createForm(RendezvousType::class, $rendezvous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('rendezvous_list');
        }

        return $this->render('rendez_vous/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendezvous/search', name: 'rendezvous_search')]
    public function searchRendezVous(EntityManagerInterface $entityManager, Request $request, RendezvousRepository $repository): Response
    {
        $results = $repository->findAll();
        
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');
            
            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT r
                     FROM App\Entity\Rendezvous r
                     WHERE r.date LIKE :searchTerm
                        OR r.type_consultation_id LIKE :searchTerm"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $results = $query->getResult();
            }
        }
        
        return $this->render('rendez_vous/search.html.twig', [
            'rendezvous_list' => $results,
        ]);
    }
}