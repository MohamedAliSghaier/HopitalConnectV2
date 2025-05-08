<?php
// src/Entity/Dossiermedicale.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Patient;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\DossiermedicaleRepository")]
class Dossiermedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")] // Ensure the primary key is auto-generated
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class, inversedBy: "dossiermedicales")]
    #[ORM\JoinColumn(name: "id_patient", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Patient $id_patient = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\NotBlank(message: "La taille est obligatoire.")]
    #[Assert\Positive(message: "La taille doit être un nombre positif.")]
    #[Assert\Type(
        type: "numeric",
        message: "La taille doit être un nombre valide."
    )]
    private ?float $taille = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\NotBlank(message: "Le poids est obligatoire.")]
    #[Assert\Positive(message: "Le poids doit être un nombre positif.")]
    #[Assert\Type(
        type: "numeric",
        message: "Le poids doit être un nombre valide."
    )]
    private ?float $poids = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous avez des maladies.")]
    private ?string $maladies = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $maladies_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous avez des antécédents cardiovasculaires.")]
    private ?string $antecedents_cardiovasculaires_familiaux = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $antecedents_cardiovasculaires_familiaux_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous êtes asthmatique.")]
    private ?string $asthmatique = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $asthmatique_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous avez un suivi dentaire régulier.")]
    private ?string $suivi_dentaire_regulier = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $suivi_dentaire_regulier_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous avez des antécédents chirurgicaux.")]
    private ?string $antecedents_chirurgicaux = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $antecedents_chirurgicaux_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer si vous avez des allergies.")]
    private ?string $allergies = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $allergies_details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La profession est obligatoire.")]
    private ?string $profession = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le niveau de stress est obligatoire.")]
    private ?string $niveau_de_stress = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La qualité de sommeil est obligatoire.")]
    private ?string $qualite_de_sommeil = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'activité physique est obligatoire.")]
    private ?string $activite_physique = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La situation familiale est obligatoire.")]
    private ?string $situation_familiale = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPatient(): ?Patient
    {
        return $this->id_patient;
    }

    public function setIdPatient(?Patient $id_patient): self
    {
        $this->id_patient = $id_patient;
        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(?float $taille): self
    {
        $this->taille = $taille;
        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;
        return $this;
    }

    public function getMaladies(): ?string
    {
        return $this->maladies;
    }

    public function setMaladies(?string $maladies): self
    {
        $this->maladies = $maladies;
        return $this;
    }

    public function getMaladiesDetails(): ?string
    {
        return $this->maladies_details;
    }

    public function setMaladiesDetails(?string $maladies_details): self
    {
        $this->maladies_details = $maladies_details;
        return $this;
    }

    public function getAntecedentsCardiovasculairesFamiliaux(): ?string
    {
        return $this->antecedents_cardiovasculaires_familiaux;
    }

    public function setAntecedentsCardiovasculairesFamiliaux(?string $antecedents_cardiovasculaires_familiaux): self
    {
        $this->antecedents_cardiovasculaires_familiaux = $antecedents_cardiovasculaires_familiaux;
        return $this;
    }

    public function getAntecedentsCardiovasculairesFamiliauxDetails(): ?string
    {
        return $this->antecedents_cardiovasculaires_familiaux_details;
    }

    public function setAntecedentsCardiovasculairesFamiliauxDetails(?string $antecedents_cardiovasculaires_familiaux_details): self
    {
        $this->antecedents_cardiovasculaires_familiaux_details = $antecedents_cardiovasculaires_familiaux_details;
        return $this;
    }

    public function getAsthmatique(): ?string
    {
        return $this->asthmatique;
    }

    public function setAsthmatique(?string $asthmatique): self
    {
        $this->asthmatique = $asthmatique;
        return $this;
    }

    public function getAsthmatiqueDetails(): ?string
    {
        return $this->asthmatique_details;
    }

    public function setAsthmatiqueDetails(?string $asthmatique_details): self
    {
        $this->asthmatique_details = $asthmatique_details;
        return $this;
    }

    public function getSuiviDentaireRegulier(): ?string
    {
        return $this->suivi_dentaire_regulier;
    }

    public function setSuiviDentaireRegulier(?string $suivi_dentaire_regulier): self
    {
        $this->suivi_dentaire_regulier = $suivi_dentaire_regulier;
        return $this;
    }

    public function getSuiviDentaireRegulierDetails(): ?string
    {
        return $this->suivi_dentaire_regulier_details;
    }

    public function setSuiviDentaireRegulierDetails(?string $suivi_dentaire_regulier_details): self
    {
        $this->suivi_dentaire_regulier_details = $suivi_dentaire_regulier_details;
        return $this;
    }

    public function getAntecedentsChirurgicaux(): ?string
    {
        return $this->antecedents_chirurgicaux;
    }

    public function setAntecedentsChirurgicaux(?string $antecedents_chirurgicaux): self
    {
        $this->antecedents_chirurgicaux = $antecedents_chirurgicaux;
        return $this;
    }

    public function getAntecedentsChirurgicauxDetails(): ?string
    {
        return $this->antecedents_chirurgicaux_details;
    }

    public function setAntecedentsChirurgicauxDetails(?string $antecedents_chirurgicaux_details): self
    {
        $this->antecedents_chirurgicaux_details = $antecedents_chirurgicaux_details;
        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): self
    {
        $this->allergies = $allergies;
        return $this;
    }

    public function getAllergiesDetails(): ?string
    {
        return $this->allergies_details;
    }

    public function setAllergiesDetails(?string $allergies_details): self
    {
        $this->allergies_details = $allergies_details;
        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }

    public function getNiveauDeStress(): ?string
    {
        return $this->niveau_de_stress;
    }

    public function setNiveauDeStress(?string $niveau_de_stress): self
    {
        $this->niveau_de_stress = $niveau_de_stress;
        return $this;
    }

    public function getQualiteDeSommeil(): ?string
    {
        return $this->qualite_de_sommeil;
    }

    public function setQualiteDeSommeil(?string $qualite_de_sommeil): self
    {
        $this->qualite_de_sommeil = $qualite_de_sommeil;
        return $this;
    }

    public function getActivitePhysique(): ?string
    {
        return $this->activite_physique;
    }

    public function setActivitePhysique(?string $activite_physique): self
    {
        $this->activite_physique = $activite_physique;
        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situation_familiale;
    }

    public function setSituationFamiliale(?string $situation_familiale): self
    {
        $this->situation_familiale = $situation_familiale;
        return $this;
    }
}