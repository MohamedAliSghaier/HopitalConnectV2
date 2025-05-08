<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Utilisateur;
use Doctrine\Common\Collections\Collection;
use App\Entity\Dossiermedicale;

#[ORM\Entity]
class Patient
{

    #[ORM\Id]
#[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ["persist", "remove"])]
#[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE")]
private Utilisateur $utilisateur;



    #[ORM\Column(type: "date", name: "dateNaissance")]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(type: "string", length: 255)]
    private string $adresse;

   


    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance($value)
    {
        $this->dateNaissance = $value;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($value)
    {
        $this->adresse = $value;
    }

    #[ORM\OneToMany(mappedBy: "Id_PatientAs", targetEntity: Assurance::class)]
    private Collection $assurances;

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Dossiermedicale::class)]
    private Collection $dossiermedicales;

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Avis::class)]
    private Collection $aviss;


    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }
    
    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

        public function getAviss(): Collection
        {
            return $this->aviss;
        }
    
        public function addAvis(Avis $avis): self
        {
            if (!$this->aviss->contains($avis)) {
                $this->aviss[] = $avis;
                $avis->setPatient_id($this);
            }
    
            return $this;
        }
    
        public function removeAvis(Avis $avis): self
        {
            if ($this->aviss->removeElement($avis)) {
                // set the owning side to null (unless already changed)
                if ($avis->getPatient_id() === $this) {
                    $avis->setPatient_id(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;
}
