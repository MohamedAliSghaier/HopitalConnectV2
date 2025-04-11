<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Patient;

#[ORM\Entity]
class Assurance
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_assurance;

        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "assurances")]
    #[ORM\JoinColumn(name: 'Id_PatientAs', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $Id_PatientAs;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string")]
    private string $type;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_debut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_fin;

    public function getId_assurance()
    {
        return $this->id_assurance;
    }

    public function setId_assurance($value)
    {
        $this->id_assurance = $value;
    }

    public function getId_PatientAs()
    {
        return $this->Id_PatientAs;
    }

    public function setId_PatientAs($value)
    {
        $this->Id_PatientAs = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getDate_debut()
    {
        return $this->date_debut;
    }

    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
    {
        return $this->date_fin;
    }

    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }
}
