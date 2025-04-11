<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Reclamation;

#[ORM\Entity]
class Utilisateur
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(type: "string", length: 50)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 100)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $mot_de_passe;

    #[ORM\Column(type: "string")]
    private string $role;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($value)
    {
        $this->prenom = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getMot_de_passe()
    {
        return $this->mot_de_passe;
    }

    public function setMot_de_passe($value)
    {
        $this->mot_de_passe = $value;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Administrateur::class)]
    private Collection $administrateurs;

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

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Medecin::class)]
    private Collection $medecins;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Patient::class)]
    private Collection $patients;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Pharmacien::class)]
    private Collection $pharmaciens;

    #[ORM\OneToMany(mappedBy: "utilisateur_id", targetEntity: Reclamation::class)]
    private Collection $reclamations;

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
