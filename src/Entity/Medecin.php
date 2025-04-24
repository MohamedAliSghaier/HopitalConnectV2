<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function __construct()
    {
        $this->ordonnances = new ArrayCollection();
        $this->aviss = new ArrayCollection();
        $this->rendezvouss = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
    }

    public function getId(): Utilisateur
    {
        return $this->id;
    }

    public function setId(Utilisateur $value): self
    {
        $this->id = $value;
        return $this;
    }

    public function getSpecialite(): string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $value): self
    {
        $this->specialite = $value;
        return $this;
    }

    public function getNum_rdv_max(): int
    {
        return $this->num_rdv_max;
    }

    public function setNum_rdv_max(int $value): self
    {
        $this->num_rdv_max = $value;
        return $this;
    }
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->id;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->id = $utilisateur;

        // Synchroniser le côté inverse de la relation
        if ($utilisateur !== null && $utilisateur->getMedecin() !== $this) {
            $utilisateur->setMedecin($this);
        }

        return $this;
    }

    // ----- À partir d'ici, inchangé comme demandé -----

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
            if ($rendezvous->getMedecinId() === $this) {
                $rendezvous->setMedecinId(null);
            }
        }

        return $this;
    }

    #[ORM\OneToMany(mappedBy: "medecin_id", targetEntity: Reclamation::class)]
    private Collection $reclamations;
}
