<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required'    => true,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'required'    => true,
                'label'       => false,
                'attr' => [
                    'placeholder' => 'Votre pseudo publique'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'Les 2 mots de passe doivent correspondre',
                'mapped'          => false,
                'required'        => true,
                'first_options'  => [
                    'label' => false,
                    'attr'  => [
                        'placeholder' => 'Mot de passe'
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr'  => [
                        'placeholder' => 'Confirmer le mot de passe'
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} caractères',
                        'max'        => 4096,
                    ]),
                ],
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => "M'enregistrer",
                'attr' => [
                    'class' => "uk-button uk-button-default"
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
