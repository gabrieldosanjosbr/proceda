<?php

namespace Proceda;

use Proceda\Builder\BuilderInterface;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Error\Validation;
use Proceda\Schema\Manager;
use Proceda\Schema\Registro;
use Proceda\Service\BuilderRegistry;
use Proceda\Service\TypeResolver;

final class Reader
{
    private Line $line;

    /** @var Line[] */
    private array $lines = [];

    public function __construct(
        private readonly Manager         $schemaManager,
        private readonly BuilderRegistry $builderRegistry,
        private readonly TypeResolver    $typeResolver,
        private readonly Validation      $validation = new Validation(),
    ) {}

    /**
     * @throws \ReflectionException
     */
    public function read(Line $line): void
    {
        $this->line = $line;
        $this->lines[] = $line;

        $schema = $this->schemaManager->findSchemaByRegistro(
            $line->registro,
        );

        if ($schema !== null) {
            $this->parseSchema($schema);
        }

        if (!$line->registro->build) {
            return;
        }

        $context = $this->schemaManager->findContextByRegistro(
            $line->registro,
            $this->schemaManager->getRootSchema(),
        );

        if ($context === null) {
            throw new \LogicException(
                sprintf(
                    'Context not found for registro %s',
                    $line->registro->identificacao,
                ),
            );
        }

        $this->buildSchema(
            $context['schema'],
            $context['parent'],
        );
    }

