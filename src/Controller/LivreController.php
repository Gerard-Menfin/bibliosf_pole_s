<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivreRepository;
use App\Entity\Livre;
use App\Form\FormLivreType;


class LivreController extends AbstractController
{
    #[Route('/tous-les-livres', name: 'app_livre')]
    public function index(LivreRepository $livreRepository): Response
    {
        // $livreRepository = new LivreRepository;
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    /**
     * La classe Request permet de gérer tout ce qui vient d'une requête HTTP.
     * Comme pour la classe Repository, on doit l'utiliser en injection de dépendance.
     * L'objet $request a des propriétés qui contiennent toutes les valeurs des variables
     * super-globales de PHP. Par exemple : 
     *       $request->query         contient        $_GET
     *       $request->request       contient        $_POST
     *       $request->files         contient        $_FILES
     *       $request->server        contient        $_SERVER
     *       $request->cookies       contient        $_COOKIES
     *       $request->session       contient        $_SESSION
     * 
     *   Ces différents objets ont des méthodes communes : get, has,...    
     *   La méthode get() permet de récupérer la valeur voulue.
     *   𝒆̲̅𝒙̲̅ : $motRecherche = $request->query->get("search");  
     *        $motRecherche = $_GET["search"]
     */
    #[Route('/ajouter-un-livre', name: 'app_livre_ajouter')]
    public function ajouter(Request $request, LivreRepository $lr)
    {
        if($request->isMethod("POST")){
            $titre = $request->request->get("titre");
            $resume = $request->request->get("resume");

            if( $titre ) {
                $livre = new Livre;
                $livre->setTitre($titre);
                $livre->setResume($resume);
                $lr->save($livre, true);
                /* La méthode addFlash permet d'ajouter un message flash (ce message sera affiché une fois et ensuite il
                sera supprimé de la session ) */
                $this->addFlash("success", "Le nouveau livre a bien été enregistré");
                return $this->redirectToRoute('app_livre');
            } 
            $this->addFlash("danger", "Le titre ne peut pas être vide !");
        }
        return $this->render("livre/formulaire.html.twig");
    }

    #[Route('/nouveau-livre', name: 'app_livre_nouveau')]
    public function nouveau(Request $rq, LivreRepository $lr)
    {
        $livre = new Livre;
        /* La fonction createForm permet de créer un objet qui va représenter un formulaire. 
            Le 1er argument sera la classe utilisée pour créer le formulaire (dans le dossier src/Form)
            Le 2ième argument sera un objet entité qui sera lié au formualre */
        $formulaire = $this->createForm(FormLivreType::class, $livre);
        /* La méthode 'handleRequest' permet à la variable $formulaire de gérer les informations venant de la requête HTTP (en utilisant l'objet 
            de la classe Request).
            Les propriétés de l'objet Livre vont être automatiquement modifiées avec les valeurs tapées dans le formulaire.
        */
        $formulaire->handleRequest($rq);
        if($formulaire->isSubmitted() && $formulaire->isValid()) {
            $lr->save($livre, true);
            $this->addFlash("success", "Le livre <strong>" . $livre->getTitre() ."</strong> a été enregistré !");
            return $this->redirectToRoute("app_livre");
        }

        return $this->render("livre/form.html.twig", [
            "formulaireLivre" => $formulaire->createView()
        ]);

    }

    #[Route('/modifier-le-livre-{id}', name: 'app_livre_modifier', requirements: ["id" => "\d+"])]
    public function modifier(Request $rq, LivreRepository $lr, int $id)
    {
        $livre = $lr->find($id);
        if($livre) {
            $formulaire = $this->createForm(FormLivreType::class, $livre);
            $formulaire->handleRequest($rq);

            if($formulaire->isSubmitted() && $formulaire->isValid()) {
                $lr->save($livre, true);
                $this->addFlash("success", "Le livre <strong>" . $livre->getTitre() ."</strong> a été modifié !");
                return $this->redirectToRoute("app_livre");
            }

            return $this->render("livre/form.html.twig", [
                "formulaireLivre" => $formulaire->createView()
            ]);
        }
    }

    #[Route('/supprimer-le-livre-{id}', name: 'app_livre_supprimer', requirements: ["id" => "\d+"])]
    public function supprimer(int $id, Request $rq, LivreRepository $lr)
    {
        $livre = $lr->find($id);
        if($livre) {
            if($rq->isMethod("POST")) {
                $lr->remove($livre, true);
                $this->addFlash("success", "Le livre <strong>" . $livre->getTitre() . "</strong> a été supprimé !" );
                return $this->redirectToRoute("app_livre");
            }

            return $this->render("livre/confirmation.html.twig", [
                "livre" => $livre
            ]);
        }
        throw $this->createNotFoundException("Il n'y a pas de livre ayant cet identifiant");
    }
}
