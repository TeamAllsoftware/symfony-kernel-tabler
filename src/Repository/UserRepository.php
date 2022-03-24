<?php

namespace App\Repository;

use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function filter(int $page = 1): Paginator
    {
        $qb = $this
            ->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :roles')
            ->setParameter('roles', '%'.User::CST_Role_APRC.'%')
            ->andWhere('user.cosu = false')
        ;

        return (new Paginator($qb))->paginate($page);
    }

    public function findGestionnaires(bool $only_qb = false): QueryBuilder
    {
        $qb = $this
            ->createQueryBuilder('user')
            ->andWhere('user.roles LIKE :role')
            ->setParameter('role', '%'.User::CST_Role_APRC.'%')
            ->andWhere('user.cosu = false')
            ;

        return $only_qb ? $qb : $qb->getQuery()->getResult();
    }
}
