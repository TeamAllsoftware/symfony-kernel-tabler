<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TomSelectType extends BaseType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'remove'                => false,
            'allow_empty_option'    => false,
            'clear'                 => false,
            'create'                => false,
            'search_min_length'     => 0,
        ]);

        $resolver->setAllowedTypes('remove', 'bool');
        $resolver->setAllowedTypes('allow_empty_option', 'bool');
        $resolver->setAllowedTypes('clear' , 'bool');
        $resolver->setAllowedTypes('create', 'bool');
        $resolver->setAllowedTypes('search_min_length', 'int');
    }

    public function getDefaultAttr(Options $options): array
    {
        $default_parent = parent::getDefaultAttr($options);

        return array_merge($default_parent, [
            'class'                         => 'tom-select',
            'data-tom-remove-button'        => $options['remove'],
            'data-tom-allow-empty-option'   => $options['allow_empty_option'],
            'data-tom-clear-button'         => $options['clear'],
            'data-tom-create'               => $options['create'],
            'data-tom-query-length'         => $options['search_min_length'],
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
