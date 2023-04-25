<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,
            [
                'label' => 'Adresse email',
                'help' => 'L\'email ne doit pas dépasser 180 caractères',
                'empty_data' => ''
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                // Fetch user with the event
                $user = $event->getData();
                // Fetch the form thanks to the event
                $form = $event->getForm();
                // Preparing password field
                //If user exist then his id is not null
                if ($user->getId() !== null) {
                    
                    //  for edit form
                    $form->add('password', PasswordType::class, 
                    [
                        'label' => 'Mot de passe ',
                        'mapped' => false,
                        'help' => 'Le mot de passe ne doit pas dépasser 255 caractères',
                        'attr' => [
                            'placeholder' => 'Laissez vide si inchangé'
                        ]
                    ]);
                } else {
                    //for create form
                    $form->add('password', PasswordType::class, 
                    [
                        'label' => 'Mot de passe ',
                        'help' => 'Le mot de passe ne doit pas dépasser 255 caractères',
                    ]);
                }
            })
            ->add('lastname', TextType::class,
            [
                'label'=> 'Nom *',
                'help' => 'Le nom ne doit pas dépasser 255 caractères',
                'empty_data' => ''
            ])
            ->add('firstname', TextType::class,
            [
                'label' => 'Prénom ',
                'help' => 'Le prénom ne doit pas dépasser 255 caractères',
                'empty_data' => ''
            ])
            ->add('nickname', TextType::class,
            [
                'label'=> 'Pseudo',
                'help' => 'Le pseudo ne doit pas dépasser 255 caractères',
            ])
            ->add('dateOfBirth', DateType::class,
            [                
                'label'=> 'Date de naissance',                
                'years' => range(date('Y')+0, 1900)      
            ])
            ->add('presentation', TextareaType::class,
            [
                'label' => 'Présentation'
            ])
            ->add('avatar', UrlType::class,
            [
                'label' => 'Photo de profil',
                'help' => 'L\'url ne doit pas dépasser 255 caractères',
                'attr' => [
                    'placeholder' => 'par ex: https://...'

                ]
            ])
            ->add('presentation', TextType::class,
            [
                'label' => 'Présentation',
                'help' => 'La présentation ne doit pas dépasser 800 caractères',
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
