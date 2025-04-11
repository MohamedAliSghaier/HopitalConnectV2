<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Pharmacien;

#[ORM\Entity]
class Medicament
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Pharmacien::class, inversedBy: "medicaments")]
    #[ORM\JoinColumn(name: 'pharmacien_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Pharmacien $pharmacien_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "integer")]
    private int $stock;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getPharmacien_id()
    {
        return $this->pharmacien_id;
    }

    public function setPharmacien_id($value)
    {
        $this->pharmacien_id = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($value)
    {
        $this->stock = $value;
    }
}
