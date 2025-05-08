<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;


use App\Entity\Avis;
use App\Entity\Reclamation;
use App\Entity\Patient;
use App\Entity\Medecin;
use App\Entity\Utilisateur;
use App\Entity\Analyse;
use App\Entity\Rendezvous;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\AvisType;
use App\Form\ReclamationType;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;



class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PATIENT');

        return $this->render('patient/patient_dashboard.html.twig', [
            'patientName' => $this->getUser()->getNom(), // Exemple : afficher le nom du patient
        ]);
    }









    #[Route('/patient/AvisEtReclamation', name: 'patient_AvisEtReclamation')]
    public function index(Request $request, EntityManagerInterface $entityManager , MailerInterface $mailer , ValidatorInterface $validator): Response
    {

        $avisList = $entityManager->getRepository(Avis::class)->findAll();
        $reclamationList = $entityManager->getRepository(Reclamation::class)->findAll();
        // Get the list of reviews and complaints
        $patient = $entityManager->find(Patient::class, $this->getUser()->getId());
        

    // Fetch all Medecins the patient has rendezvous with
    $rendezVousList = $entityManager->getRepository(Rendezvous::class)
        ->findBy(['patient' => $patient]);

    // Get a list of Medecins from the rendezvous
    $medecins = [];
    foreach ($rendezVousList as $rendezVous) {
        $medecins[] = $rendezVous->getMedecin();
    }

    // Handle the Avis form
    $editAvisId = $request->query->get('edit_avis');
    $avis = $editAvisId ? $entityManager->getRepository(Avis::class)->find($editAvisId) : new Avis();
    $isEdit = $editAvisId !== null;

    // Create the Avis form and pass the filtered Medecins
    $avisForm = $this->createForm(AvisType::class, $avis, [
        'medecins' => $medecins, // Pass the filtered Medecins to the form
    ]);
    $avisForm->handleRequest($request);

    if ($avisForm->isSubmitted() && $avisForm->isValid()) {

        $errors = $validator->validate($avis);

            if (count($errors) > 0) {
                // Handle the errors, e.g., return them in the response
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
            
            // Process the form as usual
        $medecinId = $avis->getMedecin(); 


        $avis->setPatient($patient);
        $avis->setMedecin($medecinId);
        $avis->setDate_avis(new \DateTimeImmutable());
    
        $entityManager->persist($avis);
        $entityManager->flush();

        $this->addFlash('success', 'Avis soumis avec succès.');
        return $this->redirectToRoute('patient_AvisEtReclamation');
        }
        $editReclamationId = $request->query->get('edit_reclamation');
        $reclamation = $editReclamationId
        ? $entityManager->getRepository(Reclamation::class)->find($editReclamationId)
        : new Reclamation();

        $isEditReclamation = $editReclamationId !== null;


        $reclamationForm = $this->createForm(ReclamationType::class, $reclamation, [
            'medecins' => $medecins, // Pass the filtered Medecins to the form
        ]);

        $reclamationForm->handleRequest($request);
        if ($reclamationForm->isSubmitted() && $reclamationForm->isValid()) {

            $errors = $validator->validate($reclamation);

            if (count($errors) > 0) {
                // Handle the errors, e.g., return them in the response
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
            $utilisateurPatient = $entityManager->find(Utilisateur::class, $this->getUser()->getId()); // Patient's Utilisateur ID

            $medecinIdR = $reclamation->getMedecin(); 
        
            if (!$utilisateurPatient || !$medecinIdR) {
                $this->addFlash('error', 'Patient ou Médecin non trouvé pour la réclamation.');
                return $this->redirectToRoute('patient_AvisEtReclamation');
            }
                       
            $reclamation->setUtilisateur($utilisateurPatient);
            $reclamation->setMedecin($medecinIdR);
            $reclamation->setDateReclamation(new \DateTime());
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $logoPath = $this->getParameter('kernel.project_dir') . '/public/assets/images/logo.png';



            $email = (new Email())
            ->from('dalisghaier78910@gmail.com')
            ->to('sghaier.mohamedali@esprit.tn') // you can make it dynamic
            ->subject('Nouvelle Réclamation')
            ->html('<img src="cid:logo_cid" alt="Logo" style="width: 150px; height: auto;" />
            <p>Une nouvelle réclamation a été soumise :</p>
            <ul>
                <li><strong>Sujet:</strong> ' . $reclamation->getSujet() . '</li>
                <li><strong>Description:</strong> ' . $reclamation->getDescription() . '</li>
            </ul>')
            ->embedFromPath($logoPath, 'logo_cid');

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Réclamation envoyée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            }

        
            return $this->redirectToRoute('patient_AvisEtReclamation');
        }
    
        // Pass form and lists to the view
        return $this->render('patient/AvisEtReclamation.html.twig', [
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
            'avisForm' => $avisForm->createView(),
            'reclamationForm' => $reclamationForm->createView(), 
            'isEdit' => $isEdit,
            'isEditReclamation' => $isEditReclamation,

        ]);
    } 

    #[Route('/delete-avis/{id}', name: 'delete_avis', methods: ['POST'])]
    public function deleteAvis(int $id, EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager->getRepository(Avis::class)->find($id);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('patient_AvisEtReclamation');
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
            return $this->redirectToRoute('patient_AvisEtReclamation');
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
        return $this->redirectToRoute('patient_AvisEtReclamation');
    }





    #[Route('/patient/rendezvous', name: 'rendezvous_listpatient')]
public function listRendezVous(
    Request $request,
    RendezvousRepository $repository,
    EntityManagerInterface $entityManager,
    PaginatorInterface $paginator
): Response {
    // 🔒 ID du patient (statique pour le moment)
    $user = $this->getUser();
    $patientId = $entityManager->find(Patient::class, $this->getUser()->getId());

    // 🔍 Récupérer les paramètres de recherche
    $date = $request->query->get('date');
    $type = $request->query->get('type');

    // 🏗️ Créer la requête avec filtre sur le patient
    $queryBuilder = $repository->createQueryBuilder('r')
        ->andWhere('r.patient = :PatientId')
        ->setParameter('PatientId', $patientId)
        ->orderBy('r.date', 'DESC')
        ->addOrderBy('r.startTime', 'ASC');

    // 🧲 Ajouter les filtres facultatifs
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

    return $this->render('patient/listPatient.html.twig', [
        'pagination' => $pagination,
        'search_date' => $date,
        'search_type' => $type
    ]);
}



    #[Route('/rendezvous/add', name: 'rendezvous_add')]
public function addRendezVous(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
{
    $user = $this->getUser();
    $patient = $entityManager->find(Patient::class, $this->getUser()->getId());
    $newRendezvous = new Rendezvous();
    $form = $this->createForm(RendezvousType::class, $newRendezvous);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        // Vérification si le médecin a déjà un rendez-vous à la même date et heure
        $existingRendezvous = $entityManager->getRepository(Rendezvous::class)
            ->findOneBy([
                'medecin' => $newRendezvous->getMedecin(),
                'date' => $newRendezvous->getDate(),
                'startTime' => $newRendezvous->getStartTime(),
            ]);
        $newRendezvous->setPatient($patient);

        if ($existingRendezvous) {
            $form->get('startTime')->addError(new FormError('Ce médecin a déjà un rendez-vous à cette heure.'));
        }
        $existingRendezvousPatient = $entityManager->getRepository(Rendezvous::class)
    ->findOneBy([
        'patient' => $patient, // Utilisation de 'PatientId' ici
        'date' => $newRendezvous->getDate(),
        'startTime' => $newRendezvous->getStartTime(),
    ]);

if ($existingRendezvousPatient) {
    $form->get('startTime')->addError(new FormError('Vous avez déjà un rendez-vous à cette heure.'));
}

        // Validation manuelle
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

    return $this->render('patient/add.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/rendezvous/details/{id}', name: 'rendezvous_details')]
    public function showRendezVousDetails(RendezvousRepository $repository, $id): Response
    {
        $rendezvous = $repository->find($id);
        return $this->render('patient/patientRendezVous.html.twig', [
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

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $managerRegistry->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('rendezvous_listpatient');
        }

        return $this->render('patient/update.html.twig', [
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
                     WHERE (r.date LIKE :searchTerm OR r.typeConsultationId LIKE :searchTerm)
                       AND r.patient = :patientId"
                );
                $query->setParameter('searchTerm', '%' . $searchTerm . '%');
                $query->setParameter('patientId', $patientId);
                $results = $query->getResult();
            }
        }
    
        return $this->render('patient/search.html.twig', [
            'rendezvous_list' => $results,
        ]);
    }

  
  
    #[Route('/patient/analyses', name: 'patient_analyses')]
    public function listAnalyses(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get the logged-in user (Utilisateur)
    
        // Fetch the Patient entity associated with the logged-in user
        $patient = $entityManager->getRepository(Patient::class)->findOneBy(['utilisateur' => $user]);
    
        if (!$patient) {
            throw $this->createNotFoundException('Patient not found for the logged-in user.');
        }
    
        // Fetch analyses for the logged-in patient
        $analyses = $entityManager->getRepository(Analyse::class)->findBy(['patient' => $patient]);
    
        return $this->render('patient/listanalyse.html.twig', [
            'analyses' => $analyses,
        ]);
    }




}