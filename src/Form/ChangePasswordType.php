<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'disabled'=>true,
                'label'=>'Adresse mail'
            ])
            ->add('old_password',PasswordType::class,[
                'mapped'=>false, 
                'label'=>'Mot de passe actuel',
                'attr'=>[
                    'placeholder'=>'Mot de passe actuel'
                ]
            ])
            ->add('new_password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'mapped'=>false, 
                'invalid_message'=>'Le mot de passe et le password ne match pas',
                'label'=>'Votre mot de passe',
                'required'=>true,
                'first_options'=>[
                    'label'=>'Nouveau mot de passe',
                    'attr'=>[
                        'placeholder'=>'Nouveau mot de passe'
                    ]
                ],
                'second_options'=>[
                    'label'=>'Confirmez votre nouveau mot de passe',
                    'attr'=>[
                        'placeholder'=>'Confirmation nouveau mot de passe'
                    ]
                ],
                
            ])
            ->add('Firstname',TextType::class,[
                'disabled'=>true,
                'label'=>'Prénom'

            ])
            ->add('Lastname',TextType::class,[
                'disabled'=>true,
                'label'=>'Nom'

            ])
            ->add('submit',SubmitType::class,[
                'label'=>"Mettre à jour"
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
