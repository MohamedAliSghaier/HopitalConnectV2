<?php




namespace App\Controller;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\FaceRecognitionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

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
        $captcha = $captchaGenerator->generateCaptcha();
    
        // Stocker le texte en session pour validation
        $request->getSession()->set('captcha', $captcha['text']);
    
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'captcha_image' => $captcha['image'] // Ajoutez cette ligne
        ]);
    }
    
    #[Route('/check-email', name: 'app_check_email')]
public function checkEmail(Request $request, UtilisateurRepository $userRepo): JsonResponse
{
    $email = $request->query->get('email');
    $user = $userRepo->findOneBy(['email' => $email]);

    if (!$user) {
        return $this->json(['exists' => false]);
    }

    return $this->json([
        'exists' => true,
        'photo' => $user->getPhoto() ?: 'default.png'
    ]);
}

#[Route('/login/face', name: 'app_login_face', methods: ['POST'])]
public function loginWithFace(Request $request, FaceRecognitionService $faceRecognitionService, EntityManagerInterface $entityManager): Response
{
    $photoCapturee = $request->files->get('photo'); // Photo prise en direct
    $email = $request->request->get('email');

    if (!$photoCapturee || !$email) {
        $this->addFlash('error', 'Veuillez saisir votre email et prendre une photo.');
        return $this->redirectToRoute('app_login');
    }

    // Récupérer l'utilisateur
    $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

    if (!$user || !$user->getPhoto()) {
        $this->addFlash('error', 'Utilisateur ou photo introuvable.');
        return $this->redirectToRoute('app_login');
    }

    // Chemin vers la photo stockée
    $cheminPhotoConnue = $this->getParameter('kernel.project_dir') . '/public/uploads/faces/' . $user->getPhoto();

    if (!file_exists($cheminPhotoConnue)) {
        $this->addFlash('error', 'Photo de référence manquante.');
        return $this->redirectToRoute('app_login');
    }

    // Comparaison avec la photo capturée
    if ($faceRecognitionService->compareFaces($photoCapturee->getPathname(), $cheminPhotoConnue)) {
        // Connexion réussie
        return $this->redirectToRoute('admin_dashboard'); // Redirige selon rôle si besoin
    }

    $this->addFlash('error', 'Le visage ne correspond pas.');
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