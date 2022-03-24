<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SingleDatePickerType extends AbstractType
{
    const default_options = [
        'html5'     => false,
        'required'  => false,
        'attr'      => [
            'data-widget' => 'daterangepicker',
            'class' => 'single-date',
        ],
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(self::default_options);
        $resolver->setRequired('format');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new DateTimeToStringTransformer(format: $options['format']);
        $builder->addModelTransformer($transformer);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
