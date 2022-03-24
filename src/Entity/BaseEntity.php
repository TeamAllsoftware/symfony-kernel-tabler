<?php

namespace App\Entity;

use App\Entity\Traits\EntityCosuTrait;
use App\Entity\Traits\EntityDataLifeTrait;
use App\Entity\Traits\EntityUuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @ORM\MappedSuperclass
 * @package App\Entity
 */
abstract class BaseEntity
{
    /**
     * BaseEntity constructor.
     */
    public function __construct()
    {
        $this->__uuidConstruct();
        $this->__dlConstruct();
        $this->__cosuConstruct();
    }

    // import UUID
    use EntityUuidTrait {
        EntityUuidTrait::__construct as private __uuidConstruct;
    }

    // import life data of the entity (createdAt, updatedAt, ...)
    use EntityDataLifeTrait {
        EntityDataLifeTrait::__construct as private __dlConstruct;
    }

    // import COSU
    use EntityCosuTrait {
        EntityCosuTrait::__construct as private __cosuConstruct;
    }

    public function setCosu(bool $cosu): void
    {
        if ($this->isCosu() === $cosu) return;

        if (!$this->cosu && $cosu){
            $this->setDeletedAt(new DateTime());
        } else {
            $this->setDeletedAt(null);
        }
        $this->cosu = $cosu;
    }

    public function isNew() : bool {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if ($propertyAccessor->isReadable($this, 'id')) {
            return $propertyAccessor->getValue($this, 'id') === null;
        }

        throw new \LogicException('Entity has no `id` to determine if isNew !');
    }
}
