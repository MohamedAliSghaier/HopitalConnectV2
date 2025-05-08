<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Medecin;
use Doctrine\Common\Collections\Collection;
use App\Entity\Analyse;

#[ORM\Entity]
class Rendezvous
{

    #[ORM\Id]
    #[ORM\GeneratedValue]  // This allows Doctrine to automatically generate the ID.
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    
        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'PatientId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $patient;

        #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "rendezvouss")]
    #[ORM\JoinColumn(name: 'medecinId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin;

    #[ORM\Column(name: "start_time", type: "datetime")]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $end_time;
    #[ORM\Column(name: "type_consultation_id", type: "integer")]
     private int $typeConsultationId;

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
    public function getTypeConsultationId(): int
    {
        return $this->typeConsultationId;
    }
    
    public function setTypeConsultationId(int $typeConsultationId): self
    {
        $this->typeConsultationId = $typeConsultationId;
        return $this;
    }

   public function getPatient(): ?Patient
{
    return $this->patient;  // lowercase 'p'
}

public function setPatient(?Patient $patient): self
{
    $this->patient = $patient;  // lowercase 'p'
    return $this;
}

    public function getMedecin()
    {
        return $this->medecin;
    }

    public function setMedecin($value)
    {
        $this->medecin = $value;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }
    
    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
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

    //#[ORM\OneToMany(mappedBy: "id_medecin", targetEntity: Analyse::class)]
    //private Collection $analyses;

    //#[ORM\OneToMany(mappedBy: "id_rendezvous", targetEntity: Analyse::class)]
    //private Collection $analyses;
}
