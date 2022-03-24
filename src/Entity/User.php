<?php

namespace Allsoftware\SymfonyKernelTabler\Entity;

use Allsoftware\SymfonyKernelTabler\Attribute\QuillMention;
use Allsoftware\SymfonyKernelTabler\Entity\Traits\EntityRolesTrait;
use Doctrine\ORM\Mapping as ORM;
use Allsoftware\SymfonyKernelTabler\Model\UserInterface as TablerUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    const CST_Role_SuperAdmin   = "ROLE_SUPER_ADMIN";
    const CST_Role_Admin        = "ROLE_ADMIN";
    const CST_Role_User         = "ROLE_USER";

    const CST_Role_APRC         = "ROLE_APRC";
    const CST_Role_Interlocutor = "ROLE_INTERLOCUTOR";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $fullName = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $token = null;

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function __construct()
    {
        parent::__construct();

        // guarantees that a user always has at least one role for security
        $this->__rolesConstruct(['ROLE_USER']);
    }

    // import Roles
    use EntityRolesTrait {
        EntityRolesTrait::__construct as private __rolesConstruct;
    }







    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return User
     */
    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return User
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return User
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }


    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // We're using bcrypt in security.yaml to encode the password, so
        // the salt value is built-in and you don't have to generate one
        // See https://en.wikipedia.org/wiki/Bcrypt

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    public function __serialize(): array
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return [$this->id, $this->username, $this->password];
    }

    public function __unserialize(array $data): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = $data;
    }

    public function getIdentifier(): string
    {
        return $this->getId();
    }

    public function getName(): string
    {
        return $this->getFullName();
    }

    public function getTitle(): ?string
    {
        // TODO: Implement getTitle() method.
        return null;
    }

    public function getAvatar(): ?string
    {
        // TODO: Implement getAvatar() method.
        return null;
    }
}
