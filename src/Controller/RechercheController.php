<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {
        $motRecherche = $request->query->get("search");
        $livres = $livreRepository->findByRechercheTitre($motRecherche);
        return $this->render('recherche/index.html.twig', compact("motRecherche", "livres"));
    }
}
