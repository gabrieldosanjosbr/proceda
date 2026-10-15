<?php

namespace Proceda\Schema;

final class Registro
{
    /** @var Registro[] */
    private array $extensions = [];

    public function __construct(
        public readonly string $identificacao,
        public readonly int $length,
        public readonly ?string $parent = null,
        public readonly bool $useParentAsContext = false,
        public readonly ?string $extended = null,
        public readonly bool $deferred = false,
        public readonly bool $build = false,
        public readonly bool $required = false,
    ) {}

    public function addExtension(Registro $registro): void
    {
        $this->extensions[] = $registro;
    }

    /** @return Registro[] */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
