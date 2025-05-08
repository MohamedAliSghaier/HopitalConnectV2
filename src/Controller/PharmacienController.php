<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ordonnance;
use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\OrdonnanceRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class PharmacienController extends AbstractController
{
    #[Route('/pharmacien/dashboard', name: 'pharmacien_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PHARMACIEN');

        return $this->render('pharmacien/pharmacien_dashboard.html.twig', [
            'pharmacienName' => $this->getUser()->getNom(), // Exemple : afficher le nom du pharmacien
        ]);
    }

    #[Route('/pharmacien/ordonnances', name: 'pharmacien_ordonnances')]
    public function listOrdonnances(OrdonnanceRepository $repository): Response
    {
        // Fetch all ordonnances
        $ordonnances = $repository->findAll();

        return $this->render('pharmacien/ordonnances.html.twig', [
            'ordonnances' => $ordonnances,
        ]);
    }

    #[Route('/pharmacien/validate/{id}', name: 'pharmacien_validate')]
    public function validateOrdonnance(
        Ordonnance $ordonnance,
        MedicamentRepository $medicamentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Ensure the ordonnance is not already validated
        if ($ordonnance->getStatut() === 'Terminée') {
            $this->addFlash('warning', 'Cette ordonnance a déjà été validée.');
            return $this->redirectToRoute('pharmacien_ordonnances');
        }

        // Process the medicaments in the ordonnance
        foreach ($ordonnance->getMedicaments() as $medicamentData) {
            // Split the medicament string into name and quantity
            $parts = explode(':', $medicamentData);
            if (count($parts) === 2) {
                $medicamentName = $parts[0];
                $quantity = (int) $parts[1];

                // Find the medicament in the database
                $medicament = $medicamentRepository->findOneBy(['nom' => $medicamentName]);
                if ($medicament) {
                    // Update the stock
                    $newStock = $medicament->getStock() - $quantity;
                    if ($newStock < 0) {
                        $this->addFlash('error', "Le stock pour le médicament '{$medicamentName}' est insuffisant.");
                        return $this->redirectToRoute('pharmacien_ordonnances');
                    }
                    $medicament->setStock($newStock);
                } else {
                    $this->addFlash('error', "Le médicament '{$medicamentName}' n'existe pas dans la base de données.");
                    return $this->redirectToRoute('pharmacien_ordonnances');
                }
            }
        }

        // Update the ordonnance status to "Terminée"
        $ordonnance->setStatut('Terminée');
        $entityManager->flush();

        $this->addFlash('success', 'Ordonnance validée et stock mis à jour avec succès.');

        return $this->redirectToRoute('pharmacien_ordonnances');
    }

    #[Route('/pharmacien/medicaments', name: 'pharmacien_medicament_list')]
    public function listMedicaments(MedicamentRepository $repository): Response
    {
        $medicaments = $repository->findAll();

        return $this->render('pharmacien/medicaments/list.html.twig', [
            'medicaments' => $medicaments,
        ]);
    }

    #[Route('/pharmacien/medicament/add', name: 'pharmacien_medicament_add')]
    public function addMedicament(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $medicament = new Medicament();
        $form = $this->createForm(MedicamentType::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the logged-in user
            $user = $security->getUser();

            // Ensure the user is a Pharmacien
            if (!$user instanceof \App\Entity\Pharmacien) {
                throw new \LogicException('The logged-in user is not a Pharmacien.');
            }

            // Set the Pharmacien for the Medicament
            $medicament->setPharmacien($user);

            // Save the Medicament
            $entityManager->persist($medicament);
            $entityManager->flush();

            return $this->redirectToRoute('pharmacien_medicament_list');
        }

        return $this->render('pharmacien/medicaments/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pharmacien/medicament/update/{id}', name: 'pharmacien_medicament_update')]
    public function updateMedicament(
        Medicament $medicament,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(MedicamentType::class, $medicament);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Médicament modifié avec succès.');
            return $this->redirectToRoute('pharmacien_medicament_list');
        }

        return $this->render('pharmacien/medicaments/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pharmacien/medicament/delete/{id}', name: 'pharmacien_medicament_delete')]
    public function deleteMedicament(
        Medicament $medicament,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($medicament);
        $entityManager->flush();

        $this->addFlash('success', 'Médicament supprimé avec succès.');
        return $this->redirectToRoute('pharmacien_medicament_list');
    }
}