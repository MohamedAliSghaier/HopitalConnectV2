<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Entity\Pharmacien;
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
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/pharmacien/medicaments', name: 'pharmacien_medicaments_index')]
    public function index(): Response
    {
        $medicaments = $this->entityManager->getRepository(Medicament::class)->findAll();

        return $this->render('pharmacien/medicaments/index.html.twig', [
            'medicaments' => $medicaments,
        ]);
    }

    #[Route('/medicament/details/{id}', name: 'medicament_details')]
    public function showMedicamentDetails(MedicamentRepository $repository, $id): Response
    {
        $medicament = $repository->find($id);
        return $this->render('medicament/details.html.twig', [
            'medicament' => $medicament,
        ]);
    }

    #[Route('/pharmacien/medicaments/list', name: 'pharmacien_medicaments_list')]
    public function listMedicaments(MedicamentRepository $repository): Response
    {
        $medicaments = $repository->findAll();
        return $this->render('pharmacien/medicaments/list.html.twig', [
            'medicaments' => $medicaments,
        ]);
    }

    #[Route('/pharmacien/medicaments/add', name: 'medicament_add')]
    public function addMedicament(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $medicament = new Medicament();
        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            // Errors will automatically be passed to the template
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the logged-in user (assumed to be a Utilisateur)
            $user = $this->getUser();

            if (!$user) {
                throw $this->createAccessDeniedException('You must be logged in to add a Medicament.');
            }

            // Retrieve the Pharmacien associated with the logged-in user
            $entityManager = $managerRegistry->getManager();
            $pharmacien = $entityManager->getRepository(Pharmacien::class)->findOneBy(['utilisateur' => $user]);

            if (!$pharmacien) {
                throw $this->createAccessDeniedException('No Pharmacien found for the logged-in user.');
            }

            // Associate the Medicament with the Pharmacien
            $medicament->setPharmacien($pharmacien);

            // Save the Medicament
            $entityManager->persist($medicament);
            $entityManager->flush();

            $this->addFlash('success', 'Médicament ajouté avec succès.');

            return $this->redirectToRoute('pharmacien_medicaments_list');
        }

        return $this->render('pharmacien/medicaments/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pharmacien/medicaments/delete/{id}', name: 'medicament_delete')]
    public function deleteMedicament(MedicamentRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $medicament = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($medicament);
        $entityManager->flush();

        return $this->redirectToRoute('pharmacien_medicaments_list');
    }

    #[Route('/pharmacien/medicaments/update/{id}', name: 'medicament_update')]
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
            return $this->redirectToRoute('pharmacien_medicaments_list');
        }

        return $this->render('pharmacien/medicaments/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pharmacien/medicaments/search', name: 'medicament_search')]
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

        return $this->render('pharmacien/medicaments/search.html.twig', [
            'medicament_list' => $results,
        ]);
    }
}
