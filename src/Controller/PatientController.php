<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(Security $security): Response
    {
        // Get the logged-in user
        $user = $security->getUser();

        // Ensure the user is a patient
        if (!$user || !$user->getRole() === 'patient') {
            $this->addFlash('error', 'Vous devez être connecté en tant que patient pour accéder au tableau de bord.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('patient/dashboard.html.twig', [
            'patientName' => $user->getNom(), // Assuming the Utilisateur entity has a getNom() method
        ]);
    }
}