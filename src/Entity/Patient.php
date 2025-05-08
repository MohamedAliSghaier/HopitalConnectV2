<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Utilisateur;

#[ORM\Entity]
class Patient
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "patients")]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Utilisateur $id;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Dossiermedicale::class)]
    private Collection $dossiermedicales;

    #[ORM\OneToMany(mappedBy: "patient", targetEntity: Assurance::class)]
    private Collection $assurances;

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Avis::class)]
    private Collection $aviss;

    #[ORM\OneToMany(mappedBy: "PatientId", targetEntity: Rendezvous::class)]
    private Collection $rendezvouss;

    public function __construct()
    {
        $this->dossiermedicales = new ArrayCollection();
        $this->assurances = new ArrayCollection();
        $this->ordonnances = new ArrayCollection();
        $this->aviss = new ArrayCollection();
        $this->rendezvouss = new ArrayCollection();
    }

    public function getId(): Utilisateur
    {
        return $this->id;
    }

    public function setId(Utilisateur $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

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

    public function getAssurances(): Collection
    {
        return $this->assurances;
    }

    public function addAssurance(Assurance $assurance): self
    {
        if (!$this->assurances->contains($assurance)) {
            $this->assurances[] = $assurance;
            $assurance->setPatient($this);
        }

        return $this;
    }

    public function removeAssurance(Assurance $assurance): self
    {
        if ($this->assurances->removeElement($assurance)) {
            if ($assurance->getPatient() === $this) {
                $assurance->setPatient(null);
            }
        }

        return $this;
    }

    public function getOrdonnances(): Collection
    {
        return $this->ordonnances;
    }

    public function addOrdonnance(Ordonnance $ordonnance): self
    {
        if (!$this->ordonnances->contains($ordonnance)) {
            $this->ordonnances[] = $ordonnance;
            $ordonnance->setPatientId($this);
        }

        return $this;
    }

    public function removeOrdonnance(Ordonnance $ordonnance): self
    {
        if ($this->ordonnances->removeElement($ordonnance)) {
            if ($ordonnance->getPatientId() === $this) {
                $ordonnance->setPatientId(null);
            }
        }

        return $this;
    }

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

    public function getNom(): ?string
    {
        return $this->id ? $this->id->getNom() : null;
    }
}