    public function getResult(): Result
    {
        $intercambio = $this->builderRegistry
            ->getRootBuilder()
            ->build();

        return new Result(
            intercambio: $intercambio,
            validation: $this->validation,
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function parseSchema(
        Schema $schema,
        int    $offsetAdjustment = 0,
        bool   $expandInline = true,
    ): ?BuilderInterface {
        if ($expandInline && $schema->isInline()) {
            $this->parseInline(
                $schema,
                $offsetAdjustment,
            );

            return null;
        }

        $builder = $this->builderRegistry->get(
            $schema,
        );

        foreach ($schema->fields as $field) {
            $reflectionProperty = new \ReflectionProperty(
                $builder,
                $field->property,
            );

            $match = $field->matchesRegistro(
                $field->registro,
                $this->line->registro,
            );

            if ($match === false) {
                continue;
            }

            $value = $field->getValue(
                line: $this->line,
                propertyReflection: $reflectionProperty,
                typeResolver: $this->typeResolver,
                validation: $this->validation,
                offsetAdjustment: $offsetAdjustment,
            );

            $currentValue = $reflectionProperty->getValue($builder);

            if (
                $currentValue === null
                && $field->fallback
                && $this->line->registro->identificacao !== $field->registro->identificacao
            ) {
                $value = $field->fallback->getValue(
                    $this->line,
                    $reflectionProperty,
                    $this->typeResolver,
                    $this->validation,
                    $offsetAdjustment,
                );
            }

            if (
                $value === null
                || ($currentValue !== null && $currentValue !== 0)
            ) {
                continue;
            }

            $builder->setFilled(true);

            $reflectionProperty->setValue($builder, $value);
        }

        foreach ($schema->children as $child) {
            $this->parseSchema(
                schema: $child,
                offsetAdjustment: $offsetAdjustment,
                expandInline: $expandInline,
            );

            if (!$schema->isInline()) {
                continue;
            }

            $this->buildSchema($child, $schema);
        }

        return $builder;
    }

    /**
     * @throws \ReflectionException
     */
    private function parseInline(
        Schema $schema,
        int $offsetAdjustment,
    ): void {
        $builders = [];

        for ($i = 0; $i < $schema->inline; $i++) {
            $builder = $this->parseSchema(
                schema: $schema,
                offsetAdjustment: $offsetAdjustment + ($i * $schema->span),
                expandInline: false,
            );

            $this->builderRegistry->remove($schema);

            if (!$builder->isFilled()) {
                continue;
            }

            $builders[] = $builder;
        }

        $this->builderRegistry->prependCollection(
            $schema,
            $builders,
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function buildSchema(
        Schema $schema,
        ?Schema $parentSchema = null,
    ): void {
        $builder = $this->builderRegistry->get($schema);

        foreach ($schema->children as $childSchema) {
            $childBuilder = $this->builderRegistry->find($childSchema);

            if ($childBuilder !== null) {
                $this->buildSchema(
                    $childSchema,
                    $schema,
                );
            }

            if (!$childSchema->isCollection()) {
                continue;
            }

            $this->attachCollection(
                $childSchema,
                $builder,
            );

            $this->builderRegistry->removeCollection(
                $childSchema,
            );
        }

        $parentBuilder = $parentSchema
            ? $this->builderRegistry->get($parentSchema)
            : null;

        if ($parentBuilder === null) {
            return;
        }

        $this->builderRegistry->remove($schema);

        if (!$builder->isFilled() && !$schema->required) {
            return;
        }

        if ($schema->isCollection()) {
            $this->builderRegistry->prependCollection(
                $schema,
                [$builder],
            );

            return;
        }

        if (!property_exists($parentBuilder, $schema->property)) {
            throw new \LogicException(sprintf(
                'Unable to attach schema "%s" to builder "%s". Expected property "%s".',
                $schema->path,
                $parentBuilder::class,
                $schema->property,
            ));
        }

        $built = $builder->build();

        $parentBuilder->setFilled(true);

        $property = new \ReflectionProperty(
            $parentBuilder,
            $schema->property,
        );

        $property->setValue(
            $parentBuilder,
            $built,
        );
    }

    /**
     * @throws \ReflectionException
     */
    private function attachCollection(
        Schema $schema,
        BuilderInterface $parentBuilder,
    ): void {
        $collection = $this->builderRegistry
            ->findCollection($schema);

        if ($collection === []) {
            return;
        }

        $method = sprintf(
            'add%s',
            ucfirst($schema->property),
        );

        if (!method_exists($parentBuilder, $method)) {
            $method = sprintf(
                'add%s',
                (new \ReflectionClass($schema->class))
                    ->getShortName(),
            );
        }

        if (!method_exists($parentBuilder, $method)) {
            throw new \LogicException(sprintf(
                'Unable to attach collection schema "%s" to builder "%s". Expected method "add%s" or method "%s".',
                $schema->path,
                $parentBuilder::class,
                ucfirst($schema->property),
                $method,
            ));
        }

        if (
            $schema->isInline() === false &&
            count($collection) > $schema->registro->occurs
        ) {
            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED,
                    schema: $schema,
                    registro: $schema->registro,
                    parameters: [
                        'maximum' => $schema->registro->occurs,
                        'actual' => count($collection),
                    ],
                )
            );
        }

        foreach ($collection as $builder) {
            $parentBuilder->{$method}(
                $builder->build()
            );
        }

        $parentBuilder->setFilled(true);
    }

    public function validateRequiredRegistros(): void
    {
        $positions = [];
        $reported  = [];
        $lines     = array_reverse($this->lines);
        $registros = $this->schemaManager
            ->getRegistroResolver()
            ->getRegistros();

        foreach ($lines as $index => $line) {
            $positions[$line->registro->identificacao][] = $index;
        }

        $this->validateRequiredParent(
            lines: $lines,
            positions: $positions,
            registros: $registros,
            reported: $reported,
        );

        $this->detectMissingParentRegistros(
            lines: $lines,
            registros: $registros,
            reported: $reported,
        );

        $this->validateRequiredChildren(
            lines: $lines,
            positions: $positions,
            registros: $registros,
            reported: $reported,
        );
    }

    private function validateRequiredChildren(
        array $lines,
        array $positions,
        array $registros,
        array &$reported,
    ): void {
        $requiredChildren = [];

        foreach ($registros as $registro) {
            $registros[$registro->identificacao] = $registro;

            if ($registro->parent === null || !$registro->required) {
                continue;
            }

            $requiredChildren[$registro->parent][] = $registro;
        }

        foreach ($lines as $index => $line) {
            $parent = $line->registro;

            $children = $requiredChildren[
                $parent->identificacao
            ] ?? [];

            if ($children === []) {
                continue;
            }

            $contextEnd = $this->findContextEnd(
                positions: $positions,
                registros: $registros,
                parent: $parent,
                index: $index,
                total: count($lines),
            );

            foreach ($children as $child) {
                if ($this->existsBetween(
                    positions: $positions,
                    registro: $child->identificacao,
                    start: $index + 1,
                    end: $contextEnd,
                )) {
                    continue;
                }

                $key = $child->identificacao . ':' . $index;

                if (isset($reported[$key])) {
                    continue;
                }

                $reported[$key] = true;

                $this->validation->add(
                    new LayoutError(
                        type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                        line: $line,
                        registro: $child,
                    )
                );
            }
        }
    }

    private function validateRequiredParent(
        array $lines,
        array $positions,
        array $registros,
        array &$reported,
    ): void {
        foreach ($lines as $index => $line) {
            $registro = $line->registro;

            if ($registro->parent === null) {
                continue;
            }

            $parent = $registros[$registro->parent] ?? null;

            if ($parent === null) {
                continue;
            }

            // Encontra o início do contexto em que
            // o registro atual deveria estar.
            $contextStart = $this->findContextStart(
                positions: $positions,
                registros: $registros,
                parent: $parent,
                index: $index,
            );

            // Procura o pai dentro do contexto atual.
            $parentPosition = $this->findPreviousBetween(
                positions: $positions,
                registro: $parent->identificacao,
                start: $contextStart,
                end: $index,
            );

            if ($parentPosition !== null) {
                continue;
            }

            if (!$parent->required) {
                continue;
            }

            $key = $parent->identificacao . ':' . $contextStart;

            if (isset($reported[$key])) {
                continue;
            }

            $reported[$key] = true;

            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                    line: $line,
                    registro: $parent,
                )
            );
        }
    }

