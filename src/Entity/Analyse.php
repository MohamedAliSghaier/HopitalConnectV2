<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Rendezvous;

#[ORM\Entity]
class Analyse
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_patient', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Rendezvous $id_patient;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_medecin', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Rendezvous $id_medecin;

        #[ORM\ManyToOne(targetEntity: Rendezvous::class, inversedBy: "analyses")]
    #[ORM\JoinColumn(name: 'id_rendezvous', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Rendezvous $id_rendezvous;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

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

    public function getId_rendezvous()
    {
        return $this->id_rendezvous;
    }

    public function setId_rendezvous($value)
    {
        $this->id_rendezvous = $value;
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
}
