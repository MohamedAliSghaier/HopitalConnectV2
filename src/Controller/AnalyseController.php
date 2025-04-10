<?php

namespace App\Controller;

use App\Entity\Analyse;
use App\Form\AnalyseType;
use App\Repository\AnalyseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyseController extends AbstractController
{
    #[Route('/analyse/list', name: 'analyse_list')]
    public function list(AnalyseRepository $repository): Response
    {
        return $this->render('analyse/list.html.twig', [
            'analyses' => $repository->findAll(),
        ]);
    }

    #[Route('/analyse/add', name: 'analyse_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $analyse = new Analyse();
        $form = $this->createForm(AnalyseType::class, $analyse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->persist($analyse);
            $em->flush();

            return $this->redirectToRoute('analyse_list');
        }

        return $this->render('analyse/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/analyse/details/{id}', name: 'analyse_details')]
    public function details(Analyse $analyse): Response
    {
        return $this->render('analyse/details.html.twig', [
            'analyse' => $analyse,
        ]);
    }

    #[Route('/analyse/update/{id}', name: 'analyse_update')]
    public function update(Analyse $analyse, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(AnalyseType::class, $analyse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('analyse_list');
        }

        return $this->render('analyse/update.html.twig', [
            'form' => $form->createView(),
            'analyse' => $analyse,
        ]);
    }

    #[Route('/analyse/delete/{id}', name: 'analyse_delete')]
    public function delete(Analyse $analyse, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $em->remove($analyse);
        $em->flush();

        return $this->redirectToRoute('analyse_list');
    }

    #[Route('/analyse/search', name: 'analyse_search')]
    public function search(Request $request, AnalyseRepository $repository): Response
    {
        $results = [];
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');
            $results = $repository->searchByTerm($searchTerm);
        }

        return $this->render('analyse/search.html.twig', [
            'analyses' => $results,
        ]);
    }
}