<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Patient;

#[ORM\Entity]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    private ?Medecin $medecin_id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    private ?Patient $patient_id = null;

    #[ORM\Column(type: "text")]
    private string $medicaments;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $date_prescription = null;

    #[ORM\Column(type: "text")]
    private string $instructions;

    #[ORM\Column(type: "string", length: 50)]
    private string $statut;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMedecinId(): ?Medecin
    {
        return $this->medecin_id;
    }

    public function setMedecinId(?Medecin $medecin): void
    {
        $this->medecin_id = $medecin;
    }

    public function getPatientId(): ?Patient
    {
        return $this->patient_id;
    }

    public function setPatientId(?Patient $patient): void
    {
        $this->patient_id = $patient;
    }

    public function getMedicaments(): string
    {
        return $this->medicaments;
    }

    public function setMedicaments(string $medicaments): void
    {
        $this->medicaments = $medicaments;
    }

    public function getDatePrescription(): ?\DateTimeInterface
    {
        return $this->date_prescription;
    }

    public function setDatePrescription(?\DateTimeInterface $date): void
    {
        $this->date_prescription = $date;
    }

    public function getInstructions(): string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): void
    {
        $this->instructions = $instructions;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }
}
