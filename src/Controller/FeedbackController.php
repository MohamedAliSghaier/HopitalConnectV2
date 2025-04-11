<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $avisList = $entityManager->getRepository(Avis::class)->findAll();
        $reclamationList = $entityManager->getRepository(Reclamation::class)->findAll();

        return $this->render('default/index.html.twig', [
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
        ]);
    }

    #[Route('/submit-avis', name: 'submit_avis', methods: ['POST'])]
    public function submitAvis(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');

        $avis = new Avis();
        $avis->setNote($rating);
        $avis->setCommentaire($comment);

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

    #[Route('/submit-reclamation', name: 'submit_reclamation', methods: ['POST'])]
    public function submitReclamation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issueDescription = $request->request->get('description');

        $reclamation = new Reclamation();
        $reclamation->setDescription($issueDescription);

        $entityManager->persist($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

    // Edit Avis
    #[Route('/edit-avis/{id}', name: 'edit_avis', methods: ['GET', 'POST'])]
    public function editAvis(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager->getRepository(Avis::class)->find($id);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        if ($request->isMethod('POST')) {
            $avis->setNote($request->request->get('rating'));
            $avis->setCommentaire($request->request->get('comment'));

            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('default/edit_avis.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Add this new method to handle the update of Avis
#[Route('/update-avis/{id}', name: 'update_avis', methods: ['POST'])]
public function updateAvis(int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    $avis = $entityManager->getRepository(Avis::class)->find($id);

    if (!$avis) {
        throw $this->createNotFoundException('Avis not found');
    }

    // Get new data from the form
    $rating = $request->request->get('rating');
    $comment = $request->request->get('comment');

    // Update Avis entity
    $avis->setNote($rating);
    $avis->setCommentaire($comment);

    // Persist and flush changes to database
    $entityManager->flush();

    // Redirect to the index page after successful update
    return $this->redirectToRoute('index');
}




    // Delete Avis
    #[Route('/delete-avis/{id}', name: 'delete_avis', methods: ['POST'])]
    public function deleteAvis(int $id, EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager->getRepository(Avis::class)->find($id);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

    // Edit Reclamation
    #[Route('/edit-reclamation/{id}', name: 'edit_reclamation', methods: ['GET', 'POST'])]
    public function editReclamation(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        if ($request->isMethod('POST')) {
            $reclamation->setDescription($request->request->get('description'));

            $entityManager->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('default/edit_reclamation.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    // Delete Reclamation
    #[Route('/delete-reclamation/{id}', name: 'delete_reclamation', methods: ['POST'])]
    public function deleteReclamation(int $id, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        $entityManager->remove($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }
}
