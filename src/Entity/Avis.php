<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Medecin;
use App\Entity\Patient;

#[ORM\Entity]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  // This allows Doctrine to automatically generate the ID.
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $patient_id;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin_id;

    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: "integer")]
    private int $note;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_avis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient_id(): Patient
    {
        return $this->patient_id;
    }

    public function setPatient_id(Patient $value): self
    {
        $this->patient_id = $value;
        return $this;
    }

    public function getMedecin_id(): Medecin
    {
        return $this->medecin_id;
    }

    public function setMedecin_id(Medecin $value): self
    {
        $this->medecin_id = $value;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $value): self
    {
        $this->commentaire = $value;
        return $this;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $value): self
    {
        $this->note = $value;
        return $this;
    }

    public function getDate_avis(): \DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDate_avis(\DateTimeInterface $value): self
    {
        $this->date_avis = $value;
        return $this;
    }
}
