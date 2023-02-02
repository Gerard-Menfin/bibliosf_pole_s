<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * L'annotation (ou attribut en PHP 8) est un commentaire qui sera utilisé pour ajouter
     * des informations (méta-données) à une fonction ou une classe.
     * Symfony utilise les annotations pour définir les routes du projet.
     * Une route est un lien entre une adresse URL (le chemin '/test') et une fonction d'un contrôleur. Cette fonction
     * sera exécutée lorsque l'utilisateur voudra accéder à l'URL.
     * 
     * Route est une classe donc Route() est le constructeur de cette classe.
     * Le 1er argument est le chemin (URL) : il doit toujours commencer par '/'
     * Les autres arguments seront défini par leur nom suivi de la valeur, par exemple :
     *      name: 'app_test'        signifie que cette route à pour nom app_test (ce nom sera utilisé pour les redirections, ...)
     */
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        /**
         * Pour générer l'affichage, on utilise la fonction 'render' définie dans la classe AbstractController.
         * (NB : tous les contrôleurs doivent hériter de cette classe)
         * La fonction 'render' a 2 arguments : 
         *      • le fichier qui sera utilisé pour la vue. Le chemin du fichier est donné à partir du dossier 'templates'.
         *          ça signifie que tous les fichiers twig seront dans le dossier templates.
         *      • un array associatif contenant les variables qui seront utilisées dans le fichier twig.
         *          Les indices du tableau correspondent aux noms des variables dans le fichier twig.
         *
         * NB : tous les fichiers twig doivent avoir l'extension suivante :     .html.twig
         * 
         */
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/azerty')]
    public function nouvelle(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/salut', name: 'app_test_salut')]
    #[IsGranted("ROLE_BIBLIO")]
    public function salut()
    {
        $personne = "Gérard";
        return $this->render("test/salut.html.twig", [ "prenom" => $personne ]);
    }

    /**
     * Dans le chemin d'une route, tout ce qui est entre les {} est un paramètre.
     * Ce paramètre peut être remplacé par n'importe quelle chaîne de caractères dans l'URL.
     * Pour récupérer et utiliser la valeur de ce paramètre dans la fonction, il faut
     * déclarer un argument ayant le même nom que le paramètre.
     */
    #[Route('/test/salut/{prenom}', name: 'app_test_salutation')]
    public function salutation($prenom)
    {
        return $this->render("test/salut.html.twig", [ "prenom" => $prenom ]);
    }

    /**
     * 
     */
    #[Route('/test/calcul/{a}/{b}', name: 'app_test_calcul', requirements: ['a' => '\d+', 'b' => '[0-9]+'])]
    public function calcul($a, $b)
    {
        /* EXERCICE : Modifiez le code et créer le fichier twig pour afficher les valeurs de a et b */
        // return $this->render("test/calcul.html.twig", [ "a" => $a, "b" => $b ]);
        return $this->render("test/calcul.html.twig", compact("a", "b"));
    }

    #[Route('/test/boucle/{taille}', name: 'app_test_boucle')]
    public function boucle($taille)
    {
        $maVariable = [45, "texte", true, false, 512];
        return $this->render("test/boucle.html.twig", [
            "taille" => $taille, 
            "maVariable" => $maVariable
        ]);

        // return $this->render("test/boucle.html.twig", compact("taille", "maVariable"));
    }


    #[Route("/test/boucle2", name: 'app_test_boucle2')]
    public function boucle2()
    {
        $maVariable = ["nom" => "Onyme", "prenom" => "Anne", "age" => 35];
        return $this->render("test/boucle2.html.twig", compact("maVariable"));
    }

    /* EXERCICE : ajouter la route '/test/exercices' 
        Dans la fonction du controleur, déclarer une tableau avec les valeurs suivantes : 
            "bonjour"
            "bienvenue"
            "au"
            "cours"
            "de"
            "symfony"

        Afficher toutes les valeurs du tableau (dans un nouveau fichier twig) sur une ligne, dans une balise <p>
    */
    #[Route("/test/exercices", name: 'app_test_exercices')]
    public function exercices()
    {
        $tableau = ["bonjour", "bienvenue", "au", "cours", "de", "symfony"];
        return $this->render("/test/exercices.html.twig", ["tableau" => $tableau]);
    }


}
