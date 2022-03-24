<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EntityRolesTrait
{
    /**
     * @ORM\Column(type="json")
     */
    protected array $roles;

    /**
     * @param array $roles
     */
    public function __construct(array $roles = [])
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param string $role
     * @return void
     */
    public function addRole(string $role): void
    {
        if ($this->hasRole($role) === false) $this->roles[] = $role;
    }

    /**
     * @param string $role
     * @return void
     */
    public function removeRole(string $role): void
    {
        if ($this->hasRole($role) === true) {
            if (($key = array_search($role, $this->roles)) !== false) {
                unset($this->roles[$key]);
            }
        }
    }
}
