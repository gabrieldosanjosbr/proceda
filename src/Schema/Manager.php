<?php

namespace Proceda\Schema;

use Proceda\Charset;
use Proceda\Evento;
use Proceda\Imposto\Tributo;
use Proceda\Schema;
use Proceda\Service\TypeResolver;
use Proceda\Type;

final class Manager
{
    private array $tree;
    private Schema $root;

    public function __construct(
        iterable                   $definitions,
        protected LayoutVersion    $layoutVersion,
        protected Charset          $charset,
        protected RegistroResolver $registroResolver,
        protected TypeResolver     $typeResolver,
    ) {
        $this->tree = (new Compiler())->compile(
            $definitions,
        );

        $this->root = $this->createSchema($this->tree);
    }

    public function getRootSchema(): Schema
    {
        return $this->root;
    }

    public function findContextByRegistro(
        Registro $registro,
        Schema $schema,
        ?Schema $parent = null,
    ): ?array {
        if (
            $schema->registro->identificacao === $registro->identificacao
            || (
                $registro->useParentAsContext
                && $schema->registro->identificacao === $registro->parent
            )
        ) {
            return [
                'schema' => $schema,
                'parent' => $parent,
            ];
        }

        foreach ($schema->children as $innerSchema) {
            $result = $this->findContextByRegistro(
                $registro,
                $innerSchema,
                $schema,
            );

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    public function getRegistroResolver(): RegistroResolver
    {
        return $this->registroResolver;
    }

    public function findSchemaByRegistro(
        Registro $registro,
        bool     $includeParentContext = true,
    ): ?Schema {
        return $this->collectSchemaByRegistro(
            $this->root,
            $registro,
            $includeParentContext,
        );
    }

    public function getGraph(): array
    {
        $graph = [];

        $this->collectGraph(
            $this->root,
            $graph,
        );

        return $graph;
    }

    public function findByPath(
        string $path,
    ): ?Schema {
        return $this->doFindByPath(
            $this->root,
            $path,
        );
    }

    private function doFindByPath(
        Schema $schema,
        string $path,
    ): ?Schema {
        if ($schema->path === $path) {
            return $schema;
        }

        foreach ($schema->children as $childSchema) {
            $found = $this->doFindByPath(
                $childSchema,
                $path,
            );

            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    private function collectSchemaByRegistro(
        Schema   $schema,
        Registro $registro,
        bool     $includeParentContext = true,
    ): ?Schema {
        $fields = [];
        $children = [];

        foreach ($schema->fields as $property => $field) {
            if (! $this->matchesRegistro(
                $registro,
                $field,
                $includeParentContext,
            )) {
                continue;
            }

            $fields[$property] = $field;
        }

        foreach ($schema->children as $childSchema) {
            $child = $this->collectSchemaByRegistro(
                $childSchema,
                $registro,
                $includeParentContext,
            );

            if ($child === null) {
                continue;
            }

            $children[] = $child;
        }

        if ($fields === [] && $children === []) {
            return null;
        }

        return $schema->with(
            fields: $fields,
            children: $children,
        );
    }

    private function createSchema(
        array $definition,
        ?string $property = null,
        ?Registro $registro = null,
    ): Schema {
        $context  = $definition['context'];
        $property = $context['property'] ?? $property;
        $registro = isset($context['registro'])
            ? $this->registroResolver->get($context['registro'])
            : $registro;

        [$fields, $children] = $this->createFieldsAndChildren(
            definitions: $definition['fields'] ?? [],
            registro: $registro,
            path: $context['path']
        );

        $this->sortChildren($children);

        return new Schema(
            class: $context['class'],
            path: $context['path'],
            label: $context['label'],
            property: $property,
            collection: $context['collection'] ?? false,
            registro: $registro,
            required: $context['required'] ?? false,
            occurs: $context['occurs'],
            inline: $context['inline'],
            overflow: $context['overflow'] ?? false,
            span: $context['span'] ?? 0,
            type: $this->resolveTypeForSchema($context['class'], $property),
            fields: $fields,
            children: $children,
        );
    }

    private function createFieldsAndChildren(
        array $definitions,
        ?Registro $registro,
        string $path,
    ): array {
        $fields = [];
        $children = [];

        foreach ($definitions as $property => $definition) {
            if (isset($definition['context'])) {
                $children[$property] = $this->createSchema(
                    $definition,
                    $property,
                    $registro,
                );

                continue;
            }

            $registro = isset($definition['registro'])
                ? $this->registroResolver->get($definition['registro'])
                : $registro;

            if ($registro === null) {
                throw new \LogicException(
                    sprintf('Field "%s" has no registro defined.', $property),
                );
            }

            $fields[$property] = $this->createField(
                $definition,
                $property,
                $registro,
                $path
            );
        }

        return [$fields, $children];
    }

    private function matchesRegistro(
        Registro $registro,
        Field $field,
        bool $includeParentContext,
    ): bool {
        // O registro é dono do campo.
        if ($field->registro->identificacao === $registro->identificacao) {
            return true;
        }

        if (!$includeParentContext) {
            return false;
        }

        // O campo pertence a um registro que compartilha
        // o contexto do registro atual.
        if (
            $field->registro->useParentAsContext
            && $field->registro->parent === $registro->identificacao
        ) {
            return true;
        }

        // Campo com origem alternativa.
        return $field->fallback?->registro->identificacao
            === $registro->identificacao;
    }

    public function collectGraph(
        Schema $schema,
        array &$graph,
    ): void {
        $graph['nodes'][$schema->path] = [
            'id' => $schema->path,
            'label' => $schema->label,
        ];

        foreach ($schema->children as $child) {
            $graph['edges'][] = [
                'from' => $schema->path,
                'to' => $child->path,
                'label' => $child->label,
            ];

            $this->collectGraph($child, $graph);
        }
    }

    private function createField(
        array $field,
        string $property,
        Registro $registro,
        string $path,
    ): Field {
        return new Field(
            charset: $this->charset,
            layoutVersion: $this->layoutVersion,
            registro: $registro,
            property: $property,
            path: $path,
            label: $field['label'],
            required: $field['required'] ?? false,
            decimals: $field['decimals'] ?? null,
            type: $field['type'] ?? null,
            allowed: $field['allowed'] ?? null,
            excluded: $field['excluded'] ?? [],
            length: $field['length'] ?? 0,
            offset: $field['offset'] ?? 0,
            fallback: isset($field['fallback'])
                ? new Field(
                    charset: $this->charset,
                    layoutVersion: $this->layoutVersion,
                    registro: $this->registroResolver->get(
                        $field['fallback']['registro']
                        ?? throw new \LogicException(
                            sprintf(
                                'Fallback for Scalar "%s" has no registro defined.',
                                $property,
                            ),
                        ),
                    ),
                    property: $property,
                    path: $path,
                    label: $field['label'],
                    required: $field['fallback']['required'] ?? false,
                    decimals: $field['fallback']['decimals'] ?? null,
                    type: $field['fallback']['type'] ?? null,
                    allowed: $field['fallback']['allowed'] ?? null,
                    excluded: $field['fallback']['excluded'] ?? [],
                    length: $field['fallback']['length'] ?? 0,
                    offset: $field['fallback']['offset'] ?? 0,
                )
                : null,
        );
    }

    private function resolveTypeForSchema(
        string $class,
        ?string $property,
    ): ?Type {
        if (!$property) {
            return null;
        }

        return match ($class) {
            Evento::class => $this->typeResolver->resolve(
                'Evento/Tipo/' . ucfirst($property),
            ),

            Tributo::class => $this->typeResolver->resolve(
                'Imposto/Tributo/Tipo/' . ucfirst($property),
            ),

            default => null,
        };
    }


    private function sortChildren(array &$children): void
    {
        $order = [];

        foreach (
            array_values($this->registroResolver->getRegistros()) as $position => $registro
        ) {
            $order[$registro->identificacao] = $position;
        }

        uksort(
            $children,
            static fn(string $a, string $b): int =>
                ($order[(string) $children[$a]->registro->identificacao] ?? PHP_INT_MAX)
                <=>
                ($order[(string) $children[$b]->registro->identificacao] ?? PHP_INT_MAX),
        );
    }
}
