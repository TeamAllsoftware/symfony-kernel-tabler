<?php

namespace Allsoftware\SymfonyKernelTabler\Form;

use Allsoftware\SymfonyKernelTabler\EventSubscriber\FormFilterSubscriber;
use Allsoftware\SymfonyKernelTabler\Helper\GlobalHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseType extends AbstractType
{
    const CST_Range_small = 1;
    const CST_Range_medium = 2;
    const CST_Range_large = 3;
    const CST_Range_extraLarge = 4;

    const CST_Field_ItemsPerPage = 'itemsPerPage';

    public function __construct(
        protected TranslatorInterface $translator,
        protected EntityManagerInterface $entityManager,
        protected FormFilterSubscriber $subscriber,
        protected RouterInterface $router,
    ) {  }

    public function addFormItemsPerPage(FormBuilderInterface $builder, $rangeWidth = self::CST_Range_small, $defaultValue = 15) : void
    {
        $choices = [
            15 => 15,
            25 => 25
        ];

        if ($rangeWidth >= self::CST_Range_medium){
            $choices += [
                50 => 50,
                100 => 100,
            ];
        }
        if ($rangeWidth >= self::CST_Range_large){
            $choices += [
                150 => 150,
            ];
        }
        if ($rangeWidth >= self::CST_Range_extraLarge){
            $choices += [
                200 => 200,
                250 => 250,
            ];
        }

        $builder
            ->add(
                self::CST_Field_ItemsPerPage,
                ChoiceType::class,
                [
                    'label'    => $this->translator->trans('label.item_per_pages'),
                    'required' => true,
                    'multiple' => false,
                    'choices' => $choices,
                    'data' => $defaultValue,
                ]
            );
    }

    public static function redoField(FormInterface $field, array $extraOptions = []): ?FormInterface
    {
        $parent = $field->getParent();
        $options = $field->getConfig()->getOptions();
        $type = get_class($field->getConfig()->getType()->getInnerType());

        $name = $field->getName();
        $parent->remove($name);
        return $parent->add($name, $type, GlobalHelper::array_mergeRecursiveOverride($options, $extraOptions));
    }

    public static function redoFieldBuilder(FormBuilderInterface $builder, FormBuilderInterface $field, array $extraOptions = []): ?FormBuilderInterface
    {
        $options = $field->getOptions();
        $type = get_class($field->getType()->getInnerType());

        $name = $field->getName();
        $builder->remove($name);
        return $builder->add($name, $type, GlobalHelper::array_mergeRecursiveOverride($options, $extraOptions));
    }

    protected function disableField(FormInterface $field, bool $isReadonly = false, array $options = []) : void
    {
        $this->redoField(
            $field,
            GlobalHelper::array_mergeRecursiveOverride(
                [
                    'disabled' => true,
                    'required' => $isReadonly === false,
                    'attr' => [
                        'class' => 'cursor-not-allowed',
                        'readonly' => $isReadonly === true,
                    ],
                ],
                $options)
        );
    }

    protected function removeField(FormInterface $field) : void
    {
        $parent  = $field->getParent();
        $name    = $field->getName();
        $parent->remove($name);
    }







    public function configureOptions(OptionsResolver $resolver)
    {
        $attr = function(Options $options) {
            $default = $this->getDefaultAttr($options);
            return array_replace($default, $options['new_attr']);
        };

        $resolver->setDefaults([
            'attr'          => $attr,
            'new_attr'      => [],
        ]);

        $resolver->setAllowedTypes('new_attr', 'array');
    }

    public function getDefaultAttr(Options $options): array
    {
        return [];
    }
}
