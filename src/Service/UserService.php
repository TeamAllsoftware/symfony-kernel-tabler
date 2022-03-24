<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserValidator $validator,
        private UserRepository $userRepository,
    ) { }

    public function createUser(string $username, ?string $plainPassword, string $email, string $fullName, bool $isAdmin = false, bool $auto_flush = true) : User
    {
        // Vérifie si les données renseignées sont correctes ou non
        $this->_validateUserData($username, $plainPassword, $email, $fullName);

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user === null) {
            $user = (new User())
                ->setFullName($fullName)
                ->setUsername($username)
                ->setEmail($email)
            ;

            $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

            $user->setToken(md5(uniqid('user-')));

            if ($plainPassword) {
                $this->hashPassword($user, $plainPassword);
            }

            $this->entityManager->persist($user);
            if ($auto_flush) $this->entityManager->flush();
        }

        return $user;
    }

    public function deleteUser(User $user, bool $auto_flush = true)
    {
        $user->setInterlocuteur(null);
        $this->entityManager->remove($user);
        if ($auto_flush) $this->entityManager->flush();
    }

    /**
     * See: https://symfony.com/doc/5.4/security.html#registering-the-user-hashing-passwords
     */
    public function hashPassword(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }





    private function _validateUserData(string $username, ?string $plainPassword, string $email, string $fullName): void
    {
        // first check if a user with the same username already exists.
        $existingUser = $this->userRepository->findOneBy(['username' => $username]);

        if (null !== $existingUser) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" username.', $username));
        }

        // validate password and email if is not this input means interactive.
        if($plainPassword) $this->validator->validatePassword($plainPassword);
        $this->validator->validateEmail($email);
        $this->validator->validateFullName($fullName);

        // check if a user with the same email already exists.
        $existingEmail = $this->userRepository->findOneBy(['email' => $email]);

        if (null !== $existingEmail) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }
    }
}
