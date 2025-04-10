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
        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "patients")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Utilisateur $id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(type: "string", length: 255)]
    private string $adresse;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

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

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;

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
                // set the owning side to null (unless already changed)
                if ($ordonnance->getPatient_id() === $this) {
                    $ordonnance->setPatient_id(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "patient_id", targetEntity: Avis::class)]
    private Collection $aviss;

    #[ORM\OneToMany(mappedBy: "PatientId", targetEntity: Rendezvous::class)]
    private Collection $rendezvouss;

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
                // set the owning side to null (unless already changed)
                if ($rendezvous->getPatientId() === $this) {
                    $rendezvous->setPatientId(null);
                }
            }
    
            return $this;
        }
}