    private function detectMissingParentRegistros(
        array $lines,
        array $registros,
        array &$reported,
    ): void {
        // Estado das ocorrências de cada pai ativo.
        $active = [];
        $parents = [];

        foreach ($registros as $registro) {
            if ($registro->parent !== null) {
                $parents[$registro->parent] = true;
            }
        }

        foreach ($lines as $index => $line) {
            $registro = $line->registro;
            $id = $registro->identificacao;

            // Identifica os ancestrais do registro atual.
            $ancestors = [];
            $parentId = $registro->parent;

            while ($parentId !== null && isset($registros[$parentId])) {
                $ancestors[$parentId] = true;
                $parentId = $registros[$parentId]->parent;
            }

            // Encerra contextos que não contêm a linha atual.
            foreach (array_keys($active) as $activeId) {
                if (!isset($ancestors[$activeId])) {
                    unset($active[$activeId]);
                }
            }

            // Valida o registro atual dentro do contexto do pai.
            $parentId = $registro->parent;

            if (
                $parentId !== null
                && isset($active[$parentId])
                && isset($parents[$parentId])
            ) {
                $state = &$active[$parentId];
                $count = ($state['counts'][$id] ?? 0) + 1;

                if ($count > $registro->occurs) {
                    $key = "sequence:$parentId:$index";

                    if (!isset($reported[$key])) {
                        $reported[$key] = true;

                        $this->validation->add(
                            new LayoutError(
                                type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                                line: $line,
                                registro: $this->schemaManager
                                    ->getRegistroResolver()
                                    ->get($parentId),
                            )
                        );
                    }

                    // Reinicia a contagem para o novo contexto lógico.
                    $state['counts'] = [];
                    $count = 1;
                }

                $state['counts'][$id] = $count;

                unset($state);
            }

            // Um registro físico inicia o seu próprio contexto.
            if (isset($parents[$id])) {
                $active[$id] = [
                    'counts' => [],
                ];
            }
        }
    }

    private function findContextStart(
        array $positions,
        array $registros,
        Registro $parent,
        int $index,
    ): int {
        $start = 0;
        $ancestors = [];

        $current = $parent;

        while ($current !== null) {
            $ancestors[$current->identificacao] = true;

            $current = $current->parent !== null
                ? ($registros[$current->parent] ?? null)
                : null;
        }

        // Limite estabelecido pelo ancestral imediato.
        if ($parent->parent !== null) {
            $position = $this->findPreviousBetween(
                positions: $positions,
                registro: $parent->parent,
                start: 0,
                end: $index,
            );

            $start = $position ?? 0;
        }

        // Identifica registros de fechamento dos contextos ancestrais.
        foreach ($registros as $registro) {
            if (
                !$registro->deferred
                || !$registro->required
                || $registro->parent === null
                || !isset($ancestors[$registro->parent])
            ) {
                continue;
            }

            $position = $this->findPreviousBetween(
                positions: $positions,
                registro: $registro->identificacao,
                start: $start,
                end: $index,
            );

            if ($position !== null) {
                $start = max($start, $position + 1);
            }
        }

        return $start;
    }

    private function findPreviousBetween(
        array $positions,
        string $registro,
        int $start,
        int $end,
    ): ?int {
        $previous = null;

        foreach ($positions[$registro] ?? [] as $position) {
            if ($position < $start) {
                continue;
            }

            if ($position >= $end) {
                break;
            }

            $previous = $position;
        }

        return $previous;
    }

    private function existsBetween(
        array $positions,
        string $registro,
        int $start,
        int $end,
    ): bool {
        foreach ($positions[$registro] ?? [] as $position) {
            if ($position >= $start && $position < $end) {
                return true;
            }
        }

        return false;
    }

    private function findContextEnd(
        array $positions,
        array $registros,
        Registro $parent,
        int $index,
        int $total,
    ): int {
        $end = $total;
        $current = $parent;

        while ($current !== null) {
            foreach (
                $positions[$current->identificacao] ?? [] as $position
            ) {
                if ($position <= $index) {
                    continue;
                }

                $end = min($end, $position);

                break;
            }

            $current = $current->parent !== null
                ? ($registros[$current->parent] ?? null)
                : null;
        }

        return $end;
    }
}
