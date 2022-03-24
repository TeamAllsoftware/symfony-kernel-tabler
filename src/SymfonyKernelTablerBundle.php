<?php

namespace Allsofware\SymfonyKernelTabler;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymfonyKernelTablerBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
