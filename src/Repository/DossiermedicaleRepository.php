<?php

namespace App\Repository;

use App\Entity\Dossiermedicale; // Corrected casing
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DossiermedicaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossiermedicale::class); // Corrected casing
    }
}
