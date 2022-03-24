<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type\EventSubscriber;

use Allsoftware\SymfonyKernelTabler\Form\BaseType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TomSelectAJAXSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => ['_populateChoices',-50],
            FormEvents::PRE_SUBMIT   => ['_populateChoices',-50],
        ];
    }

    public function _populateChoices(FormEvent $event){
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();

        $options['choices'] = $this->_getChoices($event->getData(), $options);
        $options['with_subscribers'] = false;

        BaseType::redoField($event->getForm(), $options);
    }

    private function _getChoices(mixed $data, array $options) {
        /** @var QueryBuilder $query_builder */
        $query_builder = $options['query_builder'];

        return $query_builder->getQuery()->getResult();
    }
}
