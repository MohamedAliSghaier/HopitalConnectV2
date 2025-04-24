<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Patient;

#[ORM\Entity]
class Avis
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "aviss")]
    #[ORM\JoinColumn(name: 'patient_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $patient_id;

    
    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: "integer")]
    private int $note;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_avis;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getPatient_id()
    {
        return $this->patient_id;
    }

    public function setPatient_id($value)
    {
        $this->patient_id = $value;
    }

    public function getMedecin_id()
    {
        return $this->medecin_id;
    }

    public function setMedecin_id($value)
    {
        $this->medecin_id = $value;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($value)
    {
        $this->commentaire = $value;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($value)
    {
        $this->note = $value;
    }

    public function getDate_avis()
    {
        return $this->date_avis;
    }

    public function setDate_avis($value)
    {
        $this->date_avis = $value;
    }
}
