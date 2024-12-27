<?php
// src/Controller/RecipeController.php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController

{
    #[Route('/search', name: 'app_recipe_search', methods: ['GET', 'POST'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredients = $entityManager->getRepository(Ingredient::class)->findAll();

        $form = $this->createFormBuilder()
            ->add('ingredients', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($ingredient) => $ingredient->getNom(), $ingredients),
                    array_map(fn($ingredient) => $ingredient->getNom(), $ingredients)
                ),
                'expanded' => true,
                'multiple' => true,
                'label' => 'Choisissez des ingrédients',
            ])
            ->getForm();

        $form->handleRequest($request);

        $recettes = [];
        $pourcentage = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedIngredients = $form->getData()['ingredients'];

            if (count($selectedIngredients) > 0) {
                $recettes = $entityManager->getRepository(Recette::class)->findByIngredients($selectedIngredients);

                foreach ($recettes as $recette) {
                    $totalIngredients = count($selectedIngredients);
                    $availableIngredients = 0;

                    foreach ($selectedIngredients as $ingredientNom) {
                        if ($recette->hasIngredient($ingredientNom)) {
                            $availableIngredients++;
                        }
                    }

                    $pourcentage[$recette->getId()] = ($availableIngredients / $totalIngredients) * 100;
                }
            }
        }

        return $this->render('recipe/search.html.twig', [
            'form' => $form->createView(),
            'recettes' => $recettes,
            'pourcentage' => $pourcentage,
        ]);
    }
}
