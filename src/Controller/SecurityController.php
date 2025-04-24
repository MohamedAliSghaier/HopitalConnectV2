<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Création du formulaire avec reCAPTCHA
        $form = $this->createFormBuilder()
            ->add('recaptcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'login',
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        // Validation du formulaire reCAPTCHA
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le reCAPTCHA a échoué. Veuillez réessayer.');
        }
    
        // Récupérer les erreurs d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Redirection si l'utilisateur est déjà connecté
        $user = $this->getUser();
        if ($user) {
            return $this->redirectBasedOnRole($user);
        }
    
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'recaptcha' => $form->createView(),
        ]);
    }

    private function redirectBasedOnRole($user): Response
    {
        
        if (in_array('ROLE_ADMINISTRATEUR', $user->getRoles(), true)) {
            return $this->redirectToRoute('dashboard'); // Page de l'administrateur
        }

        if (in_array('ROLE_MEDECIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('medecin_dashboard'); // Page du médecin
        }

        if (in_array('ROLE_PATIENT', $user->getRoles(), true)) {
            return $this->redirectToRoute('patient_dashboard'); // Page du patient
        }

        if (in_array('ROLE_PHARMACIEN', $user->getRoles(), true)) {
            return $this->redirectToRoute('pharmacien_dashboard'); // Page du pharmacien
        }

       
        return $this->redirectToRoute('app_home');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $roles = $user ? $user->getRoles() : [];

        return $this->render('administrateur/dashboard.html.twig', [
            'roles' => $roles,
        ]);
    }
}




