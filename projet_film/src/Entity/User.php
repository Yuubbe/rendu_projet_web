<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements \Symfony\Component\Security\Core\User\UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Location::class)]
    private Collection $locations;

    #[ORM\ManyToMany(targetEntity: Film::class, inversedBy: 'utilisateursFavoris')]
    #[ORM\JoinTable(name: 'user_film')]
    private Collection $favoris;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials() {}

    /** @return Collection<int, Location> */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    /** @return Collection<int, Film> */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Film $film): static
    {
        if (!$this->favoris->contains($film)) {
            $this->favoris->add($film);
        }

        return $this;
    }

    public function removeFavori(Film $film): static
    {
        $this->favoris->removeElement($film);

        return $this;
    }
}
