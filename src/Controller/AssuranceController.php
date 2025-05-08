<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use App\Service\QrCodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use App\Service\TwilioService;
use Symfony\Component\HttpFoundation\JsonResponse;

class AssuranceController extends AbstractController
{
    private NotifierInterface $notifier;
    private QrCodeService $qrCodeService;

    public function __construct(NotifierInterface $notifier, QrCodeService $qrCodeService)
    {
        $this->notifier = $notifier;
        $this->qrCodeService = $qrCodeService;
    }

    #[Route('/assurance/details/{id}', name: 'assurance_details')]
    public function showDetails(AssuranceRepository $repository, $id): Response
    {
        $assurance = $repository->find($id);

        if (!$assurance) {
            $this->addFlash('error', 'Assurance not found');
            return $this->redirectToRoute('assurance_list');
        }

        return $this->render('assurance/details.html.twig', [
            'assurance' => $assurance,
        ]);
    }

    #[Route('/assurance/list', name: 'assurance_list')]
    public function list(Request $request, AssuranceRepository $repository): Response
    {
        $searchNomAssureur = $request->query->get('search_nom_assureur', '');
        $searchTypeCouverture = $request->query->get('search_type_couverture', '');
        $searchDateDebut = $request->query->get('search_date_debut', '');
        $searchDateFin = $request->query->get('search_date_fin', '');
        $sortBy = $request->query->get('sort_by', 'date_debut');
        $sortOrder = $request->query->get('sort_order', 'DESC');

        $queryBuilder = $repository->createQueryBuilder('a');

        if ($searchNomAssureur) {
            $queryBuilder->andWhere('a.NomAssureur = :searchNomAssureur')
                ->setParameter('searchNomAssureur', $searchNomAssureur);
        }

        if ($searchTypeCouverture) {
            $queryBuilder->andWhere('a.TypeCouverture = :searchTypeCouverture')
                ->setParameter('searchTypeCouverture', $searchTypeCouverture);
        }

        if ($searchDateDebut) {
            $queryBuilder->andWhere('a.dateDebut = :searchDateDebut')
                ->setParameter('searchDateDebut', $searchDateDebut);
        }

        if ($searchDateFin) {
            $queryBuilder->andWhere('a.dateFin = :searchDateFin')
                ->setParameter('searchDateFin', $searchDateFin);
        }

        // Conversion des paramètres de tri du format du formulaire au format de l'entité
        $sortFieldMap = [
            'date_debut' => 'dateDebut',
            'date_fin' => 'dateFin',
            'nom_assureur' => 'NomAssureur',
            'type_couverture' => 'TypeCouverture'
        ];

        $entitySortField = $sortFieldMap[$sortBy] ?? 'dateDebut';

        // Validation de l'ordre de tri
        $validSortOrders = ['ASC', 'DESC'];
        $sortOrder = in_array(strtoupper($sortOrder), $validSortOrders) ? strtoupper($sortOrder) : 'DESC';

        // Ajout du tri
        $queryBuilder->orderBy("a.$entitySortField", $sortOrder);

        $assuranceList = $queryBuilder->getQuery()->getResult();

        $nomAssureurOptions = $repository->createQueryBuilder('a')
            ->select('DISTINCT a.NomAssureur')
            ->getQuery()
            ->getResult();

        $typeCouvertureOptions = $repository->createQueryBuilder('a')
            ->select('DISTINCT a.TypeCouverture')
            ->getQuery()
            ->getResult();

        return $this->render('assurance/list.html.twig', [
            'assurance_list' => $assuranceList,
            'search_nom_assureur' => $searchNomAssureur,
            'search_type_couverture' => $searchTypeCouverture,
            'search_date_debut' => $searchDateDebut,
            'search_date_fin' => $searchDateFin,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'nom_assureur_options' => $nomAssureurOptions,
            'type_couverture_options' => $typeCouvertureOptions,
        ]);
    }

