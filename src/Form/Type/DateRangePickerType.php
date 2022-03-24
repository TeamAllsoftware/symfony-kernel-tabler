<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangePickerType extends AbstractType
{
    const default_options = [
        'mapped' => false,
        'attr' => [
            'data-widget' => 'daterangepicker',
            'class' => 'date-range',
        ]
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(self::default_options);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
