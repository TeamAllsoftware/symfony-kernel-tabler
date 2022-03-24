<?php

namespace Allsoftware\SymfonyKernelTabler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class QuillMention {
    public function __construct(
        private string $name,
        private ?string $description = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
