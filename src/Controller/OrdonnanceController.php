<?php

namespace App\Controller;
use Symfony\Component\Security\Core\Security;

use App\Entity\Ordonnance;
use App\Entity\Medecin; // Add this line to import the Medecin entity
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
    public function listOrdonnances(Security $security, OrdonnanceRepository $repository): Response
    {
        // Get the logged-in user
        $user = $security->getUser();

        // Ensure the user is a doctor
        if (!$user || !$user->getRole() === 'medecin') {
            $this->addFlash('error', 'Vous devez être connecté en tant que médecin pour voir cette liste.');
            return $this->redirectToRoute('app_login');
        }

        // Fetch ordonnances created by the logged-in doctor
        $ordonnanceList = $repository->findBy(['medecin' => $user]);

        return $this->render('ordonnance/list.html.twig', [
            'ordonnance_list' => $ordonnanceList,
        ]);
    }

    #[Route('/ordonnance/add', name: 'ordonnance_add')]
    public function addOrdonnance(Security $security, EntityManagerInterface $entityManager, Request $request): Response
    {
        $ordonnance = new Ordonnance();

        // Get the logged-in user
        $user = $security->getUser();

        // Ensure the logged-in user is a doctor
        $medecin = $entityManager->getRepository(Medecin::class)->findOneBy(['id' => $user]);
        if (!$medecin) {
            $this->addFlash('error', 'Vous devez être connecté en tant que médecin pour ajouter une ordonnance.');
            return $this->redirectToRoute('ordonnance_list');
        }

        // Set the Medecin in the Ordonnance
        $ordonnance->setMedecin($medecin);

        // Create the form
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Process the medicaments field
            $medicaments = $form->get('medicaments')->getData();
            $processedMedicaments = [];
            foreach ($medicaments as $medicament) {
                $processedMedicaments[] = sprintf('%s:%d', $medicament['nom'], $medicament['quantite']);
            }
            $ordonnance->setMedicaments($processedMedicaments);

            $entityManager->persist($ordonnance);
            $entityManager->flush();

            $this->addFlash('success', 'Ordonnance ajoutée avec succès.');
            return $this->redirectToRoute('ordonnance_list');
        }

// Pass the doctor's name to the template
        return $this->render('ordonnance/add.html.twig', [
            'form' => $form->createView(),
            'nomMedecinConnecte' => $medecin->getId()->getNom(), // Assuming Medecin->getId() returns a Utilisateur
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
    public function updateOrdonnance(Request $request, OrdonnanceRepository $repository, EntityManagerInterface $entityManager, $id): Response
    {
        $ordonnance = $repository->find($id);
        if (!$ordonnance) {
            throw $this->createNotFoundException('Ordonnance not found');
        }

        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Process the medicaments field
            $medicaments = $form->get('medicaments')->getData();
            $processedMedicaments = [];

            foreach ($medicaments as $medicament) {
                // Split the string into "nom" and "quantite"
                $parts = explode(':', $medicament);
                if (count($parts) === 2) {
                    $nom = trim($parts[0]);
                    $quantite = (int) trim($parts[1]);
                    $processedMedicaments[] = sprintf('%s:%d', $nom, $quantite);
                }
            }

            $ordonnance->setMedicaments($processedMedicaments);
            $entityManager->flush();

            $this->addFlash('success', 'Ordonnance mise à jour avec succès.');
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

    #[Route('/ordonnance/my', name: 'ordonnance_my')]
    public function myOrdonnances(Security $security, OrdonnanceRepository $repository): Response
    {
        // Get the logged-in user
        $user = $security->getUser();

        // Ensure the user is a patient
        if (!$user || !$user->getRole() === 'patient') {
            $this->addFlash('error', 'Vous devez être connecté en tant que patient pour voir vos ordonnances.');
            return $this->redirectToRoute('app_login');
        }

        // Fetch ordonnances for the logged-in patient
        $ordonnances = $repository->findBy(['patient' => $user]);

        return $this->render('ordonnance/my.html.twig', [
            'ordonnances' => $ordonnances,
        ]);
    }
}
