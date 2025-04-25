<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Pharmacien;

#[ORM\Entity]
class Medicament
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pharmacien::class, inversedBy: "medicaments")]
    #[ORM\JoinColumn(name: "pharmacien_id", referencedColumnName: "id", onDelete: "SET NULL")]
    private ?Pharmacien $pharmacien = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le nom du médicament est obligatoire.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le stock est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le stock doit être un nombre positif ou zéro.")]
    private int $stock;    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function assignPharmacienToMedicament(int $medicamentId, int $pharmacienId, EntityManagerInterface $entityManager): void
    {
        $medicament = $entityManager->getRepository(Medicament::class)->find($medicamentId);

        if (!$medicament) {
            throw new \InvalidArgumentException("Medicament with ID $medicamentId not found.");
        }

        $medicament->setPharmacienById($pharmacienId, $entityManager);

        $entityManager->persist($medicament);
        $entityManager->flush();
    }

    public function getPharmacien(): ?Pharmacien
    {
        return $this->pharmacien;
    }

    public function setPharmacien(?Pharmacien $pharmacien): self
    {
        $this->pharmacien = $pharmacien;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}