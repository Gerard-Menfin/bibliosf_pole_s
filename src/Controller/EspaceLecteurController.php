<?php

namespace App\Controller;

use DateTime;
use stdClass;
use App\Entity\Livre;
use App\Entity\Emprunt;
use App\Repository\LivreRepository;
use App\Repository\EmpruntRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/espace-lecteur', name: 'app_espace_lecteur')]
class EspaceLecteurController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(RequestStack $rs): Response
    {
        /* Dans le contrôleur, si on veut récupérer l'objet Abonne contenant les informations de l'utilisateur connecté, on utilise
                $this->getUser() (vaut null si l'utilisateur n'est pas connecté)
            $abonneConnecte = $this->getUser();        */

        $session = $rs->getSession();
        $panier = $session->get("panier", []);

        return $this->render('espace_lecteur/index.html.twig', compact("panier"));
    }


    #[Route("/emprunter-livre-{id}", name: "_emprunter", requirements: ["id" => "\d+"])]
    public function emprunter(int $id, LivreRepository $lr, EmpruntRepository $er)
    {
        $livre = $lr->find($id);
        if( $livre ) {
            $emprunt = new Emprunt;
            $emprunt->setLivre( $livre );
            $emprunt->setAbonne( $this->getUser() );
            $emprunt->setSortie( new \DateTime() );
            $er->save($emprunt, true);
            return $this->redirectToRoute("app_espace_lecteur");
        } else {
            throw $this->createNotFoundException("Aucun livre ne correspondant à cet identifiant");
        }
    }


    #[Route("/reserver-livre-{id}", name: "_reserver", requirements: ["id" => "\d+"], methods: ["PUT"])]
    public function reserver(int $id, LivreRepository $lr, EmpruntRepository $er, RequestStack $rs) {
        $livre = $lr->find($id);
        if( $livre ) {
            $session = $rs->getSession();
            $panier = $session->get("panier", []);
            $dejaReserve = false;
            foreach ($panier as $ligne) {
                if( $livre->getId() == $ligne["livre"]->getId() ) {
                    $this->addFlash("info", "Ce livre fait déjà partie de vos réservations");
                    $dejaReserve = true;
                }
            }
            if( !$dejaReserve ){
                $panier[] = [ "livre" => $livre, "date" => date("Y-m-d") ];
            }
            $session->set("panier", $panier);
            // return $this->redirectToRoute("app_espace_lecteur");
            $reponse = new stdClass;
            $reponse->nb = count($panier);
            return $this->json($reponse);
        } else {
            throw $this->createNotFoundException("Aucun livre ne correspondant à cet identifiant");
        }
    }

    #[Route("/supprimer-reservation-livre-{id}", name: "_supprimer_reservation", requirements: ["id" => "\d+"])]
    public function supprimerReservation(Livre $livre, RequestStack $rs)
    {
        /* EXERCICE : écrire le code de cette route puis ajouter un lien dans l'Espace Lecteur pour 
                        pouvoir enlever un livre de la liste des réservations  */

        /* On peut récupérer un enregistrement en bdd avec le paramètre d'une route :
            - le paramètre doit avoir le nom d'un champ de la table
            - dans les arguments de la méthode liée à la route, il faut mettre un objet entité, pas
                besoin de mettre le paramètre lui-même (et plus besoin du Repository non plus)
        */
        // $livre = $lr->find($id);
        
        $panier = $rs->getSession()->get("panier", []);
        foreach($panier as $indice => $ligne) {
            if( $livre->getId() == $ligne["livre"]->getId() ) {
                unset($panier[$indice]);
                break;
            }
        }
        $rs->getSession()->set("panier", $panier);
        $this->addFlash("success", "Le livre " . $livre->getTitre() . " a été retiré de vos réservations");
        return $this->redirectToRoute("app_espace_lecteur");
    }


    #[Route("/supprimer-reservations", name: "_supprimer_reservations")]
    public function supprimerReservations(RequestStack $rs)
    {
        // $rs->getSession()->remove("panier");
        $rs->getSession()->set("panier", []);
        $this->addFlash("success", "Vous n'avez plus de réservations !");
        return $this->redirectToRoute("app_espace_lecteur");
    }

    #[Route("/rendre-emprunt-{id}", name: "_rendre_emprunt")]
    public function rendreEmprunt(Emprunt $emprunt, EmpruntRepository $er)
    {
        $emprunt->setRetour( new DateTime() );
        $er->save($emprunt, true);
        return $this->redirectToRoute("app_espace_lecteur");
    }
    
}
