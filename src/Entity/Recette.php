<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $texte = null;

    #[ORM\Column]
    private ?int $duree_totale = null;

    #[ORM\Column]
    private ?int $nombre_personnes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'recettes')]
    #[ORM\JoinTable(name: 'recette_ingredient')]
    private $ingredients;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur;


    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'recette', orphanRemoval: true, cascade: ['persist'])]
    private Collection $commentaires;
    /**
     * @var Collection<int, RecetteIngredient>
     */
    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: RecetteIngredient::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $recetteIngredients;


    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->recetteIngredients = new ArrayCollection();  // Initialisation de la collection
        $this->commentaires = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * @return Collection<int, RecetteIngredient>
     */
    public function getRecetteIngredients(): Collection
    {
        return $this->recetteIngredients;
    }

    public function getDureeTotale(): ?int
    {
        return $this->duree_totale;
    }

    public function setDureeTotale(int $duree_totale): static
    {
        $this->duree_totale = $duree_totale;

        return $this;
    }

    public function getNombrePersonnes(): ?int
    {
        return $this->nombre_personnes;
    }

    public function setNombrePersonnes(int $nombre_personnes): static
    {
        $this->nombre_personnes = $nombre_personnes;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        $ingredients = new ArrayCollection();
        foreach ($this->recetteIngredients as $recetteIngredient) {
            $ingredients->add($recetteIngredient->getIngredient());
        }
        return $ingredients;
    }
    public function addRecetteIngredient(RecetteIngredient $recetteIngredient): static
    {
        // Vérifie si l'association existe déjà
        foreach ($this->recetteIngredients as $existingRecetteIngredient) {
            if ($existingRecetteIngredient->getIngredient() === $recetteIngredient->getIngredient()) {
                // Si l'ingrédient est déjà associé, ne rien faire
                return $this;
            }
        }

        // Ajoute l'association si elle n'existe pas
        $this->recetteIngredients->add($recetteIngredient);
        $recetteIngredient->setRecette($this);

        return $this;
    }

    public function removeRecetteIngredient(RecetteIngredient $recetteIngredient): static
    {
        if ($this->recetteIngredients->removeElement($recetteIngredient)) {
            // set the owning side to null (unless already changed)
            if ($recetteIngredient->getRecette() === $this) {
                $recetteIngredient->setRecette(null);
            }
        }

        return $this;
    }
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function hasIngredient(string $ingredientNom): bool
    {
        foreach ($this->ingredients as $ingredient) {
            if ($ingredient->getNom() === $ingredientNom) {
                return true;
            }
        }

        return false;
    }
}
