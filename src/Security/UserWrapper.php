<?php

// src/Security/UserWrapper.php
namespace App\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Utilisateur;

class UserWrapper implements UserInterface, PasswordAuthenticatedUserInterface
{
    private Utilisateur $utilisateur;

    public function __construct(Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    public function getRoles(): array
    {
        return [$this->utilisateur->getRole()];
    }

    public function getPassword(): string
    {
        return $this->utilisateur->getMot_de_passe();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // Nettoyage si nécessaire
    }

    public function getUsername(): string
    {
        return $this->utilisateur->getEmail();
    }

    public function getUserIdentifier(): string
    {
        return $this->utilisateur->getEmail();
    }

    public function getWrappedUser(): Utilisateur
    {
        return $this->utilisateur;
    }
}