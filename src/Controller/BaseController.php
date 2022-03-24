<?php

namespace Allsoftware\SymfonyKernelTabler\Controller;

use Allsoftware\SymfonyKernelTabler\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
    ){}

    /**
     * @return User
     */
    public function getUser(): User
    {
        /** @var User $user */
        $user = parent::getUser();

        return $user;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}
