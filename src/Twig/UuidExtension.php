<?php

namespace Allsoftware\SymfonyKernelTabler\Twig;
use Allsoftware\SymfonyKernelTabler\Extensions\Doctrine\UuidEncoder;
use Ramsey\Uuid\UuidInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UuidExtension extends AbstractExtension
{
    public function __construct(
        private UuidEncoder $encoder
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'uuid_encode',
                [$this, 'encodeUuid'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function encodeUuid(UuidInterface $uuid): string
    {
        return $this->encoder->encode($uuid);
    }
}
