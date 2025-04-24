<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Patient;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UtilisateurController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
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

            $entityManager->persist($utilisateur);
    $entityManager->flush(); // Nécessaire pour avoir l'id

    // Création du patient lié
    $patient = new Patient();
    $patient->setUtilisateur($utilisateur);

    $entityManager->persist($patient);
    $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $roles = $user ? $user->getRoles() : []; 
    
        dump($roles);
        

    
        return $this->render('administrateur/dashboard.html.twig', [
            'roles' => $roles,
        ]);
    }

}