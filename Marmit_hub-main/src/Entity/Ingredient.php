<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ingredients')]
    private ?User $utilisateur = null;

    /**
     * @var Collection<int, RecetteIngredient>
     */
    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: RecetteIngredient::class, orphanRemoval: true)]
    private Collection $recetteIngredients;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->recetteIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom; // Représenter l'ingrédient par son nom
    }

    /**
     * @return Collection<int, RecetteIngredient>
     */
    public function getRecetteIngredients(): Collection
    {
        return $this->recetteIngredients;
    }

    public function addRecetteIngredient(RecetteIngredient $recetteIngredient): self
    {
        if (!$this->recetteIngredients->contains($recetteIngredient)) {
            $this->recetteIngredients->add($recetteIngredient);
            $recetteIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeRecetteIngredient(RecetteIngredient $recetteIngredient): self
    {
        if ($this->recetteIngredients->removeElement($recetteIngredient)) {
            if ($recetteIngredient->getIngredient() === $this) {
                $recetteIngredient->setIngredient(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * Retourne toutes les recettes associées à cet ingrédient.
     *
     * @return array
     */
    public function getRecettes(): array
    {
        $recettes = [];
        foreach ($this->recetteIngredients as $recetteIngredient) {
            if ($recetteIngredient->getRecette()) {
                $recettes[] = $recetteIngredient->getRecette();
            }
        }
        return $recettes;
    }
}
