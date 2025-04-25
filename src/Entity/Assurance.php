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

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Name cannot be blank")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Name must be at least {{ limit }} characters",
        maxMessage: "Name cannot exceed {{ limit }} characters"
    )]
    private string $nom;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Type cannot be blank")]
    private string $type;

    #[ORM\Column(name: "date_debut", type: "date")]
    #[Assert\NotNull(message: "Start date is required")]
    #[Assert\LessThanOrEqual(
        propertyPath: "dateFin",
        message: "Start date must be before or equal to end date"
    )]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(name: "date_fin", type: "date")]
    #[Assert\NotNull(message: "End date is required")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateDebut",
        message: "End date must be after or equal to start date"
    )]
    private \DateTimeInterface $dateFin;

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

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
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

  
}
