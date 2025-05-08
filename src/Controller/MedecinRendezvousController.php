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
     

class MedecinRendezvousController extends AbstractController
{

    #[Route('/rendezvous/listmedecin', name: 'rendezvous_listmedecin')]
    public function listRendezVous(
        Request $request,
        RendezvousRepository $repository,
        PaginatorInterface $paginator
    ): Response {
        
        $medecinId = 2;
    
    
        $date = $request->query->get('date');
        $type = $request->query->get('type');
    
        
        $queryBuilder = $repository->createQueryBuilder('r')
    ->leftJoin('r.medecinId', 'm')  
    ->leftJoin('r.PatientId', 'p')  
    ->leftJoin('p.id', 'u')  
    ->addSelect('m')
    ->addSelect('p')
    ->addSelect('u')
    ->andWhere('r.medecinId = :medecinId')
    ->setParameter('medecinId', $medecinId)
    ->orderBy('r.date', 'DESC')
    ->addOrderBy('r.start_time', 'ASC');


    
    
        // 🧲 Add optional filters
        if ($date) {
            $queryBuilder->andWhere('r.date = :date')
                ->setParameter('date', new \DateTime($date));
        }
    
        if ($type) {
            $queryBuilder->andWhere('r.typeConsultationId = :type')
                ->setParameter('type', $type);
        }
    
        // 📄 Pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('rendez_vous/listMedecin.html.twig', [
            'pagination' => $pagination,
            'search_date' => $date,
            'search_type' => $type
        ]);
    }
    


}