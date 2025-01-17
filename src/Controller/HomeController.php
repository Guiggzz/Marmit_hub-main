<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RecetteRepository $recetteRepository): Response
    {
        $recettes = $recetteRepository->findAll(); // Récupère toutes les recettes

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'recettes' => $recettes // Ajoutez cette ligne pour passer les recettes au template
        ]);
    }
}
