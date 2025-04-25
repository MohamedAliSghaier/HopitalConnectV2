<?php

namespace App\Controller;

use App\Entity\Dossiermedicale;
use App\Form\DossiermedicaleType; // Assuming you have a form type for Dossiermedicale
use App\Repository\DossiermedicaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DossiermedicaleController extends AbstractController
{
    #[Route('/dossiermedicale/details/{id}', name: 'dossiermedicale_details')]
    public function showDossiermedicaleDetails(DossiermedicaleRepository $repository, $id): Response
    {
        $dossiermedicale = $repository->find($id);
        return $this->render('dossiermedicale/details.html.twig', [
            'dossiermedicale' => $dossiermedicale,
        ]);
    }

    #[Route('/dossiermedicale/list', name: 'dossiermedicale_list')]
    public function listDossiermedicale(DossiermedicaleRepository $repository): Response
    {
        $dossiermedicaleList = $repository->findAll();
        return $this->render('dossiermedicale/list.html.twig', [
            'dossiermedicale_list' => $dossiermedicaleList,
        ]);
    }

    #[Route('/dossiermedicale/add', name: 'dossiermedicale_add')]
    public function addDossiermedicale(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $dossierMedicale = new Dossiermedicale();

        // Get the logged-in user
        $user = $security->getUser();

        // Find the Patient associated with the logged-in user
        $patient = $entityManager->getRepository(\App\Entity\Patient::class)->findOneBy(['id' => $user]);
        if (!$patient) {
            $this->addFlash('error', 'You must be logged in as a patient to add a dossier medicale.');
            return $this->redirectToRoute('dossiermedicale_list');
        }

        // Set the Patient in the DossierMedicale
        $dossierMedicale->setIdPatient($patient);

        // Create the form
        $form = $this->createForm(DossiermedicaleType::class, $dossierMedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dossierMedicale);
            $entityManager->flush();

            $this->addFlash('success', 'Dossier médical added successfully!');
            return $this->redirectToRoute('dossiermedicale_list');
        }

        return $this->render('dossiermedicale/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dossiermedicale/delete/{id}', name: 'dossiermedicale_delete')]
    public function deleteDossiermedicale(DossiermedicaleRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $dossiermedicale = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($dossiermedicale);
        $entityManager->flush();

        return $this->redirectToRoute('dossiermedicale_list');
    }

    #[Route('/dossiermedicale/update/{id}', name: 'dossiermedicale_update')]
    public function updateDossiermedicale(Request $request, DossiermedicaleRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $dossiermedicale = $repository->find($id);
        if (!$dossiermedicale) {
            throw $this->createNotFoundException('Dossiermedicale not found');
        }

        $form = $this->createForm(DossiermedicaleType::class, $dossiermedicale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('dossiermedicale_list');
        }

        return $this->render('dossiermedicale/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dossiermedicale/search', name: 'dossiermedicale_search')]
    public function searchDossiermedicale(EntityManagerInterface $entityManager, Request $request, DossiermedicaleRepository $repository): Response
    {
        $results = $repository->findAll();

        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');
            
            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT d
                     FROM App\Entity\Dossiermedicale d
                     WHERE d.maladies LIKE :searchTerm
                        OR d.allergies LIKE :searchTerm
                        OR d.profession LIKE :searchTerm
                        OR d.situation_familiale LIKE :searchTerm"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $results = $query->getResult();
            }
        }

        return $this->render('dossiermedicale/search.html.twig', [
            'dossiermedicale_list' => $results,
        ]);
    }
}
