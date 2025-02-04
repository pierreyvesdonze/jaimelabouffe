<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Nom de la recette'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label'   => false,
                'choices' => [
                    'Apéro'   => 'Apéro',
                    'Entrée'  => 'Entrée',
                    'Plat'    => 'Plat',
                    'Dessert' => 'Dessert',
                ],
                'attr'  => [
                    'class' => 'uk-select',
                ]
            ])
            ->add('ingredient', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Liste les ingrédients'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Décris la recette'
                ]
            ])
            ->add('portion', IntegerType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Pour combien de personnes ?'
                ]
            ])
            ->add('timePrepa', TextType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Temps de préparation (ex. 1h30 ou 24min...)'
                ]
            ])
            ->add('timeCook', TextType::class, [
                'label' => false,
                'attr'  => [
                    'class'       => 'uk-input',
                    'placeholder' => 'Temps de cuisson (ex. 45min)'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Ajoute une image',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '8M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => [
                    "class" => 'uk-button uk-button-default'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
