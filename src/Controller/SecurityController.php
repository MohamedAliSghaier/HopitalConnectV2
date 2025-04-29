<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpClient\HttpClient;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectBasedOnRole($this->getUser());
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
        
    }
    

    private function redirectBasedOnRole($user): Response
    {
        if (in_array('ROLE_ADMINISTRATEUR', $user->getRoles(), true)) {
            return $this->redirectToRoute('dashboard');
        }

        if (in_array('ROLE_MEDECIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('medecin_dashboard');
        }

        if (in_array('ROLE_PATIENT', $user->getRoles(), true)) {
            return $this->redirectToRoute('patient_dashboard');
        }

        if (in_array('ROLE_PHARMACIEN', $user->getRoles(), true)) {
            return $this->redirectToRoute('pharmacien_dashboard');
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




