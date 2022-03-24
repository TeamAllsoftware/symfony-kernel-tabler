<?php

namespace Allsoftware\SymfonyKernelTabler\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
        private Security $security,
    ) { }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    protected function getSecurity(): Security
    {
        return $this->security;
    }

    protected function getUserConnected(): ?UserInterface
    {
        return $this->security->getUser();
    }
}
