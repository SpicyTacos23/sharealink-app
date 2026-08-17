<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchTitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->setAction($options['action'])
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-input',
                ],
            ])
            ->add('mediaType', HiddenType::class, [
                'label' => false,
                'data' => $options['mediaType']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'mediaFile.form.label.search',
                'attr' => [
                    'class' => 'btn btn--primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mediaType' => null
        ]);
    }
}
