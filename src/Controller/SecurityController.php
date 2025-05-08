<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use App\Form\LoginFormType;  // Assure-toi que ce formulaire est bien importé

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Créer le formulaire de login
        $loginForm = $this->createForm(LoginFormType::class);

        // Récupérer les données soumises
        $loginForm->handleRequest($request);

        // Récupérer l'erreur et le dernier email
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifier si l'utilisateur est déjà authentifié
        $user = $this->getUser();
        if ($user) {
            return $this->redirectBasedOnRole($user); // Rediriger selon le rôle de l'utilisateur
        }

        return $this->render('security/login.html.twig', [
            'loginForm' => $loginForm->createView(), // Passer le formulaire à la vue
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    private function redirectBasedOnRole($user): Response
    {
        // Vérifier les rôles de l'utilisateur et rediriger vers la page appropriée
        if (in_array('ROLE_ADMINISTRATEUR', $user->getRoles(), true)) {
            return $this->redirectToRoute('dashboard');  // Page de l'administrateur
        }

        if (in_array('ROLE_MEDECIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('medecin_dashboard');  // Page du médecin
        }

        if (in_array('ROLE_PATIENT', $user->getRoles(), true)) {
            return $this->redirectToRoute('patient_dashboard');  // Page du patient
        }

        if (in_array('ROLE_PHARMACIEN', $user->getRoles(), true)) {
            return $this->redirectToRoute('pharmacien_dashboard');  // Page du pharmacien
        }

        // Par défaut
        return $this->redirectToRoute('app_home');
    }
    #[Route('/logout', name: 'app_logout')]
public function logout(): void
{
    // Cette méthode peut rester vide - elle sera interceptée par le firewall
    throw new \LogicException('Cette méthode sera interceptée par le système de déconnexion');
}
}



