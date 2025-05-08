<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PythonScriptService;
use Psr\Log\LoggerInterface;

class MedicationController extends AbstractController
{
    private PythonScriptService $pythonScriptService;
    private LoggerInterface $logger;

    public function __construct(PythonScriptService $pythonScriptService, LoggerInterface $logger)
    {
        $this->pythonScriptService = $pythonScriptService;
        $this->logger = $logger;
    }

    #[Route('/medications/search', name: 'medications_search', methods: ['POST'])]
    public function search(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['symptoms']) || !is_array($data['symptoms'])) {
                return new JsonResponse(['error' => 'Format de données invalide'], Response::HTTP_BAD_REQUEST);
            }

            $medications = $this->pythonScriptService->getMedicationsForSymptoms($data['symptoms']);
            return new JsonResponse($medications);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/medications', name: 'medications')]
    public function index(): Response
    {
        $this->logger->info('Accès à la page de recherche de médicaments');
        return $this->render('medication/index.html.twig');
    }
}
