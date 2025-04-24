<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ordonnance;

class MedecinController extends AbstractController
{
    #[Route('/medecin/dashboard', name: 'medecin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');

        return $this->render('medecin/medecin_dashboard.html.twig', [
            'medecinName' => $this->getUser()->getNom(),
        ]);
    }
}
