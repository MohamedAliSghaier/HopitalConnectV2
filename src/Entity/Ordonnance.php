<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Patient;

#[ORM\Entity]
class Ordonnance
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin_id;

        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "ordonnances")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $patient_id;

    #[ORM\Column(type: "text")]
    private string $medicaments;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_prescription;

    #[ORM\Column(type: "text")]
    private string $instructions;

    #[ORM\Column(type: "string", length: 50)]
    private string $statut;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getMedecin_id()
    {
        return $this->medecin_id;
    }

    public function setMedecin_id($value)
    {
        $this->medecin_id = $value;
    }

    public function getPatient_id()
    {
        return $this->patient_id;
    }

    public function setPatient_id($value)
    {
        $this->patient_id = $value;
    }

    public function getMedicaments()
    {
        return $this->medicaments;
    }

    public function setMedicaments($value)
    {
        $this->medicaments = $value;
    }

    public function getDate_prescription()
    {
        return $this->date_prescription;
    }

    public function setDate_prescription($value)
    {
        $this->date_prescription = $value;
    }

    public function getInstructions()
    {
        return $this->instructions;
    }

    public function setInstructions($value)
    {
        $this->instructions = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }
}
