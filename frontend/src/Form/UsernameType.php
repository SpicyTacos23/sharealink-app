<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsernameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['action'] === null) {
            throw new \InvalidArgumentException("The 'action' option must be set for UsernameType form -t.");
        }
        $builder->setAction($options['action']);
        $builder->add('username', TextType::class, [
            'label' => 'Nuevo nombre de usuario',
            'required' => true,
            'attr' => [
                'placeholder' => 'Introduce tu nuevo nombre'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'action' => null
        ]);
    }
}
