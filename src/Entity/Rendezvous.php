<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Medecin;
use Doctrine\Common\Collections\Collection;
use App\Entity\Analyse;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
class Rendezvous
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "integer")]
    private int $type_consultation_id;

        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'PatientId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $PatientId;

        #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'medecinId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecinId;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getType_consultation_id()
    {
        return $this->type_consultation_id;
    }

    public function setType_consultation_id($value)
    {
        $this->type_consultation_id = $value;
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

    public function getStart_time()
    {
        return $this->start_time;
    }

    public function setStart_time($value)
    {
        $this->start_time = $value;
    }

    public function getEnd_time()
    {
        return $this->end_time;
    }

    public function setEnd_time($value)
    {
        $this->end_time = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_patient", targetEntity: Analyse::class)]
    private Collection $analyses;

    public function __construct()
    {
        $this->analyses = new ArrayCollection();
    }

    public function getTypeConsultationId(): ?int
    {
        return $this->type_consultation_id;
    }

    public function setTypeConsultationId(int $type_consultation_id): static
    {
        $this->type_consultation_id = $type_consultation_id;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * @return Collection<int, Analyse>
     */
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
            // set the owning side to null (unless already changed)
            if ($analysis->getIdPatient() === $this) {
                $analysis->setIdPatient(null);
            }
        }

        return $this;
    }
}
