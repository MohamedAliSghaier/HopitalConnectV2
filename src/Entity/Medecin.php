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
        #[ORM\OneToOne(targetEntity: Utilisateur::class, inversedBy: "medecin")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Utilisateur $utilisateur;

    #[ORM\Column(type: "string", length: 100)]
    private string $specialite;

    #[ORM\Column(type: "integer")]
    private int $num_rdv_max;

    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    public function setUtilisateur($value)
    {
        $this->utilisateur = $value;
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

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Avis::class)]
    private Collection $aviss;

        public function getAviss(): Collection
        {
            return $this->aviss;
        }
    
        public function addAvis(Avis $avis): self
        {
            if (!$this->aviss->contains($avis)) {
                $this->aviss[] = $avis;
                $avis->setMedecin_id($this);
            }
    
            return $this;
        }
    
        public function removeAvis(Avis $avis): self
        {
            if ($this->aviss->removeElement($avis)) {
                // set the owning side to null (unless already changed)
                if ($avis->getMedecin_id() === $this) {
                    $avis->setMedecin_id(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Ordonnance::class)]
    private Collection $ordonnances;

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Reclamation::class)]
    private Collection $reclamations;
}
