<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Patient;

#[ORM\Entity]
class Dossiermedicale
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "dossiermedicales")]
    #[ORM\JoinColumn(name: 'id_patient', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Patient $id_patient;

    #[ORM\Column(type: "float")]
    private float $taille;

    #[ORM\Column(type: "float")]
    private float $poids;

    #[ORM\Column(type: "string")]
    private string $maladies;

    #[ORM\Column(type: "string")]
    private string $antecedents_cardiovasculaires_familiaux;

    #[ORM\Column(type: "string")]
    private string $asthmatique;

    #[ORM\Column(type: "string")]
    private string $suivi_dentaire_regulier;

    #[ORM\Column(type: "string")]
    private string $antecedents_chirurgicaux;

    #[ORM\Column(type: "string")]
    private string $allergies;

    #[ORM\Column(type: "string", length: 255)]
    private string $profession;

    #[ORM\Column(type: "string")]
    private string $niveau_de_stress;

    #[ORM\Column(type: "string")]
    private string $qualite_de_sommeil;

    #[ORM\Column(type: "string")]
    private string $activite_physique;

    #[ORM\Column(type: "string")]
    private string $situation_familiale;

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

    public function getTaille()
    {
        return $this->taille;
    }

    public function setTaille($value)
    {
        $this->taille = $value;
    }

    public function getPoids()
    {
        return $this->poids;
    }

    public function setPoids($value)
    {
        $this->poids = $value;
    }

    public function getMaladies()
    {
        return $this->maladies;
    }

    public function setMaladies($value)
    {
        $this->maladies = $value;
    }

    public function getAntecedents_cardiovasculaires_familiaux()
    {
        return $this->antecedents_cardiovasculaires_familiaux;
    }

    public function setAntecedents_cardiovasculaires_familiaux($value)
    {
        $this->antecedents_cardiovasculaires_familiaux = $value;
    }

    public function getAsthmatique()
    {
        return $this->asthmatique;
    }

    public function setAsthmatique($value)
    {
        $this->asthmatique = $value;
    }

    public function getSuivi_dentaire_regulier()
    {
        return $this->suivi_dentaire_regulier;
    }

    public function setSuivi_dentaire_regulier($value)
    {
        $this->suivi_dentaire_regulier = $value;
    }

    public function getAntecedents_chirurgicaux()
    {
        return $this->antecedents_chirurgicaux;
    }

    public function setAntecedents_chirurgicaux($value)
    {
        $this->antecedents_chirurgicaux = $value;
    }

    public function getAllergies()
    {
        return $this->allergies;
    }

    public function setAllergies($value)
    {
        $this->allergies = $value;
    }

    public function getProfession()
    {
        return $this->profession;
    }

    public function setProfession($value)
    {
        $this->profession = $value;
    }

    public function getNiveau_de_stress()
    {
        return $this->niveau_de_stress;
    }

    public function setNiveau_de_stress($value)
    {
        $this->niveau_de_stress = $value;
    }

    public function getQualite_de_sommeil()
    {
        return $this->qualite_de_sommeil;
    }

    public function setQualite_de_sommeil($value)
    {
        $this->qualite_de_sommeil = $value;
    }

    public function getActivite_physique()
    {
        return $this->activite_physique;
    }

    public function setActivite_physique($value)
    {
        $this->activite_physique = $value;
    }

    public function getSituation_familiale()
    {
        return $this->situation_familiale;
    }

    public function setSituation_familiale($value)
    {
        $this->situation_familiale = $value;
    }
}
