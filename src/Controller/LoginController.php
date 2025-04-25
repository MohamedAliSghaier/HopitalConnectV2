<?php




namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le selon son rôle
        if ($this->getUser()) {
            return $this->redirectBasedOnRole($this->getUser());
        }

        // Récupérer l'erreur et le dernier email
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    private function redirectBasedOnRole($user): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('dashboard');
        }
        if ($this->isGranted('ROLE_MEDECIN')) {
            return $this->redirectToRoute('medecin_dashboard');
        }
        if ($this->isGranted('ROLE_PATIENT')) {
            return $this->redirectToRoute('patient_dashboard');
        }
        if ($this->isGranted('ROLE_PHARMACIEN')) {
            return $this->redirectToRoute('pharmacien_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }
};
