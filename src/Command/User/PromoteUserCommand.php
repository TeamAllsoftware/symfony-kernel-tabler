<?php

namespace Allsofware\SymfonyKernelTabler\Command\User;

use Allsofware\SymfonyKernelTabler\Command\BaseCommand;
use Allsofware\SymfonyKernelTabler\Repository\UserRepository;
use Allsofware\SymfonyKernelTabler\Utils\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command that promote user.
 *
 *     $ php bin/console app:user:promote
 */
class PromoteUserCommand extends BaseCommand
{
    protected static $defaultName = 'app:user:promote';

    public function __construct(
        private UserValidator $validator,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Promote existing user')
//            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('role', InputArgument::OPTIONAL, 'The new role of the user')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('Promote User Command Interactive Wizard');

        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if (null !== $username) {
            $this->io->text(' > <info>Username</info>: '.$username);
        } else {
            $username = $this->io->ask('Username', null, [$this->validator, 'validateUsername']);
            $input->setArgument('username', $username);
        }

        // Ask for the role if it's not defined
        $role = $input->getArgument('role');
        if (null !== $role) {
            $this->io->text(' > <info>Role</info>: '.$role);
        } else {
            $role = $this->io->ask('Role');
            $input->setArgument('role', $role);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $this->validator->validateUsername($input->getArgument('username'));
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (null === $user) {
            throw new RuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        $role = $input->getArgument('role');

        if ($user->hasRole($role) === false) {
            $user->addRole($role);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->io->success(
                sprintf(
                    'User "%s" (ID: %d, email: %s) role was successfully added.',
                    $user->getUsername(),
                    $user->getId(),
                    $user->getEmail()
                )
            );
        } else {
            $this->io->info(
                sprintf(
                    'User "%s" (ID: %d, email: %s) has already the role "%s".',
                    $user->getUsername(),
                    $user->getId(),
                    $user->getEmail(),
                    $role
                )
            );
        }

        return Command::SUCCESS;
    }
}
