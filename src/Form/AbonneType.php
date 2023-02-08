<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $abonne = $options["data"]; // permet de récupérer l'objet utilisé comme données du formulaire.
        $builder
            ->add('pseudo')
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Abonné"            => "ROLE_USER",
                    "Lecteur"           => "ROLE_LECTEUR",
                    "Bibliothécaire"    => "ROLE_BIBLIO",
                    "Administrateur"    => "ROLE_ADMIN"
                ],
                "multiple" => true,
                "expanded" => true,
                "label"    => "Droit d'accès"
            ])
            ->add('password', TextType::class, [
                "label"         => "Mot de passe",
                "mapped"        => false,
                "required"      => $abonne->getId() ? false : true
            ])
            ->add('prenom')
            ->add('nom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
