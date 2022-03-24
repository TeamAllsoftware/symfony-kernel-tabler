<?php

namespace Allsoftware\SymfonyKernelTabler\Interface;

use Allsoftware\SymfonyKernelTabler\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

interface RepositoryFilterInterface {

    public function filterQb(?array $filter_data = []): QueryBuilder;

    public function filter(int $page = 1, ?array $filter_data = []): Paginator;

}
