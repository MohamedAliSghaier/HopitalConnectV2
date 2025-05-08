<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;


use App\Entity\Avis;
use App\Entity\Reclamation;
use App\Entity\Patient;
use App\Entity\Medecin;
use App\Entity\Utilisateur;
use App\Entity\Rendezvous;


 // Make sure this line is present


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

use App\Form\AvisType;
use App\Form\ReclamationType;



class FeedbackController extends AbstractController
{

    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendTestEmail(MailerInterface $mailer)
    {
        // Create the email
        $email = (new Email())
            ->from('dalisghaier78910@gmail.com')  // Replace with your email
            ->to('masghaier.contact@gmail.com')  // Replace with the recipient's email
            ->subject('Test Email from Symfony')
            ->text('This is a test email sent from Symfony');

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Test email sent successfully!');
            } catch (\Exception $e) {
                // Log detailed error information
                $this->get('logger')->error('Mailer error: ' . $e->getMessage());
                $this->get('logger')->error('Mailer stack trace: ' . $e->getTraceAsString());
                $this->addFlash('error', 'Error: ' . $e->getMessage());
            }

        // Redirect back to the index page or any other page
        return $this->redirectToRoute('index');
    }


    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager , MailerInterface $mailer , ValidatorInterface $validator): Response
    {

        $avisList = $entityManager->getRepository(Avis::class)->findAll();
        $reclamationList = $entityManager->getRepository(Reclamation::class)->findAll();
        // Get the list of reviews and complaints
        $patient = $entityManager->find(Patient::class, 3);
        

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
        return $this->redirectToRoute('index');
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
            $utilisateurPatient = $entityManager->find(Utilisateur::class, 1); // Patient's Utilisateur ID

            $medecinIdR = $reclamation->getMedecin(); 
        
            if (!$utilisateurPatient || !$medecinIdR) {
                $this->addFlash('error', 'Patient ou Médecin non trouvé pour la réclamation.');
                return $this->redirectToRoute('index');
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

        
            return $this->redirectToRoute('index');
        }
    
        // Pass form and lists to the view
        return $this->render('default/index.html.twig', [
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
            'avisForm' => $avisForm->createView(),
            'reclamationForm' => $reclamationForm->createView(), 
            'isEdit' => $isEdit,
            'isEditReclamation' => $isEditReclamation,

        ]);
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
