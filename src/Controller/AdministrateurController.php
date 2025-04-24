<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Pharmacien;
use App\Entity\Administrateur;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdministrateurController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');
        return $this->render('administrateur/dashboard.html.twig');
    }

    #[Route('/admin/utilisateurs', name: 'admin_utilisateurs')]
    public function listUtilisateurs(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAll();

        return $this->render('administrateur/utilisateurs/list.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/admin/utilisateurs/new', name: 'admin_utilisateurs_new')]
    public function newUtilisateur(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, ['with_password' => true]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Mot de passe (sans hachage)
            $plainPassword = $form->get('password')->getData();
            $utilisateur->setPassword($plainPassword);
    
            // Photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();
    
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $utilisateur->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo');
                }
            }
    
            // Persister l'utilisateur
            $em->persist($utilisateur);
            $em->flush(); // Flush pour obtenir l'ID
    
            // Création de l'entité liée selon le rôle
            switch ($utilisateur->getRole()) {
                case 'medecin':
                    $medecin = new Medecin();
                    $medecin->setUtilisateur($utilisateur);
                
                    // Vérifiez si les champs sont remplis avant de les utiliser
                    $specialite = $form->get('specialite')->getData();
                    $numRdvMax = $form->get('num_rdv_max')->getData();
                
                    if ($specialite !== null) {
                        $medecin->setSpecialite($specialite);
                    } else {
                        $medecin->setSpecialite('Généraliste'); // Valeur par défaut
                    }
                
                    if ($numRdvMax !== null) {
                        $medecin->setNum_rdv_max($numRdvMax);
                    } else {
                        $medecin->setNum_rdv_max(10); // Valeur par défaut
                    }
                
                    $em->persist($medecin);
                    break;
    
                case 'pharmacien':
                    $pharmacien = new Pharmacien();
                    $pharmacien->setUtilisateur($utilisateur);
                    $em->persist($pharmacien);
                    break;
    
                case 'administrateur':
                    $admin = new Administrateur();
                    $admin->setUtilisateur($utilisateur);
                    $em->persist($admin);
                    break;
            }
    
            $em->flush(); // Flush des entités liées
    
            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('admin_utilisateurs');
        }
    
        return $this->render('administrateur/utilisateurs/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    private function uploadPhoto($file, $slugger): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        
        $file->move(
            $this->getParameter('photos_directory'),
            $newFilename
        );
        
        return $newFilename;
    }

    #[Route('/admin/utilisateurs/{id}/edit', name: 'admin_utilisateurs_edit', methods: ['GET', 'POST'])]
    public function editUtilisateur(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
      

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->isValid());
    dump($form->getErrors(true));
    dump($utilisateur); 
            // Gestion du mot de passe seulement si fourni
            if ($form->get('password')->getData()) {
                
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $form->get('password')->getData());
                $utilisateur->setPassword($hashedPassword);
            }
        
            // Gestion de la photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($utilisateur->getPhoto()) {
                    $oldPhoto = $this->getParameter('photos_directory').'/'.$utilisateur->getPhoto();
                    if (file_exists($oldPhoto)) {
                        unlink($oldPhoto);
                    }
                }
        
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
        
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $utilisateur->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de la photo: '.$e->getMessage());
                }
            }
        
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        return $this->render('administrateur/utilisateurs/edit.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/admin/utilisateurs/{id}/delete', name: 'admin_utilisateurs_delete', methods: ['POST'])]
    public function deleteUtilisateur(Request $request, Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $em->remove($utilisateur);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('admin_utilisateurs');
    }

    #[Route('/admin/statistiques', name: 'admin_statistiques')]
    public function statistiques(Request $request, UtilisateurRepository $UtilisateurRepository): Response
    {
        $utilisateurs = $UtilisateurRepository->findAll();

      
        $sort = $request->query->get('sort', 'id');
        if ($sort === 'nom') {
            usort($utilisateurs, fn($a, $b) => strcmp($a->getNom(), $b->getNom()));
        } elseif ($sort === 'id') {
            usort($utilisateurs, fn($a, $b) => $a->getId() <=> $b->getId());
        }

        
        $search = $request->query->get('search', '');
        if ($search) {
            $utilisateurs = array_filter($utilisateurs, fn($u) => stripos($u->getNom(), $search) !== false);
        }

        
        $rolesStats = [];
        foreach ($utilisateurs as $utilisateur) {
            foreach ($utilisateur->getRoles() as $role) {
                if (!isset($rolesStats[$role])) {
                    $rolesStats[$role] = 0;
                }
                $rolesStats[$role]++;
            }
        }

        return $this->render('administrateur/utilisateurs/statistiques.html.twig', [
            'utilisateurs' => $utilisateurs,
            'rolesStats' => $rolesStats,
            'search' => $search,
            'sort' => $sort,
        ]);
    }
}