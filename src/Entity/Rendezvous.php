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
    #[ORM\GeneratedValue]
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

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $start_time;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $end_time;
    #[ORM\Column(type: "integer")]
    private int $type_consultation_id;

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

    public function getPatient(): Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getMedecin(): Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(Medecin $medecin): self
{
    $this->medecin = $medecin;
    return $this;
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

    //#[ORM\OneToMany(mappedBy: "id_medecin", targetEntity: Analyse::class)]
    //private Collection $analyses;

    //#[ORM\OneToMany(mappedBy: "id_rendezvous", targetEntity: Analyse::class)]
    //private Collection $analyses;
}
