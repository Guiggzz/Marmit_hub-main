<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Entity\RecetteIngredient;
use App\Entity\Commentaire;
use App\Form\RecetteType;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\RecetteRepository;
use App\Repository\IngredientRepository;
use App\Form\IngredientType;


class RecetteController extends AbstractController
{
    #[Route('/recette/nouvelle', name: 'app_recette_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $recette = new Recette();

        // Associer l'utilisateur connecté
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        $recette->setUtilisateur($utilisateur);

        // Création et traitement du formulaire
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('recettes_photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_recette_create');
                }

                $recette->setPhoto($newFilename);
            }

            // Gérer manuellement les RecetteIngredients
            $ingredientsData = $form->get('ingredients')->getData();
            foreach ($ingredientsData as $ingredient) {
                // Vérifier si l'ingrédient est déjà présent
                if (!$recette->getIngredients()->contains($ingredient)) {
                    $recetteIngredient = new RecetteIngredient();
                    $recetteIngredient->setRecette($recette);
                    $recetteIngredient->setIngredient($ingredient);
                    $recette->addRecetteIngredient($recetteIngredient);
                    $em->persist($recetteIngredient);
                }
            }

            // Enregistrement de la recette et des ingrédients
            $em->persist($recette);
            $em->flush();

            $this->addFlash('success', 'La recette a été créée avec succès.');
            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/nouvelle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/{id}', name: 'recette_show')]
    public function show(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $recette = $em->getRepository(Recette::class)->find($id);

        if (!$recette) {
            throw $this->createNotFoundException('Recette non trouvée.');
        }


        // Création du formulaire pour le commentaire
        $commentaire = new Commentaire();
        $commentaire->setRecette($recette);
        $commentaire->setUtilisateur($this->getUser());
        $commentaire->setDate(new \DateTime());

        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);
        $formCommentaire->handleRequest($request);

        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'formCommentaire' => $formCommentaire->createView(),
        ]);
    }

    #[Route('/recette/supprimer/{id}', name: 'recette_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $recette = $em->getRepository(Recette::class)->find($id);

        if (!$recette) {
            throw $this->createNotFoundException('Recette non trouvée.');
        }

        // Vérification : l'utilisateur connecté est-il l'auteur ?
        if ($recette->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette recette.');
            return $this->redirectToRoute('app_home');
        }

        // Supprimer la recette
        $em->remove($recette);
        $em->flush();

        $this->addFlash('success', 'La recette a été supprimée avec succès.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/recette/{id}/edit', name: 'recette_edit')]
    public function edit(int $id, RecetteRepository $recetteRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Trouve la recette à éditer
        $recette = $recetteRepository->find($id);

        if (!$recette) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Vérifie si l'utilisateur connecté est bien celui qui a créé la recette
        if ($recette->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette recette.');
            return $this->redirectToRoute('recette_index');
        }

        // Crée le formulaire d'édition
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la nouvelle photo si présente
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('recettes_photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('recette_edit', ['id' => $recette->getId()]);
                }

                $recette->setPhoto($newFilename);
            }

            // Mettre à jour les RecetteIngredients
            $ingredientsData = $form->get('ingredients')->getData();

            // Supprimer les ingrédients non inclus dans le formulaire
            foreach ($recette->getRecetteIngredients() as $recetteIngredient) {
                if (!$ingredientsData->contains($recetteIngredient->getIngredient())) {
                    $recette->removeRecetteIngredient($recetteIngredient);
                    $entityManager->remove($recetteIngredient);
                }
            }

            // Ajouter les nouveaux ingrédients
            foreach ($ingredientsData as $ingredient) {
                if (!$recette->getIngredients()->contains($ingredient)) {
                    $recetteIngredient = new RecetteIngredient();
                    $recetteIngredient->setRecette($recette);
                    $recetteIngredient->setIngredient($ingredient);
                    $recette->addRecetteIngredient($recetteIngredient);
                    $entityManager->persist($recetteIngredient);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Recette modifiée avec succès.');

            return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
        }

        return $this->render('recette/modifRecette.html.twig', [
            'form' => $form->createView(),
            'recette' => $recette,
        ]);
    }
    #[Route('/ingredient/{id}/update', name: 'app_ingredient_update')]
    public function update(
        int $id,
        IngredientRepository $ingredientRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $ingredient = $ingredientRepository->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Ingrédient non trouvé.');
        }

        // Vérifier que l'utilisateur connecté est le créateur
        if ($ingredient->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet ingrédient.');
            return $this->redirectToRoute('app_ingredient_list');
        }

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Ingrédient modifié avec succès.');
            return $this->redirectToRoute('app_ingredient_list');
        }

        return $this->render('ingredient/update.html.twig', [
            'form' => $form->createView(),
            'ingredient' => $ingredient,
        ]);
    }
}
