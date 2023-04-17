<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,
            [
                'label' => 'Adresse email'
            ])
            ->add('roles', ChoiceType::class,
            [
                'label' => 'Quel rôle voulez-vous attribuer à cet utilisateur ?',
                'choices' => [
                    'Artiste' => 'ROLE_ARTIST',
                    'Modérateur' => 'ROLE_MODERATOR',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ],

            ])
            ->add('password', PasswordType::class,
            [
                'label' => 'Mot de passe'
            ])
            ->add('lastname', TextType::class,
            [
                'label'=> 'Nom'
            ])
            ->add('firstname', TextType::class,
            [
                'label' => 'Prénom'
            ])
            ->add('nickname', TextType::class,
            [
                'label'=> 'Pseudo'
            ])
            ->add('avatar', UrlType::class,
            [
                'label' => 'Photo de profil',
                'attr' => [
                    'placeholder' => 'par ex: https://...'

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
