<?php

namespace App\Controller\Admin;

use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/abonne')]
#[IsGranted("ROLE_ADMIN")]
class AbonneController extends AbstractController
{
    #[Route('/', name: 'app_admin_abonne_index', methods: ['GET'])]
    public function index(AbonneRepository $abonneRepository): Response
    {
        return $this->render('admin/abonne/index.html.twig', [
            'abonnes' => $abonneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_abonne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AbonneRepository $abonneRepository): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonneRepository->save($abonne, true);

            return $this->redirectToRoute('app_admin_abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/abonne/new.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonne_show', methods: ['GET'])]
    public function show(Abonne $abonne): Response
    {
        return $this->render('admin/abonne/show.html.twig', [
            'abonne' => $abonne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_abonne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonne $abonne, AbonneRepository $abonneRepository): Response
    {
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $abonneRepository->save($abonne, true);

            return $this->redirectToRoute('app_admin_abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/abonne/edit.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_abonne_delete', methods: ['POST'])]
    public function delete(Request $request, Abonne $abonne, AbonneRepository $abonneRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonne->getId(), $request->request->get('_token'))) {
            $abonneRepository->remove($abonne, true);
        }

        return $this->redirectToRoute('app_admin_abonne_index', [], Response::HTTP_SEE_OTHER);
    }
}
