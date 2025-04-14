<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use App\Form\OrdonnanceType;
use App\Repository\OrdonnanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrdonnanceController extends AbstractController
{
    #[Route('/ordonnance/details/{id}', name: 'ordonnance_details')]
    public function showOrdonnanceDetails(OrdonnanceRepository $repository, $id): Response
    {
        $ordonnance = $repository->find($id);
        return $this->render('ordonnance/details.html.twig', [
            'ordonnance' => $ordonnance,
        ]);
    }

    #[Route('/ordonnance/list', name: 'ordonnance_list')]
    public function listOrdonnances(OrdonnanceRepository $repository): Response
    {
        $ordonnances = $repository->findAll();
        return $this->render('ordonnance/list.html.twig', [
            'ordonnance_list' => $ordonnances,
        ]);
    }

    #[Route('/ordonnance/add', name: 'ordonnance_add')]
    public function addOrdonnance(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $ordonnance = new Ordonnance();
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            // Errors will be passed to the template
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($ordonnance);
            $entityManager->flush();

            $this->addFlash('success', 'Ordonnance ajoutée avec succès.');

            return $this->redirectToRoute('ordonnance_list');
        }

        return $this->render('ordonnance/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ordonnance/delete/{id}', name: 'ordonnance_delete')]
    public function deleteOrdonnance(OrdonnanceRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $ordonnance = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($ordonnance);
        $entityManager->flush();

        return $this->redirectToRoute('ordonnance_list');
    }

    #[Route('/ordonnance/update/{id}', name: 'ordonnance_update')]
    public function updateOrdonnance(Request $request, OrdonnanceRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $ordonnance = $repository->find($id);
        if (!$ordonnance) {
            throw $this->createNotFoundException('Ordonnance not found');
        }

        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('ordonnance_list');
        }

        return $this->render('ordonnance/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ordonnance/search', name: 'ordonnance_search')]
    public function searchOrdonnance(EntityManagerInterface $entityManager, Request $request): Response
    {
        $results = [];

        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');

            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT o FROM App\Entity\Ordonnance o WHERE o.medicaments LIKE :searchTerm"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $results = $query->getResult();
            }
        }

        return $this->render('ordonnance/search.html.twig', [
            'ordonnance_list' => $results,
        ]);
    }
}
