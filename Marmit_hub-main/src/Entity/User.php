<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{


    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Recette::class)]
    private Collection $recettes;

    #[ORM\OneToMany(mappedBy: "utilisateur", targetEntity: Ingredient::class)]
    private Collection $ingredients;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\OneToMany(targetEntity: Ingredient::class, mappedBy: 'uilisateur_id')]
    private Collection $user_id_ingredients;

    /**
     * @var Collection<int, Recette>
     */
    #[ORM\OneToMany(targetEntity: Recette::class, mappedBy: 'utilisateur_id', orphanRemoval: true)]
    private Collection $user_recette;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;


    public function __construct()
    {
        $this->recettes = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->user_id_ingredients = new ArrayCollection();
        $this->user_recette = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getUserIdIngredients(): Collection
    {
        return $this->user_id_ingredients;
    }

    public function addUserIdIngredient(Ingredient $userIdIngredient): static
    {
        if (!$this->user_id_ingredients->contains($userIdIngredient)) {
            $this->user_id_ingredients->add($userIdIngredient);
            $userIdIngredient->setUtilisateur($this);
        }

        return $this;
    }

    public function removeUserIdIngredient(Ingredient $userIdIngredient): static
    {
        if ($this->user_id_ingredients->removeElement($userIdIngredient)) {
            // set the owning side to null (unless already changed)
            if ($userIdIngredient->getUtilisateur() === $this) {
                $userIdIngredient->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getUserRecette(): Collection
    {
        return $this->user_recette;
    }

    public function addUserRecette(Recette $userRecette): static
    {
        if (!$this->user_recette->contains($userRecette)) {
            $this->user_recette->add($userRecette);
            $userRecette->setUtilisateur($this);
        }

        return $this;
    }

    public function removeUserRecette(Recette $userRecette): static
    {
        if ($this->user_recette->removeElement($userRecette)) {
            // set the owning side to null (unless already changed)
            if ($userRecette->getUtilisateur() === $this) {
                $userRecette->setUtilisateur(null);
            }
        }

        return $this;
    }
}
