<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Medecin;

#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Utilisateur $utilisateur_id;

        #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $sujet;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_reclamation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getUtilisateur_id()
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateur_id($value)
    {
        $this->utilisateur_id = $value;
    }

    public function getMedecin_id()
    {
        return $this->medecin_id;
    }

    public function setMedecin_id($value)
    {
        $this->medecin_id = $value;
    }

    public function getSujet()
    {
        return $this->sujet;
    }

    public function setSujet($value)
    {
        $this->sujet = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDate_reclamation()
    {
        return $this->date_reclamation;
    }

    public function setDate_reclamation($value)
    {
        $this->date_reclamation = $value;
    }
}
