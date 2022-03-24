<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\EventSubscriber\FormFilterSubscriber;
use Allsoftware\SymfonyKernelTabler\Form\Type\EventSubscriber\TomSelectAJAXSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TomSelectAJAXType extends TomSelectType
{
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        FormFilterSubscriber $subscriber,
        RouterInterface $router,
        private TomSelectAJAXSubscriber $tomSelectAJAXSubscriber
    ){
        parent::__construct(translator: $translator, entityManager: $entityManager, subscriber: $subscriber, router: $router);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'search_min_length' => 2,
            'choices'           => [],
            'choice_value'      => null,
            'choice_label'      => null,
            'with_subscribers'  => true,
        ]);

        $resolver->setRequired([
            'query_builder',
            'url',
            'choice_value',
            'choice_label',
            'search_field',
        ]);

        $resolver->setAllowedTypes('query_builder', ['callable', QueryBuilder::class]);
        $resolver->setAllowedTypes('url'         , 'string');
        $resolver->setAllowedTypes('choice_value', 'string');
        $resolver->setAllowedTypes('choice_label', 'string');
        $resolver->setAllowedTypes('search_field', ['string', 'array']);

        $resolver->setNormalizer('search_field', static function (Options $options, $searchField) {
            if (is_string($searchField)) {
                $searchField = (array) $searchField;
            }

            return array_combine(array_values($searchField), array_values($searchField));
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['with_subscribers']) {
            $builder->addEventSubscriber($this->tomSelectAJAXSubscriber);
        }
    }

    public function getDefaultAttr(Options $options): array
    {
        $default_parent = parent::getDefaultAttr($options);
        return array_merge($default_parent, [
            'class'                     => 'tom-select-AJAX',
            'data-tom-url'              => $options['url'],
            'data-tom-value-field'      => $options['choice_value'],
            'data-tom-label-field'      => $options['choice_label'],
            'data-tom-search-field'     => json_encode(array_values($options['search_field'])),
        ]);
    }
}
