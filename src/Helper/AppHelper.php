<?php

namespace Allsoftware\SymfonyKernelTabler\Helper;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AppHelper
{
    private string $projectDir;

    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
        $this->projectDir = $this->parameterBag->get('kernel.project_dir');
    }

    #[ArrayShape(['path' => "string", "filename" => "string"])]
    public static function generateFileNameAndPath(?string $filename = null): array
    {
        if ($filename === null) $filename = Uuid::uuid1()->toString();
        elseif (strlen($filename) < 4) throw new \LogicException('$filename must be at least 4 char long !');

        $dir    = substr($filename, 0, 2);
        $subDir = substr($filename, 2, 2);

        return ['path' => $dir . DIRECTORY_SEPARATOR . $subDir, "filename" => $filename];
    }

    protected function getPublicDir(): string
    {
        return $this->projectDir . DIRECTORY_SEPARATOR . 'public';
    }

    protected function getVarDir(): string
    {
        return $this->projectDir . DIRECTORY_SEPARATOR . 'var';
    }

    #[Pure]
    protected function getAwsDir(): string
    {
        return $this->getVarDir() . DIRECTORY_SEPARATOR . 's3data';
    }
}
