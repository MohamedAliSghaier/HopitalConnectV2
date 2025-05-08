<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AssuranceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Patient;

#[ORM\Entity(repositoryClass: AssuranceRepository::class)]
class Assurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "assurances")]
    #[ORM\JoinColumn(name: "patient_id", referencedColumnName: "id", onDelete: "CASCADE")]
    #[Assert\NotNull(message: "Patient is required")]
    private ?Patient $patient = null;

    #[ORM\Column(name: "date_debut", type: "date")]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\LessThanOrEqual(
        propertyPath: "dateFin",
        message: "La date de début doit être antérieure ou égale à la date de fin."
    )]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(name: "date_fin", type: "date")]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateDebut",
        message: "La date de fin doit être postérieure ou égale à la date de début."
    )]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(name: "nom_assureur", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Insurer name cannot be blank")]
    private string $NomAssureur;

    #[ORM\Column(name: "type_assureur", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Insurer type cannot be blank")]
    private string $TypeAssureur;

    #[ORM\Column(name: "numero_police", type: "integer")]
    #[Assert\NotBlank(message: "Le numéro de police est obligatoire.")]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: "Le numéro de police doit contenir exactement {{ limit }} chiffres."
    )]
    #[Assert\Positive(message: "Le numéro de police doit être un nombre positif.")]
    private int $NumeroPolice;

    #[ORM\Column(name: "nom_titulaire", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Policyholder name cannot be blank")]
    private string $NomTitulaire;

    #[ORM\Column(name: "type_couverture", type: "string", length: 255)]
    #[Assert\NotBlank(message: "Coverage type cannot be blank")]
    private string $TypeCouverture;

    public function getId(): ?int
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

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): \DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getNomAssureur(): string
    {
        return $this->NomAssureur;
    }

    public function setNomAssureur(string $NomAssureur): self
    {
        $this->NomAssureur = $NomAssureur;
        return $this;
    }

    public function getTypeAssureur(): string
    {
        return $this->TypeAssureur;
    }

    public function setTypeAssureur(string $TypeAssureur): self
    {
        $this->TypeAssureur = $TypeAssureur;
        return $this;
    }

    public function getNumeroPolice(): int
    {
        return $this->NumeroPolice;
    }

    public function setNumeroPolice(int $NumeroPolice): self
    {
        $this->NumeroPolice = $NumeroPolice;
        return $this;
    }

    public function getNomTitulaire(): string
    {
        return $this->NomTitulaire;
    }

    public function setNomTitulaire(string $NomTitulaire): self
    {
        $this->NomTitulaire = $NomTitulaire;
        return $this;
    }

    public function getTypeCouverture(): string
    {
        return $this->TypeCouverture;
    }

    public function setTypeCouverture(string $TypeCouverture): self
    {
        $this->TypeCouverture = $TypeCouverture;
        return $this;
    }
}
