<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Avis;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class MedecinController extends AbstractController
{
    #[Route('/{id}', name: 'medecin_show')]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        // Fetch Medecin by its ID
        try { $medecin = $em->getRepository(Medecin::class)->find($id);

        // Check if Medecin exists
        if (!$medecin) {
            throw $this->createNotFoundException('Medecin not found');
        }





        // Fetch Avis and Reclamations for the Medecin
        $avisList = $em->getRepository(Avis::class)->findBy(['medecin' => $medecin]);
        $reclamationList = $em->getRepository(Reclamation::class)->findBy(['medecin' => $medecin]);

        $avgReview = $em->createQuery(
            'SELECT AVG(a.note) 
             FROM App\Entity\Avis a 
             WHERE a.medecin = :medecin'
        )->setParameter('medecin', $medecin)
         ->getSingleScalarResult();

        // Count number of reclamations
        $reclamationCount = $em->createQuery(
            'SELECT COUNT(r.id) 
             FROM App\Entity\Reclamation r 
             WHERE r.medecin = :medecin'
        )->setParameter('medecin', $medecin)
         ->getSingleScalarResult();

        

        return $this->render('medecin/showmedecin.html.twig', [
            'medecin' => $medecin,
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
            'avgReview' => $avgReview,
            'reclamationCount' => $reclamationCount,
        ]);}

        catch (\Exception $e) {
            return new Response('Error: ' . $e->getMessage());
        }
    }
}
