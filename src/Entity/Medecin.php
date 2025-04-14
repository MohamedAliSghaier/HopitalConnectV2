<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Utilisateur;
use Doctrine\Common\Collections\Collection;
use App\Entity\Reclamation;

#[ORM\Entity]
class Medecin
{

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "medecins")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Utilisateur $id;

    #[ORM\Column(type: "string", length: 100)]
    private string $specialite;

    #[ORM\Column(type: "integer")]
    private int $num_rdv_max;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getSpecialite()
    {
        return $this->specialite;
    }

    public function setSpecialite($value)
    {
        $this->specialite = $value;
    }

    public function getNum_rdv_max()
    {
        return $this->num_rdv_max;
    }

    public function setNum_rdv_max($value)
    {
        $this->num_rdv_max = $value;
    }

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;

        public function getOrdonnances(): Collection
        {
            return $this->ordonnances;
        }
    
        public function addOrdonnance(Ordonnance $ordonnance): self
        {
            if (!$this->ordonnances->contains($ordonnance)) {
                $this->ordonnances[] = $ordonnance;
                $ordonnance->setMedecin_id($this);
            }
    
            return $this;
        }
    
        public function removeOrdonnance(Ordonnance $ordonnance): self
        {
            if ($this->ordonnances->removeElement($ordonnance)) {
                // set the owning side to null (unless already changed)
                if ($ordonnance->getMedecin_id() === $this) {
                    $ordonnance->setMedecin_id(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Avis::class)]
    private Collection $aviss;

    #[ORM\OneToMany(mappedBy: "medecinId", targetEntity: Rendezvous::class)]
    private Collection $rendezvouss;

        public function getRendezvouss(): Collection
        {
            return $this->rendezvouss;
        }
    
        public function addRendezvous(Rendezvous $rendezvous): self
        {
            if (!$this->rendezvouss->contains($rendezvous)) {
                $this->rendezvouss[] = $rendezvous;
                $rendezvous->setMedecinId($this);
            }
    
            return $this;
        }
    
        public function removeRendezvous(Rendezvous $rendezvous): self
        {
            if ($this->rendezvouss->removeElement($rendezvous)) {
                // set the owning side to null (unless already changed)
                if ($rendezvous->getMedecinId() === $this) {
                    $rendezvous->setMedecinId(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Reclamation::class)]
    private Collection $reclamations;
}
