<?php

namespace Proceda\Collection;

use Proceda\Type;

/**
 * @implements \IteratorAggregate<string, Type>
 */
final class TypeCollection implements \IteratorAggregate, \Countable
{
    /** @var array<string, Type> */
    protected array $types = [];

    /**
     * @param Type[] $types
     */
    public function __construct(
        public readonly string $name,
        array $types = [],
    ) {
        foreach ($types as $type) {
            $this->add($type);
        }
    }

    /**
     * @return \Traversable<string, Type>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->types;
    }

    public function count(): int
    {
        return count($this->types);
    }

    public function add(Type $type): void
    {
        $this->types[$type->name] = $type;
    }

    public static function time(): self
    {
        return new self(
            name: 'time',
            types: [],
        );
    }

    public function with(
        string|array|null $allowed = null,
        array $excluded = [],
    ): self {
        return new self(
            name: $this->name,
            types: array_values(array_filter(
                $this->types,
                fn(Type $type) => $this->isAllowed(
                    value: $type->value,
                    allowed: $allowed,
                    excluded: $excluded,
                ),
            )),
        );
    }

    public function isEmpty(): bool
    {
        return empty($this->types);
    }

    public function has(string|int $value): bool
    {
        foreach ($this->types as $type) {
            if ($type->value === $value) {
                return true;
            }
        }

        return false;
    }

    public function get(string|int $value): ?Type
    {
        foreach ($this->types as $type) {
            if ($type->value == $value) {
                return $type;
            }
        }

        return null;
    }

    public function getByName(string $name): ?Type
    {
        foreach ($this->types as $type) {
            if ($type->name === $name) {
                return $type;
            }
        }

        return null;
    }

    private function isAllowed(
        string|int $value,
        ?array     $allowed = null,
        array      $excluded = [],
    ): bool {
        if (in_array($value, $excluded)) {
            return false;
        }

        if ($allowed === null) {
            return true;
        }

        return in_array($value, $allowed);
    }
}
