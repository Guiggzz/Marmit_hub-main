<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;

class CommentaireController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commentaire/add/{recetteId}", name="commentaire_add", methods={"POST"})
     */
    public function addCommentaire(Request $request, $recetteId): RedirectResponse
    {
        // Utiliser l'EntityManager pour récupérer la recette
        $recette = $this->entityManager->getRepository(Recette::class)->find($recetteId);

        if (!$recette) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Création du commentaire
        $commentaire = new Commentaire();
        $commentaire->setTexte($request->request->get('texte'));
        $commentaire->setRecette($recette); // Associe la recette

        // Lier le commentaire à l'utilisateur connecté
        $user = $this->getUser();
        if ($user) {
            $commentaire->setUtilisateur($user); // Associe l'utilisateur au commentaire
        }

        // Définir la date actuelle pour le commentaire
        $commentaire->setDate(new \DateTime()); // Cela définit la date actuelle (date et heure)

        // Sauvegarde du commentaire
        $this->entityManager->persist($commentaire);
        $this->entityManager->flush();

        return $this->redirectToRoute('recette_show', ['id' => $recetteId]);
    }

    /**
     * @Route("/commentaire/delete/{id}", name="commentaire_delete", methods={"POST"})
     */
    public function deleteCommentaire($id): RedirectResponse
    {
        // Utiliser l'EntityManager pour récupérer le commentaire
        $commentaire = $this->entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        // Vérifier si l'utilisateur connecté est celui qui a posté le commentaire
        if ($commentaire->getUtilisateur() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('recette_show', ['id' => $commentaire->getRecette()->getId()]);
        }

        // Supprimer le commentaire
        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été supprimé.');
        return $this->redirectToRoute('recette_show', ['id' => $commentaire->getRecette()->getId()]);
    }
}
