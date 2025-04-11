<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Analyse;

#[ORM\Entity]
class Rendezvous
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "integer")]
    private int $type_consultation_id;

    #[ORM\Column(type: "integer")]
    private int $PatientId;

    #[ORM\Column(type: "integer")]
    private int $medecinId;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $start_time;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $end_time;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getType_consultation_id()
    {
        return $this->type_consultation_id;
    }

    public function setType_consultation_id($value)
    {
        $this->type_consultation_id = $value;
    }

    public function getPatientId()
    {
        return $this->PatientId;
    }

    public function setPatientId($value)
    {
        $this->PatientId = $value;
    }

    public function getMedecinId()
    {
        return $this->medecinId;
    }

    public function setMedecinId($value)
    {
        $this->medecinId = $value;
    }

    public function getStart_time()
    {
        return $this->start_time;
    }

    public function setStart_time($value)
    {
        $this->start_time = $value;
    }

    public function getEnd_time()
    {
        return $this->end_time;
    }

    public function setEnd_time($value)
    {
        $this->end_time = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Analyse::class)]
    private Collection $analyses;

        public function getAnalyses(): Collection
        {
            return $this->analyses;
        }
    
        public function addAnalyse(Analyse $analyse): self
        {
            if (!$this->analyses->contains($analyse)) {
                $this->analyses[] = $analyse;
                $analyse->setId_patient($this);
            }
    
            return $this;
        }
    
        public function removeAnalyse(Analyse $analyse): self
        {
            if ($this->analyses->removeElement($analyse)) {
                // set the owning side to null (unless already changed)
                if ($analyse->getId_patient() === $this) {
                    $analyse->setId_patient(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_medecin", targetEntity: Analyse::class)]
    private Collection $analyses;

        public function getAnalyses(): Collection
        {
            return $this->analyses;
        }
    
        public function addAnalyse(Analyse $analyse): self
        {
            if (!$this->analyses->contains($analyse)) {
                $this->analyses[] = $analyse;
                $analyse->setId_medecin($this);
            }
    
            return $this;
        }
    
        public function removeAnalyse(Analyse $analyse): self
        {
            if ($this->analyses->removeElement($analyse)) {
                // set the owning side to null (unless already changed)
                if ($analyse->getId_medecin() === $this) {
                    $analyse->setId_medecin(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_rendezvous", targetEntity: Analyse::class)]
    private Collection $analyses;

        public function getAnalyses(): Collection
        {
            return $this->analyses;
        }
    
        public function addAnalyse(Analyse $analyse): self
        {
            if (!$this->analyses->contains($analyse)) {
                $this->analyses[] = $analyse;
                $analyse->setId_rendezvous($this);
            }
    
            return $this;
        }
    
        public function removeAnalyse(Analyse $analyse): self
        {
            if ($this->analyses->removeElement($analyse)) {
                // set the owning side to null (unless already changed)
                if ($analyse->getId_rendezvous() === $this) {
                    $analyse->setId_rendezvous(null);
                }
            }
    
            return $this;
        }
}
