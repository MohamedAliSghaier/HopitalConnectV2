<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        $returnRoute = $this->getRouteBasedOnRole($user);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'return_route' => $returnRoute,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));

            if ($request->files->get('photo')) {
                $photo = $request->files->get('photo');
                $filename = uniqid().'.'.$photo->guessExtension();
                $photo->move($this->getParameter('photos_directory'), $filename);
                $user->setPhoto($filename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
        ]);
    }

    private function getRouteBasedOnRole(Utilisateur $user): string
{
    if ($user->getRole() === 'administrateur') {
        return 'admin_dashboard';
    }

    if ($user->getRole() === 'medecin') {
        return 'medecin_dashboard';
    }

    if ($user->getRole() === 'patient') {
        return 'patient_dashboard';
    }

    if ($user->getRole() === 'pharmacien') {
        return 'pharmacien_dashboard';
    }

    return 'app_home';
}
}
