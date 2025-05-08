<?php

namespace App\Controller;

use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\Utilisateur; 
use Knp\Component\Pager\PaginatorInterface;
     // Assure-toi que c'est bien cette interface

class RendezvousController extends AbstractController
{


    #[Route('/rendezvous/list', name: 'rendezvous_list')]
public function listRendezVous( Request $request, RendezvousRepository $repository, PaginatorInterface $paginator): Response {
    // Récupérer les paramètres de recherche
    $date = $request->query->get('date');
    $type = $request->query->get('type');

    // Créer la requête de base
    $queryBuilder = $repository->createQueryBuilder('r')
        ->orderBy('r.date', 'DESC')
        ->addOrderBy('r.start_time', 'ASC');

    // Ajouter les filtres si présents
    if ($date) {
        $queryBuilder->andWhere('r.date = :date')
           ->setParameter('date', new \DateTime($date));
    }

    if ($type) {
        $queryBuilder->andWhere('r.type_consultation_id = :type')
           ->setParameter('type', $type);
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $queryBuilder, // Requête QueryBuilder
        $request->query->getInt('page', 1), // Numéro de page
        10 // Nombre d'éléments par page
    );

    return $this->render('rendez_vous/list.html.twig', [
        'pagination' => $pagination,
        'search_date' => $date,
        'search_type' => $type
    ]);
}






}