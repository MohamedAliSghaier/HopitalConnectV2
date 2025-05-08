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
     

class PatientRendezvousController extends AbstractController
{


    #[Route('/rendezvous/listpatient', name: 'rendezvous_listpatient')]
public function listRendezVous(
    Request $request,
    RendezvousRepository $repository,
    PaginatorInterface $paginator
): Response {
    
    $patientId = 1;

    
    $date = $request->query->get('date');
    $type = $request->query->get('type');

    
    $queryBuilder = $repository->createQueryBuilder('r')
        ->andWhere('r.PatientId = :PatientId')
        ->setParameter('PatientId', $patientId)
        ->orderBy('r.date', 'DESC')
        ->addOrderBy('r.start_time', 'ASC');

    
    if ($date) {
        $queryBuilder->andWhere('r.date = :date')
            ->setParameter('date', new \DateTime($date));
    }

    if ($type) {
        $queryBuilder->andWhere('r.type_consultation_id = :type')
            ->setParameter('type', $type);
    }

    
    $pagination = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1),
        10
    );

    return $this->render('rendez_vous/listPatient.html.twig', [
        'pagination' => $pagination,
        'search_date' => $date,
        'search_type' => $type
    ]);
}



    #[Route('/rendezvous/add', name: 'rendezvous_add')]
public function addRendezVous(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
{
    $patient = $entityManager->find(Patient::class,1);
    $newRendezvous = new Rendezvous();
    $form = $this->createForm(RendezvousType::class, $newRendezvous);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        
        $existingRendezvous = $entityManager->getRepository(Rendezvous::class)
            ->findOneBy([
                'medecinId' => $newRendezvous->getMedecinId(),
                'date' => $newRendezvous->getDate(),
                'start_time' => $newRendezvous->getStartTime(),
            ]);
        $newRendezvous->setPatientId($patient);

        if ($existingRendezvous) {
            $form->get('start_time')->addError(new FormError('Ce médecin a déjà un rendez-vous à cette heure.'));
        } 

        
$existingRendezvousPatient = $entityManager->getRepository(Rendezvous::class)
    ->findOneBy([
        'PatientId' => $patient, 
        'date' => $newRendezvous->getDate(),
        'start_time' => $newRendezvous->getStartTime(),
    ]);

if ($existingRendezvousPatient) {
    $form->get('start_time')->addError(new FormError('Vous avez déjà un rendez-vous à cette heure.'));
}

        
        $errors = $validator->validate($newRendezvous);
        if (count($errors) > 0 || $form->isValid() === false) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        } else {
            $entityManager->persist($newRendezvous);
            $entityManager->flush();

            return $this->redirectToRoute('rendezvous_listpatient');
        }
    }

    return $this->render('rendez_vous/add.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/rendezvous/details/{id}', name: 'rendezvous_details')]
    public function showRendezVousDetails(RendezvousRepository $repository, $id): Response
    {
        $rendezvous = $repository->find($id);
        return $this->render('rendez_vous/patientRendezVous.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/rendezvous/delete/{id}', name: 'rendezvous_delete')]
    public function deleteRendezVous(RendezvousRepository $repository, ManagerRegistry $managerRegistry, $id): Response
    {
        $rendezvous = $repository->find($id);
        $entityManager = $managerRegistry->getManager();
        $entityManager->remove($rendezvous);
        $entityManager->flush();
        
        return $this->redirectToRoute('rendezvous_listpatient');
    }

    #[Route('/rendezvous/update/{id}', name: 'rendezvous_update')]
public function updateRendezVous(Request $request, RendezvousRepository $repository, ManagerRegistry $managerRegistry, $id): Response
{
    $rendezvous = $repository->find($id);
    if (!$rendezvous) {
        throw $this->createNotFoundException('Rendezvous not found');
    }

    $form = $this->createForm(RendezvousType::class, $rendezvous);
    $form->handleRequest($request);

    // Vérification de la disponibilité du médecin et du patient
    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification si le médecin a déjà un rendez-vous à la même date et heure
        $existingRendezvous = $managerRegistry->getRepository(Rendezvous::class)
            ->findOneBy([
                'medecinId' => $rendezvous->getMedecinId(),
                'date' => $rendezvous->getDate(),
                'start_time' => $rendezvous->getStartTime(),
            ]);

        if ($existingRendezvous && $existingRendezvous->getId() !== $rendezvous->getId()) {
            $form->get('start_time')->addError(new FormError('Ce médecin a déjà un rendez-vous à cette heure.'));
        }

        // Vérification si le patient a déjà un rendez-vous à la même date et heure
        $existingRendezvousPatient = $managerRegistry->getRepository(Rendezvous::class)
            ->findOneBy([
                'PatientId' => $rendezvous->getPatientId(), // Utilisation de 'PatientId' ici
                'date' => $rendezvous->getDate(),
                'start_time' => $rendezvous->getStartTime(),
            ]);

        if ($existingRendezvousPatient && $existingRendezvousPatient->getId() !== $rendezvous->getId()) {
            $form->get('start_time')->addError(new FormError('Vous avez déjà un rendez-vous à cette heure.'));
        }

        // Si le formulaire est valide, on effectue les modifications
        if ($form->isValid()) {
        $entityManager = $managerRegistry->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('rendezvous_listpatient');
        }
    }

    return $this->render('rendez_vous/update.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/rendezvous/search', name: 'rendezvous_search')]
    public function searchRendezVous( EntityManagerInterface $entityManager,Request $request,RendezvousRepository $repository ): Response {
        $patientId = 1; // Static patient ID
    
        // Default to all rendezvous for that patient
        $results = $repository->createQueryBuilder('r')
            ->where('r.PatientId = :patientId')
            ->setParameter('patientId', $patientId)
            ->getQuery()
            ->getResult();
    
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search_term');
    
            if ($searchTerm) {
                $query = $entityManager->createQuery(
                    "SELECT r
                     FROM App\Entity\Rendezvous r
                     WHERE (r.date LIKE :searchTerm OR r.type_consultation_id LIKE :searchTerm)
                       AND r.patient = :patientId"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $query->setParameter('patientId', $patientId);
                $results = $query->getResult();
            }
        }
    
        return $this->render('rendez_vous/search.html.twig', [
            'rendezvous_list' => $results,
        ]);
    }
    




}