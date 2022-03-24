<?php

namespace Allsofware\SymfonyKernelTabler\Command\User;

use Allsofware\SymfonyKernelTabler\Command\BaseCommand;
use Allsofware\SymfonyKernelTabler\Repository\UserRepository;
use Allsofware\SymfonyKernelTabler\Service\UserService;
use Allsofware\SymfonyKernelTabler\Utils\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command that change user password.
 *
 *     $ php bin/console app:user:change-password
 */
class ChangePasswordUserCommand extends BaseCommand
{
    protected static $defaultName = 'app:user:change-password';

    public function __construct(
        private UserValidator $validator,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserService $userService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Change existing user password')
//            ->setHelp($this->getCommandHelp())
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('Change User password Command Interactive Wizard');

        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if (null !== $username) {
            $this->io->text(' > <info>Username</info>: '.$username);
        } else {
            $username = $this->io->ask('Username', null, [$this->validator, 'validateUsername']);
            $input->setArgument('username', $username);
        }

        $password = $this->io->askHidden('Password (your type will be hidden)', [$this->validator, 'validatePassword']);
        $input->setArgument('password', $password);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $this->validator->validateUsername($input->getArgument('username'));
        $plainPassword = $input->getArgument('password');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (null === $user) {
            throw new RuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        $this->userService->hashPassword($user, $plainPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('User "%s" (ID: %d, email: %s) password was successfully updated.', $user->getUsername(), $user->getId(), $user->getEmail()));

        return Command::SUCCESS;
    }
}
