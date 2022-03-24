<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuillType extends BaseType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'show_tool_bar' => true,
            'mention_url' => false,
            'mention_allowed_chars' => '^([a-zA-Z0-9_]|[^\w\s])*$',
            'mention_denotation_chars' => [],
            'mention_show_denotation_char' => false,
        ]);

        $resolver->setAllowedTypes('show_tool_bar'                      , ['bool']);
        $resolver->setAllowedTypes('mention_url'                        , ['string', 'bool']);
        $resolver->setAllowedTypes('mention_allowed_chars'              , 'string');
        $resolver->setAllowedTypes('mention_denotation_chars'           , ['string', 'array']);
        $resolver->setAllowedTypes('mention_show_denotation_char'       , 'bool');

        $resolver->setNormalizer('mention_denotation_chars', static function (Options $options, $searchField) {
            if (is_string($searchField)) {
                $searchField = (array) $searchField;
            }

            return array_values($searchField);
        });
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getDefaultAttr(Options $options): array
    {
        $default_parent = parent::getDefaultAttr($options);
        return array_merge($default_parent, [
            'class'                                       => 'quill-symfony',
            'data-quill-show-toolbar'                     => $options['show_tool_bar'],
            'data-quill-mention-url'                      => $options['mention_url'],
            'data-quill-mention-allowed-chars'            => $options['mention_allowed_chars'],
            'data-quill-mention-denotation-chars'         => json_encode($options['mention_denotation_chars']),
            'data-quill-mention-show-denotation-char'     => $options['mention_show_denotation_char'],
        ]);
    }
}
