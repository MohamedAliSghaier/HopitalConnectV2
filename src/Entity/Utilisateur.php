<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $nom;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $prenom;

    #[ORM\Column(type: "string", length: 100, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Veuillez saisir une adresse email valide.")]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $mot_de_passe;

    #[ORM\Column(type: "string", length: 20)]
    #[Assert\NotBlank(message: "Le rôle est obligatoire.")]
    #[Assert\Choice(
        choices: ["ROLE_USER", "administrateur", "medecin", "pharmacien"],
        message: "Le rôle sélectionné est invalide."
    )]
    private string $role = 'ROLE_USER';

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    #[Assert\NotBlank(message: "Le genre est obligatoire.")]
    #[Assert\Choice(choices: ["Homme", "Femme"], message: "Le genre doit être 'Homme' ou 'Femme'.")]
    private ?string $genre = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToOne(mappedBy: "utilisateur", targetEntity: Administrateur::class, cascade: ['persist', 'remove'])]
    private ?Administrateur $administrateur = null;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Medecin::class)]
    private Collection $medecins;
    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Patient::class)]
    private Collection $patients;
   #[ORM\OneToOne(mappedBy: "utilisateur", targetEntity: Patient::class, cascade: ["persist", "remove"])]
private ?Patient $patient = null;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Pharmacien::class)]
    private Collection $pharmaciens;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToOne(mappedBy: 'id', targetEntity: Medecin::class, cascade: ['persist', 'remove'])]
    private ?Medecin $medecin = null;

    #[ORM\OneToOne(mappedBy: "utilisateur", targetEntity: Pharmacien::class, cascade: ['persist', 'remove'])]
    private ?Pharmacien $pharmacien = null;
    

    public function __construct()
    {
        $this->medecins = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->pharmaciens = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }


private ?string $plaintext = null;

public function getPlaintext(): ?string
{
    return $this->plaintext;
}

public function setPlaintext(?string $plaintext): self
{
    $this->plaintext = $plaintext;
    return $this;
}

    // Méthodes requises par UserInterface
    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role)];
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // $this->plainPassword = null;
    }

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
        $this->mot_de_passe = $password;
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
    public function getMot_de_passe(): string
    {
        return $this->getPassword();
    }

    public function setMot_de_passe(string $mot_de_passe): self
    {
        return $this->setPassword($mot_de_passe);
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(?string $motDePasse): self
    {
        $this->mot_de_passe = $motDePasse;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        if ($medecin === null && $this->medecin !== null) {
            $this->medecin->setUtilisateur(null);
        }

        if ($medecin !== null && $medecin->getUtilisateur() !== $this) {
            $medecin->setUtilisateur($this);
        }

        $this->medecin = $medecin;

        return $this;
    }
    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        // Assure la relation bidirectionnelle
        if ($patient !== null && $patient->getUtilisateur() !== $this) {
            $patient->setUtilisateur($this);
        }

        $this->patient = $patient;
        return $this;
    }

    public function getPharmacien(): ?Pharmacien
    {
        return $this->pharmacien;
    }

    public function setPharmacien(?Pharmacien $pharmacien): self
    {
        if ($pharmacien === null && $this->pharmacien !== null) {
            $this->pharmacien->setUtilisateur(null);
        }

        if ($pharmacien !== null && $pharmacien->getUtilisateur() !== $this) {
            $pharmacien->setUtilisateur($this);
        }

        $this->pharmacien = $pharmacien;

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
            if ($reclamation->getUtilisateur_id() === $this) {
                $reclamation->setUtilisateur_id(null);
            }
        }

        return $this;
    }
}
