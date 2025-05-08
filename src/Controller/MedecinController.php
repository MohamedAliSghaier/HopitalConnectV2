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
use App\Entity\Medecin;
use App\Entity\Avis;
use App\Entity\Analyse;
use App\Form\AnalyseType;
use App\Repository\AnalyseRepository;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ordonnance;
use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Utilisateur; 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_MEDECIN')]
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
    #[Route('/medecin/AvisEtReclamation', name: 'medecin_AvisEtReclamation')]
    public function show( EntityManagerInterface $em): Response
    {
        $id = $this->getUser()->getId();
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

        

        return $this->render('medecin/AvisEtReclamation.html.twig', [
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



    #[Route('/medecin/patients', name: 'medecin_patients')]
    public function listPatients(
        Request $request,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        
        $medecinId = $this->getUser()->getId();
        $searchQuery = $request->query->get('search');
        
        // Get all patients who have had appointments with this doctor
        $qb = $entityManager->createQueryBuilder();
        $qb->select('DISTINCT p')
           ->from('App\Entity\Patient', 'p')
           ->innerJoin('p.rendezvouss', 'r')
           ->where('r.medecin = :medecinId')
           ->setParameter('medecinId', $medecinId);
        
        if ($searchQuery) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('p.nom', ':search'),
                    $qb->expr()->like('p.prenom', ':search'),
                    $qb->expr()->like('p.email', ':search')
                )
            )
            ->setParameter('search', '%' . $searchQuery . '%');
        }

        $query = $qb->getQuery();
        $patients = $query->getResult();
        
        return $this->render('medecin/patients.html.twig', [
            'patients' => $patients,
            'search_query' => $searchQuery
        ]);
    }

    #[Route('/medecin/patient/{id}', name: 'medecin_patient_details')]
    public function viewPatientDetails(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_MEDECIN');
        
        $patient = $entityManager->getRepository('App\Entity\Patient')->find($id);
        if (!$patient) {
            throw $this->createNotFoundException('Patient not found');
        }
        
        // Get patient's medical record
        $dossierMedical = $entityManager->getRepository('App\Entity\Dossiermedicale')
            ->findOneBy(['id_patient' => $patient]);
            
        // Get patient's insurance information
        $assurance = $entityManager->getRepository('App\Entity\Assurance')
            ->findOneBy(['patient' => $patient]);
        
        return $this->render('medecin/patient_details.html.twig', [
            'patient' => $patient,
            'dossier_medical' => $dossierMedical,
            'assurance' => $assurance
        ]);
    }

    


    #[Route('/medecin/listmedecin', name: 'rendezvous_listmedecin')]
    public function listRendezVous(
        Request $request,
        RendezvousRepository $repository,
        PaginatorInterface $paginator
    ): Response {
        // 🔒 Static Medecin ID
        $medecinId = $this->getUser()->getId();


    
        // 🔍 Retrieve filter parameters
        $date = $request->query->get('date');
        $type = $request->query->get('type');
    
        // 🏗️ Build the query filtered by medecin
        $queryBuilder = $repository->createQueryBuilder('r')
            ->andWhere('r.medecin = :medecinId')
            ->setParameter('medecinId', $medecinId)
            ->orderBy('r.date', 'DESC')
            ->addOrderBy('r.startTime', 'ASC');
    
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
    
        return $this->render('medecin/listMedecin.html.twig', [
            'pagination' => $pagination,
            'search_date' => $date,
            'search_type' => $type
        ]);
    }
  
   
    #[Route('/analyse/add', name: 'analyse_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry, RendezvousRepository $rendezvousRepository): Response
    {
        $em = $managerRegistry->getManager();
        $utilisateur = $this->getUser(); // Get the logged-in user (Utilisateur)
    
        // Fetch the Medecin entity associated with the logged-in Utilisateur
        $medecin = $em->getRepository(Medecin::class)->findOneBy(['utilisateur' => $utilisateur]);
    
        if (!$medecin) {
            throw $this->createNotFoundException('Medecin not found for the logged-in user.');
        }
    
        // Fetch rendezvous for the logged-in doctor
        $rendezvousList = $rendezvousRepository->findBy(['medecin' => $medecin]);
    
        $analyse = new Analyse();
        $form = $this->createForm(AnalyseType::class, $analyse, [
            'rendezvous_choices' => $rendezvousList, // Pass the filtered rendezvous list to the form
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $analyse->setMedecin($medecin); // Set the Medecin instance
            $analyse->setPatient($analyse->getRendezvous()->getPatient());
    
            $em->persist($analyse);
            $em->flush();
    
            $this->addFlash('success', 'Analysis added successfully.');
    
            return $this->redirectToRoute('analyse_list');
        }
    
        return $this->render('medecin/analyse/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  


    #[Route('/analyse/list', name: 'analyse_list')]
    public function list(AnalyseRepository $repository, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MEDECIN'); // Ensure only Medecin users can access this route
    
        $em = $managerRegistry->getManager();
        $utilisateur = $this->getUser(); // Get the logged-in user (Utilisateur)
    
        // Fetch the Medecin entity associated with the logged-in Utilisateur
        $medecin = $em->getRepository(Medecin::class)->findOneBy(['utilisateur' => $utilisateur]);
    
        if (!$medecin) {
            throw $this->createNotFoundException('Medecin not found for the logged-in user.');
        }
    
        // Fetch analyses for the logged-in doctor
        $analyses = $repository->createQueryBuilder('a')
            ->join('a.rendezvous', 'r')
            ->where('r.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->getQuery()
            ->getResult();
    
        return $this->render('medecin/analyse/list.html.twig', [
            'analyses' => $analyses,
        ]);
    }


#[Route('/analyse/update/{id}', name: 'analyse_update')]
public function update(
    Analyse $analyse,
    Request $request,
    ManagerRegistry $managerRegistry,
    RendezvousRepository $rendezvousRepository
): Response {
    $em = $managerRegistry->getManager();
    $utilisateur = $this->getUser(); // Get the logged-in user (Utilisateur)

    // Fetch the Medecin entity associated with the logged-in Utilisateur
    $medecin = $em->getRepository(Medecin::class)->findOneBy(['utilisateur' => $utilisateur]);

    if (!$medecin) {
        throw $this->createNotFoundException('Medecin not found for the logged-in user.');
    }

    // Fetch rendezvous for the logged-in doctor
    $rendezvousList = $rendezvousRepository->findBy(['medecin' => $medecin]);

    // Create the form with the filtered rendezvous list
    $form = $this->createForm(AnalyseType::class, $analyse, [
        'rendezvous_choices' => $rendezvousList, // Pass the filtered rendezvous list to the form
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $analyse->setMedecin($medecin); // Ensure the Medecin instance is set
        $analyse->setPatient($analyse->getRendezvous()->getPatient()); // Update the patient based on the selected rendezvous

        $em->flush();

        $this->addFlash('success', 'Analysis updated successfully.');

        return $this->redirectToRoute('analyse_list');
    }

    return $this->render('medecin/analyse/update.html.twig', [
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
    
    #[Route('/analyse/details/{id}', name: 'analyse_details')]
    public function details(Analyse $analyse): Response
    {
        return $this->render('medecin/analyse/details.html.twig', [
            'analyse' => $analyse,
        ]);
    }


}
