<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Utilisateur;
use App\Entity\Dossiermedicale;

#[ORM\Entity]
class Patient
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id")]
    private ?Utilisateur $utilisateur = null;// Ici, le champ id est un ManyToOne vers Utilisateur

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(type: "string", length: 255)]
    private string $adresse;

    #[ORM\OneToMany(mappedBy: "Id_PatientAs", targetEntity: Assurance::class)]
    private Collection $assurances;

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Dossiermedicale::class)]
    private Collection $dossiermedicales;

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Avis::class)]
    private Collection $aviss;

    #[ORM\OneToMany(mappedBy: "patient", targetEntity: Rendezvous::class)]
    private Collection $rendezvouss;

    public function __construct()
    {
        $this->dateNaissance = new \DateTime('2000-01-01'); // Valeur par défaut
        $this->adresse = 'Adresse par défaut'; // Valeur par défaut
        $this->assurances = new ArrayCollection();
        $this->dossiermedicales = new ArrayCollection();
        $this->ordonnances = new ArrayCollection();
        $this->aviss = new ArrayCollection();
        $this->rendezvouss = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->utilisateur ? $this->utilisateur->getId() : null;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        // Synchroniser le côté inverse de la relation
        if ($utilisateur !== null && $utilisateur->getPatient() !== $this) {
            $utilisateur->setPatient($this);  // Assure la relation inverse
        }

        return $this;
    }

  

    public function getDateNaissance(): \DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }
    


    // Relations avec Ordonnances
    public function getOrdonnances(): Collection
    {
        return $this->ordonnances;
    }

    public function addOrdonnance(Ordonnance $ordonnance): self
    {
        if (!$this->ordonnances->contains($ordonnance)) {
            $this->ordonnances[] = $ordonnance;
            $ordonnance->setPatient_id($this);
        }

        return $this;
    }

    public function removeOrdonnance(Ordonnance $ordonnance): self
    {
        if ($this->ordonnances->removeElement($ordonnance)) {
            if ($ordonnance->getPatient_id() === $this) {
                $ordonnance->setPatient_id(null);
            }
        }

        return $this;
    }

    // Relations avec Rendezvous
    public function getRendezvouss(): Collection
    {
        return $this->rendezvouss;
    }

    public function addRendezvous(Rendezvous $rendezvous): self
    {
        if (!$this->rendezvouss->contains($rendezvous)) {
            $this->rendezvouss[] = $rendezvous;
            $rendezvous->setPatientId($this);
        }

        return $this;
    }

    public function removeRendezvous(Rendezvous $rendezvous): self
    {
        if ($this->rendezvouss->removeElement($rendezvous)) {
            if ($rendezvous->getPatientId() === $this) {
                $rendezvous->setPatientId(null);
            }
        }

        return $this;
    }

    // Relations avec Assurance
    public function getAssurances(): Collection
    {
        return $this->assurances;
    }

    public function addAssurance(Assurance $assurance): self
    {
        if (!$this->assurances->contains($assurance)) {
            $this->assurances[] = $assurance;
            $assurance->setIdPatientAs($this);
        }

        return $this;
    }

    public function removeAssurance(Assurance $assurance): self
    {
        if ($this->assurances->removeElement($assurance)) {
            if ($assurance->getIdPatientAs() === $this) {
                $assurance->setIdPatientAs(null);
            }
        }

        return $this;
    }

    // Relations avec Dossiermedicale
    public function getDossiermedicales(): Collection
    {
        return $this->dossiermedicales;
    }

    public function addDossiermedicale(Dossiermedicale $dossiermedicale): self
    {
        if (!$this->dossiermedicales->contains($dossiermedicale)) {
            $this->dossiermedicales[] = $dossiermedicale;
            $dossiermedicale->setIdPatient($this);
        }

        return $this;
    }

    public function removeDossiermedicale(Dossiermedicale $dossiermedicale): self
    {
        if ($this->dossiermedicales->removeElement($dossiermedicale)) {
            if ($dossiermedicale->getIdPatient() === $this) {
                $dossiermedicale->setIdPatient(null);
            }
        }

        return $this;
    }

    // Relations avec Avis
    public function getAviss(): Collection
    {
        return $this->aviss;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->aviss->contains($avis)) {
            $this->aviss[] = $avis;
            $avis->setPatientId($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->aviss->removeElement($avis)) {
            if ($avis->getPatientId() === $this) {
                $avis->setPatientId(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->utilisateur ? $this->utilisateur->getNom() : null;
    }
}
