<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(type: "string", length: 50)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 100, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $mot_de_passe;

    #[ORM\Column(type: "string", length: 20)]
    private string $role = 'ROLE_USER';

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Administrateur::class)]
    private Collection $administrateurs;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Medecin::class)]
    private Collection $medecins;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Patient::class)]
    private Collection $patients;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Pharmacien::class)]
    private Collection $pharmaciens;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Reclamation::class)]
    private Collection $reclamations;

    public function __construct()
    {
        $this->administrateurs = new ArrayCollection();
        $this->medecins = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->pharmaciens = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }

    // Méthodes requises par UserInterface
    public function getRoles(): array
    {
        return ['ROLE_'.strtoupper($this->role)]; // Supposant que vous avez une propriété 'role'
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Si vous stockez des données temporaires sensibles, effacez-les ici
        // $this->plainPassword = null;
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

   
    public function setPassword(string $password): self
    {
        $this->mot_de_passe = $password; // Notez le '=' qui était manquant
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    // Alias pour la compatibilité
    public function getMot_de_passe(): string
    {
        return $this->getPassword();
    }

    public function setMot_de_passe(string $mot_de_passe): self
    {
        return $this->setPassword($mot_de_passe);
    }

    // Méthodes pour les relations
    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): void
    {
        $this->medecin = $medecin;
    }
    public function getAdministrateurs(): Collection
    {
        return $this->administrateurs;
    }

    public function addAdministrateur(Administrateur $administrateur): self
    {
        if (!$this->administrateurs->contains($administrateur)) {
            $this->administrateurs[] = $administrateur;
            $administrateur->setId($this);
        }

        return $this;
    }

    public function removeAdministrateur(Administrateur $administrateur): self
    {
        if ($this->administrateurs->removeElement($administrateur)) {
            // set the owning side to null (unless already changed)
            if ($administrateur->getId() === $this) {
                $administrateur->setId(null);
            }
        }

        return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations[] = $reclamation;
            $reclamation->setUtilisateur_id($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur_id() === $this) {
                $reclamation->setUtilisateur_id(null);
            }
        }

        return $this;
    }
}