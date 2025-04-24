<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Patient;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
    
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setMot_de_passe($form->get('plaintext')->getData());
            $utilisateur->setRole('patient');
    
            $photoFile = $form->get('photo')->getData();
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
            
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );
            $utilisateur->setPhoto($newFilename);
        }

        // Création du patient
        $patient = new Patient();
        $patient->setDateNaissance($form->get('dateNaissance')->getData());
        $patient->setAdresse($form->get('adresse')->getData());
        
        // Établir la relation bidirectionnelle
        $patient->setUtilisateur($utilisateur);
        $utilisateur->setPatient($patient);

        // Persist et flush
        $entityManager->persist($utilisateur);
        $entityManager->persist($patient);
        $entityManager->flush();
            // Persist et flush
         
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
