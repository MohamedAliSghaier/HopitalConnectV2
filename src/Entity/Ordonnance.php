<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Patient;

#[ORM\Entity]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    //#[Assert\NotNull(message: "Le médecin est obligatoire.")]
    private ?Medecin $medecin = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[Assert\NotNull(message: "Le patient est obligatoire.")]
    private ?Patient $patient = null;

    #[ORM\Column(type: "json")]
    #[Assert\NotBlank(message: "Les médicaments sont obligatoires.")]
    private array $medicaments = [];

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\NotNull(message: "La date de prescription est obligatoire.")]
    private ?\DateTimeInterface $date_prescription = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Les instructions sont obligatoires.")]
    #[Assert\Length(min: 5, minMessage: "Les instructions doivent contenir au moins {{ limit }} caractères.")]
    private string $instructions;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Choice(choices: ["En cours", "Terminée", "Annulée"], message: "Le statut doit être 'En cours', 'Terminée' ou 'Annulée'.")]
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

    public function getMedicaments(): array
    {
        return $this->medicaments;
    }

    public function setMedicaments(array $medicaments): void
    {
        $this->medicaments = $medicaments;
    }

    public function addMedicament(?string $nom, ?int $quantite): void
    {
        if ($nom !== null && $quantite !== null) {
            $this->medicaments[] = sprintf('%s:%d', $nom, $quantite);
        }
    }

    public function removeMedicament(string $nom): void
    {
        $this->medicaments = array_filter($this->medicaments, function ($medicament) use ($nom) {
            return $medicament['nom'] !== $nom;
        });
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

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): void
    {
        $this->medecin = $medecin;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): void
    {
        $this->patient = $patient;
    }
}
