<?php

namespace Proceda\Service;

use Proceda\Infrastructure\YamlSource;
use Proceda\Infrastructure\ProfilePath;

final readonly class PropertyResolver
{
    private function __construct(
        private array $property,
    ) {}

    public static function fromArray(array $property): self
    {
        return new self($property);
    }

    public static function fromDefinitions(
        iterable $definitions,
    ): self {
        $properties = [];

        foreach ($definitions as $definition) {
            $properties = array_replace_recursive(
                $properties,
                $definition,
            );
        }

        return self::fromArray($properties);
    }

    public static function yaml(
        ?string $profile = null,
    ): self {
        $profilePath = ProfilePath::root($profile);

        $definitions = YamlSource::fromPath(
            path: dirname(__DIR__, 2) . '/layout/property.yaml',
            overridePath: $profilePath === null
                ? null
                : $profilePath . '/property.yaml',
        )->getDefinitions();

        return self::fromDefinitions($definitions);
    }

    public function collection(string $property): string
    {
        return $this->property['collection'][$property]
            ?? $property;
    }
}