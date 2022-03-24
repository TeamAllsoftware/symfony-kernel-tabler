<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaAutoSizeType extends BaseType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('required', false);
    }

    public function getDefaultAttr(Options $options): array
    {
        $default_parent = parent::getDefaultAttr($options);

        return array_merge($default_parent, [
            'data-bs-toggle' => "autosize",
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
