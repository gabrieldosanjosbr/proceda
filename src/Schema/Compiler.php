<?php

namespace Proceda\Schema;

class Compiler
{
    private const array METADATA = [
        'registro' => null,
        'class' => null,
        'path' => null,
        'property' => null,
        'type' => null,
        'required' => false,
        'occurs' => 1,
        'inline' => 1,
        'overflow' => false,
        'collection' => false,
        'label' => null,
    ];

    private array $definitions = [];
    private array $tree = [];

    public function compile(iterable $definitions): array
    {
        foreach ($definitions as $definition) {
            $this->mergeDefinition($definition);
        }

        $this->buildTree();

        return array_shift($this->tree);
    }

    public function getDefinition(string $path): array
    {
        if (!isset($this->definitions[$path])) {
            throw new \LogicException(
                sprintf('Definition "%s" not found.', $path),
            );
        }

        return $this->definitions[$path];
    }

    private function mergeDefinition(array $definition): void
    {
        $normalized = $this->normalizeDefinition($definition);
        $context = $normalized['context'];

        $this->definitions[$context['path']] = array_replace_recursive(
            $this->definitions[$context['path']] ?? [],
            $normalized,
        );
    }

    private function normalizeDefinition(array $definition): array
    {
        $context = $this->normalizeContext($definition);

        $fields = $this->normalizeFields(
            fields: $this->extractFields($definition),
            context: $context,
            registro: $context['registro'],
        );

        if ($context['inline'] > 1) {
            $context['span'] = $this->computeSpan($fields);
        }

        return [
            'context' => $context,
            'fields' => $fields,
        ];
    }

    private function buildTree(): void
    {
        foreach ($this->definitions as $path => $definition) {
            if (str_contains($path, '/')) {
                continue;
            }

            $this->tree[$path] = $this->expandDefinition($definition);
        }
    }

    private function expandDefinition(
        array $definition,
        array $visited = [],
    ): array {
        $path = $definition['context']['path'];
        $visited[$path] = true;

        $segments = explode('/', $path);
        $current = end($segments);

        foreach ($this->definitions as $bucketPath => $bucket) {
            $bucketSegments = explode('/', $bucketPath);

            if (
                count($bucketSegments) < 2
                || $bucketSegments[0] !== $current
            ) {
                continue;
            }

            $bucket = $this->expandDefinition(
                $bucket,
                $visited,
            );

            $target = &$definition;

            foreach (array_slice($bucketSegments, 1) as $segment) {
                $property = $this->property($segment);

                $target['fields'][$property] ??= [];
                $target = &$target['fields'][$property];
            }

            $target = array_replace_recursive(
                $target,
                $bucket,
            );

            unset($target);
        }

        return $definition;
    }

    private function normalizeFields(
        array $fields,
        array $context,
        ?string $registro = null,
    ): array {
        $normalized = [];

        foreach ($fields as $property => $field) {
            if ($this->isField($field)) {
                $normalized[$property] = $this->normalizeField($field);
                continue;
            }

            $fieldContext = $this->normalizeContext(
                source: $field,
                registro: $registro,
            );

            $fieldContext['path'] = sprintf(
                '%s/%s',
                $context['path'],
                $this->dasherize($property),
            );

            $normalizedFields = $this->normalizeFields(
                fields: $this->extractFields($field),
                context: $fieldContext,
                registro: $fieldContext['registro'],
            );

            if ($fieldContext['inline'] > 1) {
                $fieldContext['span'] = $this->computeSpan(
                    $normalizedFields,
                );
            }

            $normalized[$property] = [
                'context' => $fieldContext,
                'fields' => $normalizedFields,
            ];
        }

        return $normalized;
    }

    private function normalizeContext(
        array $source,
        ?string $registro = null,
    ): array {
        $context = array_replace(
            self::METADATA,
            array_intersect_key(
                $source,
                self::METADATA,
            ),
        );

        $context['registro'] ??= $registro;

        $context['class'] = $source['class'];

        $context['path'] = $this->normalizePath(
            $source['class'],
            $source['path'] ?? null,
        );

        return $context;
    }

    private function normalizeField(array $field): array
    {
        if (
            isset($field['allowed'])
            && is_string($field['allowed'])
        ) {
            $field['allowed'] = $this->normalizeAllowed(
                $field['allowed'],
            );
        }

        return $field;
    }

    private function normalizeAllowed(string $allowed): array
    {
        if (!preg_match(
            '/^(-?\d+)\.\.(-?\d+)$/',
            $allowed,
            $matches,
        )) {
            throw new \LogicException(
                'Invalid value for field allowed.',
            );
        }

        return range(
            (int) $matches[1],
            (int) $matches[2],
        );
    }

    private function isField(array $definition): bool
    {
        return array_key_exists('length', $definition);
    }

    private function extractFields(array $definition): array
    {
        return array_diff_key(
            $definition,
            self::METADATA,
        );
    }

    private function computeSpan(array $fields): int
    {
        $span = 0;

        foreach ($fields as $field) {
            if ($this->isField($field)) {
                $span += $field['length'] ?? 0;
                continue;
            }

            $span += $this->computeSpan(
                $field['fields'] ?? [],
            );
        }

        return $span;
    }

    private function normalizePath(
        string $class,
        ?string $path,
    ): string {
        if ($path !== null) {
            return trim($path, '/');
        }

        return $this->dasherize(
            $this->basename($class),
        );
    }

    private function dasherize(string $value): string
    {
        return strtolower(
            preg_replace(
                '/(?<!^)[A-Z]/',
                '-$0',
                $value,
            ),
        );
    }

    private function property(string $value): string
    {
        return lcfirst(
            str_replace(
                '-',
                '',
                ucwords($value, '-'),
            ),
        );
    }

    private function basename(string $class): string
    {
        return substr(
            strrchr($class, '\\'),
            1,
        );
    }
}
