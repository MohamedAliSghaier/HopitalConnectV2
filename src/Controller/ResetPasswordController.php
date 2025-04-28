<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer, SessionInterface $session, FormFactoryInterface $formFactory, EntityManagerInterface $em)
    {
        // Création du formulaire pour l'email
        $form = $formFactory->createBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le code',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
    
            // Vérification si l'email existe dans la base de données
            $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
    
            if ($user) {
                // Générer un code à 6 chiffres
                $code = random_int(100000, 999999);
    
                // Stocker le code en session
                $session->set('reset_password_code', $code);
                $session->set('reset_password_email', $email);
                $session->set('reset_password_expires_at', (new \DateTime())->modify('+10 minutes')->getTimestamp());
    
                // Envoyer l'email
                $emailMessage = (new Email())
                    ->from('noreply@tonsite.com')
                    ->to($email)
                    ->subject('Votre code de réinitialisation')
                    ->text("Votre code de réinitialisation est : $code. Il est valable 10 minutes.");
    
                $mailer->send($emailMessage);
    
                $this->addFlash('success', 'Un code a été envoyé à votre email.');
                return $this->redirectToRoute('app_verify_reset_code');
            } else {
                $this->addFlash('error', 'Cet email n\'existe pas dans notre base de données.');
            }
        }
    
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

#[Route('/verify-code', name: 'app_verify_reset_code')]
public function verifyCode(Request $request, SessionInterface $session)
{
    if ($request->isMethod('POST')) {
        $inputCode = $request->request->get('reset_code');
        $savedCode = $session->get('reset_password_code');
        $expiresAt = $session->get('reset_password_expires_at');

        // 🔵 Ajoute ce log pour voir ce que tu reçois
        dump($inputCode, $savedCode);

        if (!$savedCode || time() > $expiresAt) {
            $this->addFlash('error', 'Code expiré. Veuillez recommencer.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ((string)$inputCode !== (string)$savedCode) { // Compare en tant que string
            $this->addFlash('error', 'Code incorrect.');
            return $this->redirectToRoute('app_verify_reset_code');
        }

        $this->addFlash('success', 'Code valide, veuillez définir votre nouveau mot de passe.');
        return $this->redirectToRoute('app_reset_password');
    }

    return $this->render('security/verify_reset_code.html.twig');
}

#[Route('/reset-password', name: 'app_reset_password')]
public function resetPassword(Request $request, SessionInterface $session, EntityManagerInterface $em)
{
    $email = $session->get('reset_password_email');

    if (!$email) {
        return $this->redirectToRoute('app_forgot_password');
    }

    if ($request->isMethod('POST')) {
        $newPassword = $request->request->get('new_password');

        // Vérification pour éviter que $newPassword soit null
        if ($newPassword && $email) {
            // Trouver l'utilisateur par email
            $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Hashage du mot de passe avant de le sauvegarder
                $user->setPassword($newPassword);
                $em->flush();

                // Nettoyer la session
                $session->remove('reset_password_code');
                $session->remove('reset_password_email');
                $session->remove('reset_password_expires_at');

                $this->addFlash('success', 'Mot de passe changé avec succès !');

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Utilisateur introuvable.');
            }
        }
    }

    return $this->render('security/reset_password.html.twig');
}

}
