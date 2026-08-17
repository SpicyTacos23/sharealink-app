<?php

namespace App\Form;

use App\Enum\MediaLinkLanguage;
use App\Enum\MediaLinkServer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewMediaFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('post')
            ->setAction($options['action'])
            ->add('server', ChoiceType::class, [
                'label' => 'mediaFile.form.label.server',
                'required' => true,
                'choices' => array_combine(
                    array_map(fn($c) => $c->value, MediaLinkServer::cases()),
                    array_map(fn($c) => $c->value, MediaLinkServer::cases())
                ),
            ])

            ->add('quality', ChoiceType::class, [
                'label' => 'mediaFile.form.label.quality',
                'choices' => [
                    'HD 1080P' => 1080,
                    'HD 720P'  => 720,
                    'SD 480P'  => 480,
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
            ])

            ->add('language', EnumType::class, [
                'label' => 'mediaFile.form.label.language',
                'class' => MediaLinkLanguage::class,
                'choice_attr' => function ($choice, $key, $value) {
                    return [
                        'data-flag' => strtolower($value),
                    ];
                },
            ])

            ->add('link', TextType::class, [
                'label' => 'mediaFile.form.label.link',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])

            ->add('iframe', TextType::class, [
                'label' => 'mediaFile.form.label.iframe',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                ]
            ])

            ->add('movie', HiddenType::class)
            ->add('movieImage', HiddenType::class)
            ->add('movieTitle', HiddenType::class)


            ->add('submit', SubmitType::class, [
                'label' => '<span class="send-icon">📨</span> Send',
                'label_html' => true,
                'attr' => [
                    'class' => 'btn-send'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'movie' => null,
            'movieImage' => null,
            'movieTitle' => null
        ]);
    }
}
