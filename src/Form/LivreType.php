<?php

namespace App\Form;

use App\Entity\Genre;
use Doctrine\ORM\Mapping\Entity;
use App\Entity\Livre, App\Entity\Auteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                "constraints" => [
                    new NotNull(["message" => "Le titre ne peut pas être vide"])
                ]
            ])
            ->add('resume', TextareaType::class, [
                "label" => "Résumé"

            ])
            ->add('couverture')
            ->add('auteur', EntityType::class, [
                "class"         => Auteur::class,
                "choice_label"  => function ($auteur) {
                    return $auteur->getPrenom() . " " . $auteur->getNom();
                },
                "placeholder"   => "Choisissez un auteur..."   
            ])
            ->add("genres", EntityType::class, [
                "class"         => Genre::class,
                "choice_label"  => "libelle",  // la propriété 'libelle' de la classe Genre sera utilisée pour l'affichage des valeurs
                "multiple"      => true,
                "expanded"      => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
