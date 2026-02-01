<?php

namespace App\Tests\Support;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TestUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $email;
    private string $password = '';
    /** @var Collection<int, mixed> */
    private Collection $favoris;
    /** @var Collection<int, mixed> */
    private Collection $locations;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->favoris = new ArrayCollection();
        $this->locations = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    /**
     * @return Collection<int, mixed>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addFavori(object $film): void
    {
        if (!$this->favoris->contains($film)) {
            $this->favoris->add($film);
        }
    }

    public function removeFavori(object $film): void
    {
        if ($this->favoris->contains($film)) {
            $this->favoris->removeElement($film);
        }
    }
}
