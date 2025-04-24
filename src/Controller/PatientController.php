<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');

        return $this->render('patient/patient_dashboard.html.twig', [
            'patientName' => $this->getUser()->getNom(), // Exemple : afficher le nom du patient
        ]);
    }
}