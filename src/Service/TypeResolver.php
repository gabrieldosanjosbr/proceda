<?php

namespace Proceda\Service;

use Proceda\Collection\TypeCollection;
use Proceda\Infrastructure\YamlSource;
use Proceda\Infrastructure\ProfilePath;
use Proceda\Type;

final readonly class TypeResolver
{
    /**
     * @param array<string, TypeCollection> $typeCollection
     */
    private function __construct(
        private array $typeCollection,
    ) {}

    public static function yaml(
        ?string $profile = null,
    ): self {
        $profilePath = ProfilePath::root($profile);

        $source = YamlSource::fromPath(
            path: dirname(__DIR__, 2) . '/layout/type',
            overridePath: $profilePath === null
                ? null
                : $profilePath . '/type',
        );

        return self::fromDefinitions(
            $source->getDefinitions(),
        );
    }

    /**
     * @param iterable<array<string, array>> $definitions
     */
    public static function fromDefinitions(
        iterable $definitions,
    ): self {
        $types = [];

        foreach ($definitions as $definitionFile) {
            foreach ($definitionFile as $name => $values) {
                $collection = $types[$name]
                    ??= new TypeCollection(name: $name);

                foreach ($values as $data) {
                    $collection->add(
                        new Type(
                            name: $data['name'],
                            value: $data['value'],
                            label: $data['label'],
                        ),
                    );
                }
            }
        }

        return new self($types);
    }

    public function resolve(string $path): Type
    {
        [$collection, $name] = self::split($path);

        $types = $this->getTypeCollection($collection);
        $type = $types->getByName($name);

        if ($type === null) {
            throw new \LogicException(
                sprintf(
                    'Type "%s" not found.',
                    $path,
                ),
            );
        }

        return $type;
    }

    public function getTypeCollection(
        string $name,
        string|array|null $allowed = null,
        array $excluded = [],
    ): TypeCollection {
        if ($name === 'time') {
            return TypeCollection::time();
        }

        $typeCollection = $this->typeCollection[$name]
            ?? throw new \LogicException(
                sprintf(
                    'TypeCollection "%s" not found.',
                    $name,
                ),
            );

        return $typeCollection->with(
            allowed: $allowed,
            excluded: $excluded,
        );
    }

    private static function split(string $path): array
    {
        $position = strrpos($path, '/');

        if ($position === false) {
            throw new \LogicException(
                sprintf('Invalid type path "%s".', $path),
            );
        }

        return [
            substr($path, 0, $position),
            substr($path, $position + 1),
        ];
    }
}
