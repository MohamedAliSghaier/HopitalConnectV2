<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}