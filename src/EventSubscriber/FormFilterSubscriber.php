<?php

namespace Allsoftware\SymfonyKernelTabler\EventSubscriber;

use DateTime;
use Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionProperty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Permet de :
 * - Ajouter automatiquement les boutons reset/filtrer dans le FormType
 * - Garder en session les données renseignées dans un filtre
 * - Re-remplir les champs du formulaire si connu depuis la session.
 */
class FormFilterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SessionInterface $session,
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) { }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => [
                // Avant de rendre le formulaire → Encore modifiable
                ['autoCompleteFromSession', 50],
                ['addFilterFormButtons', 40],
            ],
            FormEvents::PRE_SUBMIT   => [
                // Avant que le formulaire soit traité
                ['saveDataInSession'],
            ],
            FormEvents::POST_SUBMIT  => [
                // Après que les données soient SET dans l'objet
                ['checkButtonActions'],
            ],
        ];
    }

    /**
     * Rempli les données d'un formulaire depuis la session en cours
     * @param FormEvent $event
     * @throws Exception
     */
    public function autoCompleteFromSession(FormEvent $event)
    {
        $form = $event->getForm();
        $formName = $this->_getSessionName($form);

        if ($this->session->has($formName)) {
            // Le filtre est présent en session

            $data = $event->getData();
            $filters = $this->session->get($formName);

            if (is_array($filters)) {
                // Pour tous les champs connus en session
                foreach ($filters as $field => $value) {
                    if ($form->has($field)) {
                        // ** Récupération de la config du champ
                        $fieldConfig = $form->get($field)->getConfig();

                        if (self::_fieldHasType($fieldConfig, EntityType::class)) {
                            $entityManager = $fieldConfig->getOption('em');
                            if ($entityManager === null) {
                                $entityManager = $this->entityManager;
                            }
                            if (!is_array($value)) $value = [$value];
                            $newVal = new ArrayCollection();
                            foreach ($value as $val) {
                                $className = $fieldConfig->getOption('class');
                                $val = $entityManager
                                    ->getRepository($className)
                                    ->find($val);
                                $newVal->add($val);
                            }
                            $value = $newVal;
                        } elseif (self::_fieldHasType($fieldConfig, EntityType::class)) {
                            // ===== EntityType =====
                            $entityManager = $fieldConfig->getOption('em');
                            if ($entityManager === null) $entityManager = $this->entityManager;

                            // Récupération du nom de la Class PHP
                            // eg: Tiers
                            $className = $fieldConfig->getOption('class');
                            // ** Rechercher de l'entité concernée
                            $value = $entityManager
                                ->getRepository($className)
                                ->find($value);
                        } elseif (self::_fieldHasType($fieldConfig, CheckboxType::class)) {
                            // ===== CheckBoxType =====
                            // ** CAST en bool
                            $value = (bool) $value;
                        } elseif (self::_fieldHasType($fieldConfig, DateType::class)) {
                            // ===== DateType =====
                            // ** CAST en DateTime
                            $value != '' ? $value = new DateTime($value) : $value = null;
                        }
                    }

                    if (is_array($data)) {
                        // Les données sont en array simple
                        // ** Set de la valeur depuis un tableau
                        $data[$field] = $value;

                    } elseif ($data !== null && property_exists($data, $field)) {
                        // les données proviennent d'une entité/objet
                        $rp = new ReflectionProperty($data,$field);

                        // La propriété a-t-elle un accès public direct
                        // eg: $tiers->cosu
                        if ($rp->isPublic()) $data->{$field} = $value;

                        // La propriété a-t-elle un accès depuis une méthode
                        // eg: $tiers->setCosu()
                        if ($rp->isPrivate() || $rp->isProtected()){
                            $data->{"set".ucfirst($field)}($value);
                        }
                    }
                }
            }

            // Application des données de session au formulaire AVANT affichage html
            $event->setData($data);
        }
    }


    /**
     * Ajout les boutons SUBMIT pour le formulaire de filtre
     * @param FormEvent $event
     * @return void
     */
    public function addFilterFormButtons(FormEvent $event) {
        $event->getForm()
            ->add(
                'reset',
                SubmitType::class,
                [
                    'label' => '<i class="fas fa-eraser me-1"></i>'.$this->translator->trans('action.erase_filter'),
                    'label_html' => true,
                    'attr'  => [
                        'class' => 'btn btn-warning reset-cookie-form-action',
                    ],
                ]
            )
            ->add(
                'filter_data',
                SubmitType::class,
                [
                    'label' => '<i class="fas fa-search me-1"></i>'.$this->translator->trans('action.filter'),
                    'label_html' => true,
                    'attr'  => [
                        'class' => 'btn btn-primary ms-auto',
                    ],
                ]
            )
        ;
    }

    /**
     * Sauvegarde des données du formulaire en sessions
     */
    public function saveDataInSession(FormEvent $event)
    {
        $filters = $event->getData();
        $form = $event->getForm();

        // Save form values in session
        $this->session->set($this->_getSessionName($form), $filters);
    }

    /**
     * Vérification si :
     * - le filtre doit être effacé
     * - le filtre a été validé
     */
    public function checkButtonActions(FormEvent $event)
    {
        $form = $event->getForm();
        $formName = $this->_getSessionName($form);

        if ($this->_isResetClicked($event)) {
            // ** Unset du flag comme filtré
            $this->session->set($this->_getHasFilterSessionName($form), false);
            // ** Suppression de la session pour le formulaire
            $this->session->remove($formName);
        } elseif ($this->_isFiltered($event)) {
            // ** Set du flag comme filtré
            $this->session->set($this->_getHasFilterSessionName($form), true);
        }
    }

    /**
     * Donne le nom du flag session pour savoir si le formulaire est filtré
     * @param FormInterface $form
     * @return string
     */
    public static function _getHasFilterSessionName(FormInterface $form): string
    {
        return "form_{$form->getConfig()->getName()}_hasFilter";
    }

    /**
     * Vérifie si la configuration du champ est du type cherché
     * eg: EntityType, CheckBoxType, ...
     * @param FormConfigInterface $config
     * @param string $type
     * @return bool
     */
    protected function _fieldHasType(FormConfigInterface $config, string $type): bool
    {
        $match = false;
        $innerType = $config->getType()->getInnerType();

        if (get_class($innerType) === $type) {
            $match = true;
        } else if ($innerType->getParent() === $type) {
            $match = true;
        }

        return $match;
    }

    protected function _isResetClicked(FormEvent $event): bool
    {
        $form = $event->getForm();
        return $form->get('reset') instanceof SubmitButton && $form->get('reset')->isClicked();
    }

    protected function _isFiltered(FormEvent $event): bool
    {
        $form = $event->getForm();
        return
            (
                (
                    $form->get('filter_data') instanceof SubmitButton
                    &&
                    $form->get('filter_data')->isClicked()
                )
                ||
                $form->isSubmitted()
            )
            &&
            $this->_isResetClicked($event) === false
            ;
    }

    /**
     * Donne le nom de session pour le formulaire en cours
     * @param FormInterface $form
     * @return string
     */
    private function _getSessionName(FormInterface $form): string
    {
        return 'form_filter_'.$form->getConfig()->getName();
    }
}
