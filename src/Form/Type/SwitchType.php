<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends BaseType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'form-check-input',
            ],
            'label_attr' => [
                'class' => '',
            ],
            'row_attr' => [
                'class' => 'mb-3 form-switch'
            ]
        ]);
    }


    public function getParent(): string
    {
        return CheckboxType::class;
    }

}
