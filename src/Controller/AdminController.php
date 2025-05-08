<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Avis;
use App\Entity\Reclamation;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(EntityManagerInterface $em): Response
    {
        // Fetch all avis (reviews)
        $avisList = $em->getRepository(Avis::class)->findAll();

        // Fetch all reclamations (complaints)
        $reclamationList = $em->getRepository(Reclamation::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
        ]);
    }
}
