<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\NoBadWords;


use App\Entity\Medecin;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]  // This allows Doctrine to automatically generate the ID.
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Utilisateur $utilisateur;

        #[ORM\ManyToOne(targetEntity: Medecin::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'medecin_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Medecin $medecin;


    #[NoBadWords]
    #[Assert\NotBlank(message: "Sujet requis")]
    #[Assert\Length(min: 5, minMessage: "Sujet trop court")]
    #[ORM\Column(type: "string", length: 255)]
    private string $sujet;


    #[NoBadWords]
    #[Assert\NotBlank(message: "Description requise")]
    #[Assert\Length(min: 10, minMessage: "Description trop courte")]
    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateReclamation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    public function setUtilisateur($value)
    {
        $this->utilisateur = $value;
    }

    public function getMedecin()
    {
        return $this->medecin;
    }

    public function setMedecin($value)
    {
        $this->medecin= $value;
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

    public function getDateReclamation ()
    {
        return $this->dateReclamation;
    }

    public function setDateReclamation($value)
    {
        $this->dateReclamation = $value;
    }
}