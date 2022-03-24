<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait EntityUuidTrait
{
    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    protected UuidInterface $uuid;

    /**
     * EntityUuidTrait constructor.
     */
    public function __construct()
    {
        $this->generateUuid();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function generateUuid(): void
    {
        $this->uuid = Uuid::uuid1();
    }
}
