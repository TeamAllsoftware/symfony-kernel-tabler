<?php

namespace Allsoftware\SymfonyKernelTabler\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    protected function _checkCosu($entity){
        $prop  = 'cosu';
        if (property_exists($entity, $prop) && $entity->{$prop} === true) throw new \LogicException(get_class($entity) . ' - Cannot be used when Cosu !');
    }
}
