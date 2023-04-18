<?php

namespace App\Form;

use App\Entity\Exhibition;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExhibitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'label' => 'Titre de l\'exposition',
                'help' => 'Le titre ne doit pas dépasser 255 caractères',
                'empty_data' => ''
            ]
            )
            ->add('description', TextType::class,
            [
                'label' => 'Description de l\'exposition'
            ])
            ->add('artist', EntityType::class,
            [
                'label' => 'Artiste',
                'class' => User::class,
                'query_builder' => function(EntityRepository $er)
                {
                    return $er->createQueryBuilder('u')
                    ->where('u.roles LIKE :role')
                    ->setParameter('role', '%"'.'ROLE_ARTIST'.'"%');
                },
                'choice_label' => function (User $user)
                {
                    return $user->getFirstname().' '.$user->getLastname();
                },
                'placeholder' => 'Choisissez un artiste',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exhibition::class,
        ]);
    }
}
