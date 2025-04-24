<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Pharmacien
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: "pharmacien", targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->utilisateur?->getId();
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        // Synchroniser le côté inverse de la relation
        if ($utilisateur !== null && $utilisateur->getPharmacien() !== $this) {
            $utilisateur->setPharmacien($this);
        }

        return $this;
    }
}