    #[Route('/assurance/add', name: 'assurance_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, Security $security, TwilioService $twilioService, QrCodeService $qrCodeService): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$security->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter une assurance.');
            return $this->redirectToRoute('app_login');
        }

        $assurance = new Assurance();
        $user = $security->getUser();

        $patient = $entityManager->getRepository(\App\Entity\Patient::class)->findOneBy(['utilisateur' => $user]);
        if (!$patient) {
            $this->addFlash('error', 'Vous devez être connecté en tant que patient pour ajouter une assurance.');
            return $this->redirectToRoute('assurance_list');
        }

        $assurance->setPatient($patient);
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($assurance);
                $entityManager->flush();

                // Generate QR code
                $qrData = sprintf(
                    "Assurance: %s\nType: %s\nNuméro Police: %s\nTitulaire: %s\nDate Début: %s\nDate Fin: %s",
                    $assurance->getNomAssureur(),
                    $assurance->getTypeAssureur(),
                    $assurance->getNumeroPolice(),
                    $assurance->getNomTitulaire(),
                    $assurance->getDateDebut()->format('Y-m-d'),
                    $assurance->getDateFin()->format('Y-m-d')
                );
                
                $filename = 'assurance_' . $assurance->getId() . '.png';
                $qrCodeService->generateQrCode($qrData, $filename);

                // Send SMS using fixed number for Twilio free trial
                $message = sprintf(
                    "Nouvelle assurance ajoutée:\nAssureur: %s\nType: %s\nNuméro Police: %s\nTitulaire: %s",
                    $assurance->getNomAssureur(),
                    $assurance->getTypeAssureur(),
                    $assurance->getNumeroPolice(),
                    $assurance->getNomTitulaire()
                );

                // Use fixed number for Twilio free trial
                $twilioService->sendSMS('+21654135617', $message);

                $this->addFlash('success', 'L\'assurance a été ajoutée avec succès et un SMS a été envoyé.');
                return $this->redirectToRoute('assurance_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'assurance: ' . $e->getMessage());
            }
        }

        return $this->render('assurance/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/assurance/qr-code/{id}', name: 'assurance_qr_code')]
    public function downloadQrCode(Assurance $assurance, QrCodeService $qrCodeService): Response
    {
        $filename = 'assurance_' . $assurance->getId() . '.png';
        $filepath = $qrCodeService->getQrCodePath($filename);
        
        if (!file_exists($filepath)) {
            // Generate the QR code if it doesn't exist
            $qrData = sprintf(
                "Assurance: %s\nType: %s\nNuméro Police: %s\nTitulaire: %s\nDate Début: %s\nDate Fin: %s",
                $assurance->getNomAssureur(),
                $assurance->getTypeAssureur(),
                $assurance->getNumeroPolice(),
                $assurance->getNomTitulaire(),
                $assurance->getDateDebut()->format('Y-m-d'),
                $assurance->getDateFin()->format('Y-m-d')
            );
            
            $qrCodeService->generateQrCode($qrData, $filename);
        }

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'qr_code_' . $assurance->getId() . '.png'
        );

        return $response;
    }

    #[Route('/assurance/update/{id}', name: 'assurance_update')]
    public function update(Request $request, AssuranceRepository $repository, EntityManagerInterface $em, $id): Response
    {
        $assurance = $repository->find($id);

        if (!$assurance) {
            $this->addFlash('error', 'Assurance not found');
            return $this->redirectToRoute('assurance_list');
        }

        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $em->flush();
                    $this->addFlash('success', 'Assurance updated successfully!');
                    return $this->redirectToRoute('assurance_list');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error updating assurance: ' . $e->getMessage());
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('assurance/update.html.twig', [
            'form' => $form->createView(),
            'assurance' => $assurance,
        ]);
    }

    #[Route('/assurance/delete/{id}', name: 'assurance_delete')]
    public function delete(AssuranceRepository $repository, EntityManagerInterface $em, $id): Response
    {
        $assurance = $repository->find($id);

        if (!$assurance) {
            $this->addFlash('error', 'Assurance not found');
        } else {
            try {
                $em->remove($assurance);
                $em->flush();
                $this->addFlash('success', 'Assurance deleted successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error deleting assurance: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('assurance_list');
    }

    #[Route('/assurance/search', name: 'assurance_search')]
    public function searchAssurance(EntityManagerInterface $entityManager, Request $request): Response
    {
        $results = [];

        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');

            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT a FROM App\Entity\Assurance a
                     WHERE a.NomAssureur LIKE :searchTerm
                     OR a.dateDebut LIKE :searchTerm
                     OR a.dateFin LIKE :searchTerm"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $results = $query->getResult();
            }
        }

        return $this->render('assurance/search.html.twig', [
            'assurance_list' => $results,
        ]);
    }

    #[Route('/assurance/search-ajax', name: 'assurance_search_ajax', methods: ['GET'])]
    public function searchAjax(Request $request, AssuranceRepository $repository): JsonResponse
    {
        try {
            // Récupération des paramètres de recherche
            $searchNomAssureur = $request->query->get('search_nom_assureur', '');
            $searchTypeCouverture = $request->query->get('search_type_couverture', '');
            $searchDateDebut = $request->query->get('search_date_debut', '');
            $searchDateFin = $request->query->get('search_date_fin', '');
            $sortBy = $request->query->get('sort_by', 'date_debut');
            $sortOrder = $request->query->get('sort_order', 'DESC');

            // Log des paramètres de recherche
            error_log(sprintf(
                'Search params: nom=%s, type=%s, debut=%s, fin=%s, sort=%s, order=%s',
                $searchNomAssureur,
                $searchTypeCouverture,
                $searchDateDebut,
                $searchDateFin,
                $sortBy,
                $sortOrder
            ));

            // Création du QueryBuilder
            $qb = $repository->createQueryBuilder('a');

            // Ajout des conditions de recherche
            if ($searchNomAssureur) {
                $qb->andWhere('a.NomAssureur LIKE :nom')->setParameter('nom', "%$searchNomAssureur%");
                error_log("Filtering by NomAssureur: " . $searchNomAssureur);
            }

            if ($searchTypeCouverture) {
                $qb->andWhere('a.TypeCouverture LIKE :type')->setParameter('type', "%$searchTypeCouverture%");
                error_log("Filtering by TypeCouverture: " . $searchTypeCouverture);
            }

            if ($searchDateDebut) {
                try {
                    $dateDebut = new \DateTime($searchDateDebut);
                    $qb->andWhere('a.dateDebut >= :debut')->setParameter('debut', $dateDebut);
                    error_log("Filtering by dateDebut >= " . $dateDebut->format('Y-m-d'));
                } catch (\Exception $e) {
                    error_log("Error parsing date_debut: " . $e->getMessage());
                }
            }

            if ($searchDateFin) {
                try {
                    $dateFin = new \DateTime($searchDateFin);
                    $qb->andWhere('a.dateFin <= :fin')->setParameter('fin', $dateFin);
                    error_log("Filtering by dateFin <= " . $dateFin->format('Y-m-d'));
                } catch (\Exception $e) {
                    error_log("Error parsing date_fin: " . $e->getMessage());
                }
            }

            // Conversion des paramètres de tri du format du formulaire au format de l'entité
            $sortFieldMap = [
                'date_debut' => 'dateDebut',
                'date_fin' => 'dateFin',
                'nom_assureur' => 'NomAssureur',
                'type_couverture' => 'TypeCouverture'
            ];

            $entitySortField = $sortFieldMap[$sortBy] ?? 'dateDebut';
            error_log("Using sort field: " . $entitySortField);

            // Validation de l'ordre de tri
            $validSortOrders = ['ASC', 'DESC'];
            $sortOrder = in_array(strtoupper($sortOrder), $validSortOrders) ? strtoupper($sortOrder) : 'DESC';
            error_log("Using sort order: " . $sortOrder);

            // Ajout du tri
            $qb->orderBy("a.$entitySortField", $sortOrder);

            // Log de la requête SQL générée
            $query = $qb->getQuery();
            error_log("Generated SQL: " . $query->getSQL());
            error_log("Parameters: " . json_encode($query->getParameters()));

            // Exécution de la requête
            $assuranceList = $query->getResult();

            // Log du nombre de résultats
            error_log(sprintf('Found %d results', count($assuranceList)));

            // Rendu du template partiel
            $html = $this->renderView('assurance/_assurance_list.html.twig', [
                'assurance_list' => $assuranceList,
            ]);

            // Retour de la réponse JSON
            return new JsonResponse([
                'success' => true,
                'html' => $html,
                'count' => count($assuranceList)
            ]);
        } catch (\Exception $e) {
            error_log('Error in searchAjax: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return new JsonResponse([
                'success' => false,
                'error' => 'Une erreur est survenue lors de la recherche: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/assurance/search-by-titulaire', name: 'assurance_search_by_titulaire', methods: ['GET'])]
    public function searchByTitulaire(Request $request, AssuranceRepository $repository): JsonResponse
    {
        try {
            $searchTerm = $request->query->get('search_term', '');
            
            // Log des paramètres de recherche
            error_log(sprintf('Searching by titulaire: %s', $searchTerm));

            $qb = $repository->createQueryBuilder('a')
                ->where('a.NomTitulaire LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->orderBy('a.NomTitulaire', 'ASC');

            $assuranceList = $qb->getQuery()->getResult();

            // Log du nombre de résultats
            error_log(sprintf('Found %d results for titulaire search', count($assuranceList)));

            $html = $this->renderView('assurance/_assurance_list.html.twig', [
                'assurance_list' => $assuranceList,
            ]);

            return new JsonResponse([
                'success' => true,
                'html' => $html,
                'count' => count($assuranceList)
            ]);
        } catch (\Exception $e) {
            error_log('Error in searchByTitulaire: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return new JsonResponse([
                'success' => false,
                'error' => 'Une erreur est survenue lors de la recherche: ' . $e->getMessage()
            ], 500);
        }
    }
}
