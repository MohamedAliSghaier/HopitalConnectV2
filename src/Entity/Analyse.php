<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Rendezvous;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
class Analyse
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_patient', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Rendezvous $id_patient;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_medecin', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Rendezvous $id_medecin;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_rendezvous', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Rendezvous $RendezVous = null;

  


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_patient()
    {
        return $this->id_patient;
    }

    public function setId_patient($value)
    {
        $this->id_patient = $value;
    }

    public function getId_medecin()
    {
        return $this->id_medecin;
    }

    public function setId_medecin($value)
    {
        $this->id_medecin = $value;
    }

    public function getRendezVous()
    {
        return $this->RendezVous;
    }

    public function setRendezVous($value)
    {
        $this->RendezVous = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getIdPatient(): ?Rendezvous
    {
        return $this->id_patient;
    }

    public function setIdPatient(?Rendezvous $id_patient): static
    {
        $this->id_patient = $id_patient;

        return $this;
    }

    public function getIdMedecin(): ?Rendezvous
    {
        return $this->id_medecin;
    }

    public function setIdMedecin(?Rendezvous $id_medecin): static
    {
        $this->id_medecin = $id_medecin;

        return $this;
    }

    
}
