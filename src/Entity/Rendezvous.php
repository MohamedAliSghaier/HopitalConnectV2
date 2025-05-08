<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
class Rendezvous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Vous devez remplir ce champ.')]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être une date valide")]
    #[Assert\GreaterThanOrEqual("today", message: "La date ne peut pas être dans le passé")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: 'Vous devez remplir ce champ.')]
    #[Assert\Choice([1, 2], message: "Le type de consultation doit être 1 (Consultation) ou 2 (Téléconsultation)")]
    private int $type_consultation_id;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'PatientId', referencedColumnName: 'id', onDelete: 'CASCADE' )]
    private ?Patient $PatientId = null;  // Assurer que la valeur par défaut soit null


    #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'medecinId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un médecin.')]

    private Medecin $medecinId;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure de début est obligatoire")]
    #[Assert\Type("\DateTimeInterface", message: "L'heure doit être une heure valide")]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Analyse::class)]
    private Collection $analyses;
    

    public function __construct()
    {
        $this->analyses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTypeConsultationId(): int
    {
        return $this->type_consultation_id;
    }

    public function setTypeConsultationId(int $type_consultation_id): self
    {
        $this->type_consultation_id = $type_consultation_id;
        return $this;
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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;
        return $this;
    }

    public function getAnalyses(): Collection
    {
        return $this->analyses;
    }

    public function addAnalysis(Analyse $analysis): static
    {
        if (!$this->analyses->contains($analysis)) {
            $this->analyses->add($analysis);
            $analysis->setIdPatient($this);
        }

        return $this;
    }

    public function removeAnalysis(Analyse $analysis): static
    {
        if ($this->analyses->removeElement($analysis)) {
            if ($analysis->getIdPatient() === $this) {
                $analysis->setIdPatient(null);
            }
        }

        return $this;
    }
    
    
}