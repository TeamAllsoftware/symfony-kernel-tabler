<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EntityCosuTrait
{
    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    public bool $cosu;

    /**
     * EntityCosuTrait constructor.
     */
    public function __construct()
    {
        $this->cosu = false;
    }

    /**
     * @return bool
     */
    public function isCosu(): bool
    {
        return $this->cosu;
    }

    /**
     * @param bool $cosu
     */
    public function setCosu(bool $cosu): void
    {
        $this->cosu = $cosu;
    }
}
