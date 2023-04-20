<?php

namespace App\Form;

use App\Entity\Artwork;
use App\Entity\Exhibition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArtworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'label' => 'Titre de l\'oeuvre',
                'empty_data' => ''
               
            ])
            ->add('description', TextareaType::class,
            [
                'label' => 'Description de l\'oeuvre',
            ])
            ->add('picture', UrlType::class,
            [
                'label' => 'Lien de l\'oeuvre',
                'attr' => [
                    'placeholder' => 'par ex: https://...'

                ],
                'empty_data' => ''
               
            ])
            // ->add('status')
            ->add('exhibition', EntityType::class,
            [
                'label' => 'A quelle exposition voulez-vous lier cette oeuvre ?',
                'class' => Exhibition::class,
                'choice_label' => 'title',
                'placeholder' => 'Veuillez sélectionner une exposition'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artwork::class,
        ]);
    }
}
