<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tags', EntityType::class, [
                'label'        => false,
                'class'        => Tag::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true,
                'attr'         => [
                    'class' => 'uk-checkbox',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    "class" => 'uk-button uk-button-default'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
