<?php




namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\FaceRecognitionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;

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

    #[Route('/login/face', name: 'app_login_face', methods: ['POST'])]
    public function loginWithFace(Request $request, FaceRecognitionService $faceRecognitionService, EntityManagerInterface $entityManager): Response
    {
        $photo = $request->files->get('photo');
        $role = $request->request->get('role');

        if (!$photo || !$role) {
            $this->addFlash('error', 'Veuillez sélectionner un rôle et télécharger une photo.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les utilisateurs ayant le rôle sélectionné
        $users = $entityManager->getRepository(Utilisateur::class)->findBy(['role' => $role]);

        foreach ($users as $user) {
            if ($faceRecognitionService->verifyUserFace($photo, $user->getEmail())) {
                // Connecter l'utilisateur et rediriger en fonction de son rôle
                return $this->redirectToRoute('admin_dashboard'); // Redirigez vers le tableau de bord administrateur
            }
        }

        $this->addFlash('error', 'Aucun visage correspondant trouvé pour le rôle sélectionné.');
        return $this->redirectToRoute('app_login');
    }

    private function redirectBasedOnRole(Utilisateur $utilisateur): Response
    {
        if ($utilisateur->getRole() === 'administrateur') {
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($utilisateur->getRole() === 'medecin') {
            return $this->redirectToRoute('medecin_dashboard');
        }

        if ($utilisateur->getRole() === 'patient') {
            return $this->redirectToRoute('patient_dashboard');
        }

        if ($utilisateur->getRole() === 'pharmacien') {
            return $this->redirectToRoute('pharmacien_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }
}