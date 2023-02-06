<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livre')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_admin_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('admin/livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivreRepository $livreRepository): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // si un fichier a été uploadé dans l'input nommé 'couverture'...
            $fichier = $form->get("couverture")->getData();
            if( $fichier ) {
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauFichier = $nomFichier . "_" . uniqid();
                $nouveauFichier .= "." . $fichier->guessExtension();
                // on copie le fichier uploadé dans le dossier image, avec le nouveau nom de fichier
                $fichier->move($this->getParameter("dossier_images"), $nouveauFichier);

                // on modifie la propriété 'couverture' de l'objet Livre avant qu'il ne soit enregistré en bdd
                $livre->setCouverture($nouveauFichier);
            }

            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_admin_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('admin/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_admin_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get("couverture")->getData();
            if( $fichier ) {
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauFichier = $nomFichier . "_" . uniqid();
                $nouveauFichier .= "." . $fichier->guessExtension();
                // on copie le fichier uploadé dans le dossier image, avec le nouveau nom de fichier
                $fichier->move($this->getParameter("dossier_images"), $nouveauFichier);

                // on modifie la propriété 'couverture' de l'objet Livre avant qu'il ne soit enregistré en bdd
                $livre->setCouverture($nouveauFichier);
            }

            $livreRepository->save($livre, true);

            return $this->redirectToRoute('app_admin_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, LivreRepository $livreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $livreRepository->remove($livre, true);
        }

        return $this->redirectToRoute('app_admin_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
