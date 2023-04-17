<?php

namespace App\Form;

use App\Entity\Artwork;
use App\Entity\Exhibition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'label' => 'Titre de l\'oeuvre'
            ])
            ->add('description', TextType::class,
            [
                'label' => 'Description de l\'oeuvre'
            ])
            ->add('picture', UrlType::class,
            [
                'label' => 'Lien de l\'oeuvre',
                'attr' => [
                    'placeholder' => 'par ex: https://...'

                ]
            ])
            // ->add('status')
            ->add('exhibition', EntityType::class,
            [
                'label' => 'A quelle exposition voulez-vous lier cette oeuvre ?',
                'class' => Exhibition::class,
                'expanded' => true,
                'multiple' => false,
                'choice_label' => 'title',
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
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
