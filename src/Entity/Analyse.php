<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Rendezvous;
use App\Entity\Patient;
use App\Entity\Medecin;

#[ORM\Entity]
class Analyse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;


    #[ORM\ManyToOne(targetEntity: Patient::class)]
#[ORM\JoinColumn(name: 'id_patient', referencedColumnName: 'id', onDelete: 'CASCADE')]
private ?Patient $patient = null;


    #[ORM\ManyToOne(targetEntity: Medecin::class)]
    #[ORM\JoinColumn(name: 'id_medecin', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Medecin $medecin = null;

    #[ORM\ManyToOne(targetEntity: Rendezvous::class)]
    #[ORM\JoinColumn(name: 'id_rendezvous', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Rendezvous $rendezvous = null;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        $this->medecin = $medecin;
        return $this;
    }

    public function getRendezvous(): ?Rendezvous
    {
        return $this->rendezvous;
    }

    public function setRendezvous(?Rendezvous $rendezvous): self
    {
        $this->rendezvous = $rendezvous;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
