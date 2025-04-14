<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicamentController extends AbstractController
{
    #[Route('/medicament/details/{id}', name: 'medicament_details')]
    public function showMedicamentDetails(MedicamentRepository $repository, $id): Response
    {
        $medicament = $repository->find($id);
        return $this->render('medicament/details.html.twig', [
            'medicament' => $medicament,
        ]);
    }

    #[Route('/medicament/list', name: 'medicament_list')]
    public function listMedicaments(MedicamentRepository $repository): Response
    {
        $medicaments = $repository->findAll();
        return $this->render('medicament/list.html.twig', [
            'medicament_list' => $medicaments,
        ]);
    }

    #[Route('/medicament/add', name: 'medicament_add')]
    public function addMedicament(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $medicament = new Medicament();
        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            // Les erreurs seront automatiquement transmises au template
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($medicament);
            $entityManager->flush();

            $this->addFlash('success', 'Médicament ajouté avec succès.');

            return $this->redirectToRoute('medicament_list');
        }

        return $this->render('medicament/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/medicament/delete/{id}', name: 'medicament_delete')]
    public function deleteMedicament(MedicamentRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $medicament = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($medicament);
        $entityManager->flush();

        return $this->redirectToRoute('medicament_list');
    }

    #[Route('/medicament/update/{id}', name: 'medicament_update')]
    public function updateMedicament(Request $request, MedicamentRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $medicament = $repository->find($id);
        if (!$medicament) {
            throw $this->createNotFoundException('Medicament not found');
        }

        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('medicament_list');
        }

        return $this->render('medicament/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/medicament/search', name: 'medicament_search')]
    public function searchMedicament(EntityManagerInterface $entityManager, Request $request): Response
    {
        $results = [];

        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');

            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT m FROM App\Entity\Medicament m WHERE m.nom LIKE :searchTerm"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $results = $query->getResult();
            }
        }

        return $this->render('medicament/search.html.twig', [
            'medicament_list' => $results,
        ]);
    }
}
