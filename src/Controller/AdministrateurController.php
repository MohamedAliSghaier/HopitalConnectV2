<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Avis;
use App\Entity\Reclamation;
use App\Entity\Pharmacien;
use App\Entity\Administrateur;
use App\Entity\Utilisateur;
use App\Entity\Medicament;
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
use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;    
use App\Repository\AnalyseRepository;    
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Patient;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\MedicamentRepository;

class AdministrateurController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');
        return $this->render('administrateur/dashboard.html.twig');
    }

    #[Route('/admin/AvisEtReclamation', name: 'admin_AvisEtReclamation')]
    public function ShowAvisEtReclamations(EntityManagerInterface $em): Response
    {
        $avisList = $em->getRepository(Avis::class)->findAll();
        $reclamationList = $em->getRepository(Reclamation::class)->findAll();

        return $this->render('administrateur/AvisEtReclamation.html.twig', [
            'avisList' => $avisList,
            'reclamationList' => $reclamationList,
        ]);
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
public function statistiques(Request $request, UtilisateurRepository $utilisateurRepository): Response
{
    $allUtilisateurs = $utilisateurRepository->findAll();

    // --- 1. Statistiques globales ---
    $rolesStats = [];
    $genreStats = [
        'Homme' => 0,
        'Femme' => 0,
      
    ];

    foreach ($allUtilisateurs as $utilisateur) {
        foreach ($utilisateur->getRoles() as $role) {
            $rolesStats[$role] = ($rolesStats[$role] ?? 0) + 1;
        }

        $genre = $utilisateur->getGenre();
        if ($genre && isset($genreStats[$genre])) {
            $genreStats[$genre]++;
        }
    }

    // --- 2. Appliquer tri + recherche uniquement à la liste affichée ---
    $utilisateurs = $allUtilisateurs;

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

    return $this->render('administrateur/utilisateurs/statistiques.html.twig', [
        'utilisateurs' => $utilisateurs,
        'rolesStats' => $rolesStats,
        'genreStats' => $genreStats,
        'search' => $search,
        'sort' => $sort,
    ]);
}




#[Route('/admin/listrendezvous', name: 'adminrendezvouslist')]
public function listRendezVous( Request $request, RendezvousRepository $repository, PaginatorInterface $paginator): Response {
    // Récupérer les paramètres de recherche
    $date = $request->query->get('date');
    $type = $request->query->get('type');

    // Créer la requête de base
    $queryBuilder = $repository->createQueryBuilder('r')
        ->orderBy('r.date', 'DESC')
        ->addOrderBy('r.startTime', 'ASC');

    // Ajouter les filtres si présents
    if ($date) {
        $queryBuilder->andWhere('r.date = :date')
           ->setParameter('date', new \DateTime($date));
    }

    if ($type) {
        $queryBuilder->andWhere('r.typeConsultationId = :type')
           ->setParameter('type', $type);
    }

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $queryBuilder, // Requête QueryBuilder
        $request->query->getInt('page', 1), // Numéro de page
        10 // Nombre d'éléments par page
    );

    return $this->render('administrateur/list.html.twig', [
        'pagination' => $pagination,
        'search_date' => $date,
        'search_type' => $type
    ]);
}



#[Route('/admin/medicaments/statistiques', name: 'admin_medicaments_stats')]
public function medicamentStats(EntityManagerInterface $entityManager): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');

    $medicamentRepository = $entityManager->getRepository(Medicament::class);

    // Nombre total de médicaments
    $totalMedicaments = $medicamentRepository->count([]);

    // Nombre de médicaments par pharmacien
    $medicamentsParPharmacien = $entityManager->createQuery(
        'SELECT u.id as userId, u.nom, u.prenom, COUNT(m.id) AS total
         FROM App\Entity\Medicament m
         JOIN m.pharmacien p
         JOIN p.utilisateur u
         GROUP BY u.id, u.nom, u.prenom'
    )->getResult();

    // Statistiques pour les graphiques
    $statsGraphiques = [
        'medicamentsParPharmacien' => [
            'labels' => array_map(function($item) {
                return $item['prenom'] . ' ' . $item['nom'];
            }, $medicamentsParPharmacien),
            'data' => array_map(function($item) {
                return $item['total'];
            }, $medicamentsParPharmacien),
            'backgroundColor' => [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
            ],
            'borderColor' => [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
            ],
        ],
        'evolutionStock' => [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            'data' => [65, 59, 80, 81, 56, 55],
            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
        ],
    ];

    return $this->render('administrateur/medicaments/statistiques.html.twig', [
        'totalMedicaments' => $totalMedicaments,
        'medicamentsParPharmacien' => $medicamentsParPharmacien,
        'statsGraphiques' => $statsGraphiques,
    ]);
}

#[Route('/admin/medicaments', name: 'admin_medicaments')]
public function listMedicaments(MedicamentRepository $medicamentRepository): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');
    $medicaments = $medicamentRepository->findAll();

    return $this->render('administrateur/medicaments/list.html.twig', [
        'medicaments' => $medicaments,
    ]);
}

#[Route('/administrateur/analyselist', name: 'adminanalyse')]
public function list(AnalyseRepository $repository): Response
{
    return $this->render('administrateur/analyselist.html.twig', [
        'analyses' => $repository->findAll(),
    ]);
}



}