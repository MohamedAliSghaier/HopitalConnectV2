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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;


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
        foreach ($ordonnances as $ordonnance) {
            $medicaments = $ordonnance->getMedicaments();
            $processedMedicaments = [];

            foreach ($medicaments as $medicament) {
                if (is_array($medicament) && isset($medicament['nom'], $medicament['quantite'])) {
                    $processedMedicaments[] = $medicament['nom'] . ':' . $medicament['quantite'];
                }
            }

            $ordonnance->setMedicaments($processedMedicaments);
        }
        return $this->render('ordonnance/list.html.twig', [
            'ordonnance_list' => $ordonnances,
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

        // Convert the medicaments field to an array if it is stored as a string
        if (is_string($ordonnance->getMedicaments())) {
            $ordonnance->setMedicaments(json_decode($ordonnance->getMedicaments(), true) ?? []);
        }

        $rawMedicaments = $ordonnance->getMedicaments(); // ["Doliprane:2", "Efferalgan:1"]
        $structuredMedicaments = [];

        foreach ($rawMedicaments as $item) {
          if (strpos($item, ':') !== false) {
             [$nom, $quantite] = explode(':', $item);
             $structuredMedicaments[] = [
               'nom' => $nom,
               'quantite' => (int) $quantite,
           ];
          }
    
        }

        $ordonnance->setMedicaments($structuredMedicaments);


        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the medicaments field from the form
            $medicaments = $form->get('medicaments')->getData();
            $processedMedicaments = [];

            // Process medicaments into the same format as in the add method
            foreach ($medicaments as $medicament) {
                if (isset($medicament['nom'], $medicament['quantite'])) {
                    $processedMedicaments[] = sprintf('%s:%d', $medicament['nom'], $medicament['quantite']);
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
                    "SELECT o
                     FROM App\Entity\Ordonnance o
                     JOIN o.patient p
                     JOIN p.utilisateur u
                     WHERE u.nom LIKE :searchTerm
                        OR o.date_prescription LIKE :searchTerm"
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

    #[Route('/ordonnance/list', name: 'ordonnance_list')]
    public function list(Request $request, OrdonnanceRepository $ordonnanceRepository): Response
    {
        $searchNomPatient = $request->query->get('search_nom_patient', '');
        $searchNomMedecin = $request->query->get('search_nom_medecin', '');
        $searchDatePrescription = $request->query->get('search_date_prescription', '');
        $searchStatut = $request->query->get('search_statut', '');
    
        $qb = $ordonnanceRepository->createQueryBuilder('o')
        ->leftJoin('o.patient', 'p')
        ->leftJoin('p.utilisateur', 'u')   // Patient -> Utilisateur
        ->leftJoin('o.medecin', 'm')        // Ordonnance -> Medecin
        ->leftJoin('m.id', 'mu');           // Medecin.id -> Utilisateur
    
    
        if ($searchNomPatient) {
            $qb->andWhere('u.nom LIKE :searchNomPatient')
               ->setParameter('searchNomPatient', '%' . $searchNomPatient . '%');
        }
    
        if ($searchNomMedecin) {
            $qb->andWhere('mu.nom LIKE :searchNomMedecin')
               ->setParameter('searchNomMedecin', '%' . $searchNomMedecin . '%');
        }
    
        if ($searchDatePrescription) {
            $qb->andWhere('o.date_prescription = :searchDatePrescription')
               ->setParameter('searchDatePrescription', $searchDatePrescription);
        }
    
        if ($searchStatut) {
            $qb->andWhere('o.statut = :searchStatut')
               ->setParameter('searchStatut', $searchStatut);
        }
    
        $ordonnanceList = $qb->getQuery()->getResult();
    
        return $this->render('ordonnance/list.html.twig', [
            'ordonnance_list' => $ordonnanceList,
            'search_nom_patient' => $searchNomPatient,
            'search_nom_medecin' => $searchNomMedecin,
            'search_date_prescription' => $searchDatePrescription,
            'search_statut' => $searchStatut,
        ]);
    }

    #[Route('/ordonnance/pdf/{id}', name: 'ordonnance_pdf')]
public function generateOrdonnancePdf(
    int $id,
    EntityManagerInterface $entityManager,
    Pdf $knpSnappyPdf
): Response {
    // 1. Récupérer l'entité Ordonnance
    $ordonnance = $entityManager->getRepository(Ordonnance::class)->find($id);

    if (!$ordonnance) {
        throw $this->createNotFoundException('Ordonnance non trouvée.');
    }

    // 2. Générer le HTML depuis un template Twig
    $html = $this->renderView('ordonnance/pdf.html.twig', [
        'ordonnance' => $ordonnance,
    ]);

    // 3. Générer le contenu PDF
    $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

    // 4. Retourner le PDF au navigateur
    return new Response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="ordonnance_' . $ordonnance->getId() . '.pdf"',
    ]);
}

#[Route('/ordonnances/graph', name: 'ordonnances_graph')]
public function showGraph(OrdonnanceRepository $ordonnanceRepository)
{
    // Récupérer toutes les ordonnances
    $ordonnances = $ordonnanceRepository->getOrdonnancesParMois();

    // Vérifier si des ordonnances ont été récupérées
    if (empty($ordonnances)) {
        return new Response('Aucune ordonnance trouvée.');
    }

    // Initialiser un tableau pour compter les ordonnances par mois
    $months = [];
    foreach ($ordonnances as $ordonnance) {
        // Utilise la date_prescription pour extraire le mois
        $month = $ordonnance['date_prescription']->format('m'); 
        if (!isset($months[$month])) {
            $months[$month] = 0;
        }
        $months[$month]++;
    }

    // Trier les mois par ordre numérique
    ksort($months);

    return $this->render('ordonnance/graph.html.twig', [
        'months' => array_keys($months),
        'counts' => array_values($months),
    ]);
}

#[Route('/ordonnance/search-ajax', name: 'ordonnance_search_ajax', methods: ['GET'])]
public function searchAjax(Request $request, OrdonnanceRepository $ordonnanceRepository): Response
{
    $searchNomPatient = $request->query->get('search_nom_patient', '');
    $searchDatePrescription = $request->query->get('search_date_prescription', '');
    $searchStatut = $request->query->get('search_statut', '');
    $sortBy = $request->query->get('sort_by', 'date_prescription');
    $sortOrder = $request->query->get('sort_order', 'DESC');

    $qb = $ordonnanceRepository->createQueryBuilder('o')
        ->leftJoin('o.patient', 'p')
        ->leftJoin('p.utilisateur', 'u')
        ->leftJoin('o.medecin', 'm')
        ->leftJoin('m.id', 'mu');

    if ($searchNomPatient) {
        $qb->andWhere('u.nom LIKE :searchNomPatient')
           ->setParameter('searchNomPatient', '%' . $searchNomPatient . '%');
    }

    if ($searchDatePrescription) {
        $qb->andWhere('o.date_prescription = :searchDatePrescription')
           ->setParameter('searchDatePrescription', $searchDatePrescription);
    }

    if ($searchStatut) {
        $qb->andWhere('o.statut = :searchStatut')
           ->setParameter('searchStatut', $searchStatut);
    }

    // Ajout du tri
    switch ($sortBy) {
        case 'patient':
            $qb->orderBy('u.nom', $sortOrder);
            break;
        case 'date_prescription':
        default:
            $qb->orderBy('o.date_prescription', $sortOrder);
            break;
    }

    $ordonnances = $qb->getQuery()->getResult();

    $html = $this->renderView('ordonnance/_ordonnance_list.html.twig', [
        'ordonnance_list' => $ordonnances,
    ]);

    return new JsonResponse(['html' => $html]);
}

}
