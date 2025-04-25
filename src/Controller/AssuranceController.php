<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AssuranceController extends AbstractController
{
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
    public function list(AssuranceRepository $repository): Response
    {
        $assuranceList = $repository->findAll();
        return $this->render('assurance/list.html.twig', [
            'assurance_list' => $assuranceList,
        ]);
    }

    #[Route('/assurance/add', name: 'assurance_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $assurance = new Assurance();

        // Get the logged-in user
        $user = $security->getUser();

        // Ensure the logged-in user is a patient
        $patient = $entityManager->getRepository(\App\Entity\Patient::class)->findOneBy(['id' => $user]);
        if (!$patient) {
            $this->addFlash('error', 'You must be logged in as a patient to add an assurance.');
            return $this->redirectToRoute('assurance_list');
        }

        // Set the Patient in the Assurance
        $assurance->setPatient($patient);

        // Create the form
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($assurance);
                $entityManager->flush();

                $this->addFlash('success', 'Assurance added successfully!');
                return $this->redirectToRoute('assurance_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error saving assurance: ' . $e->getMessage());
            }
        }

        return $this->render('assurance/add.html.twig', [
            'form' => $form->createView(),
        ]);
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
    public function search(Request $request, AssuranceRepository $repository): Response
    {
        $searchTerm = $request->query->get('q') ?? $request->request->get('search_term');
        $results = [];

        if ($searchTerm) {
            $results = $repository->createQueryBuilder('a')
                ->where('a.nom LIKE :term OR a.type LIKE :term')
                ->setParameter('term', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        } else {
            $results = $repository->findAll();
        }

        return $this->render('assurance/search.html.twig', [
            'assurance_list' => $results,
            'search_term' => $searchTerm,
        ]);
    }
}