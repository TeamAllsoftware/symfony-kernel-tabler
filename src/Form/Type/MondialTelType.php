<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MondialTelType extends BaseType
{
//    Masque de saisie pour le numéro de telephone et fax avec indicatif de pays

    public function configureOptions(OptionsResolver $resolver)
    {
        $attr = [
            'data-input-mask' => '+9{1,3}9{9}',
            'data-input-mask-place-holder' => "",
        ];

        $resolver->setDefaults([
            'attr'          => $attr,
        ]);
    }

    public function getParent(): string
    {
        return TelType::class;
    }
}
