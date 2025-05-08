<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Medecin;
use App\Entity\Patient;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\NoBadWords;



#[ORM\Entity]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  // This allows Doctrine to automatically generate the ID.
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $patient;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin;
    
    #[NoBadWords]
    #[Assert\NotBlank(message: "Le commentaire est requis.")]
    #[Assert\Length(min: 5, minMessage: "Le commentair au moins {{ limit }} caractères.")]
    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[Assert\NotBlank(message: "La note est requise.")]
    #[Assert\Range(
        notInRangeMessage: "La note doit être entre {{ min }} et {{ max }}.",
        min: 1,
        max: 5
    )]
    #[ORM\Column(type: "integer")]
    private int $note;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_avis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $value): self
    {
        $this->patient= $value;
        return $this;
    }

    public function getMedecin(): Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(Medecin $value): self
    {
        $this->medecin = $value;
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
