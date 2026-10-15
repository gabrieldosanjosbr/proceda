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

    private Validation $validation;

    /** @var Line[] */
    private array $lines = [];

    public function __construct(
        private readonly Manager         $schemaManager,
        private readonly BuilderRegistry $builderRegistry,
        private readonly TypeResolver    $typeResolver,
    ) {
        $this->validation = new Validation();
    }

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
            count($collection) > $schema->occurs
        ) {
            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED,
                    schema: $schema,
                    registro: $schema->registro,
                    parameters: [
                        'maximum' => $schema->occurs,
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

    public function validateMissingRegistros(): void
    {
        $registros = $this->schemaManager
            ->getRegistroResolver()
            ->getRegistros();

        $rootRegistro = reset($registros);

        $hasRoot = array_any(
            $this->lines,
            static fn (Line $line): bool =>
                $line->registro->identificacao
                === $rootRegistro->identificacao,
        );

        if (!$hasRoot) {
            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                    registro: $rootRegistro,
                )
            );
        }

        $lines = array_reverse($this->lines, true);

        $this->validateRequiredParents(
            $registros,
            $lines,
        );

        foreach ($lines as $index => $line) {
            $registro = $line->registro;

            if (
                $registro->identificacao
                === $rootRegistro->identificacao
            ) {
                continue;
            }

            $hasRequiredExtension = array_any(
                $registro->getExtensions(),
                static fn (Registro $extension): bool =>
                $extension->required,
            );

            if (!$hasRequiredExtension) {
                continue;
            }

            $lineBlock = $this->getBlock(
                $index,
                $registro,
            );

            $this->validateRequiredExtensions(
                $lineBlock,
                $registro,
                $line
            );

            $requiredRegistros  = [];
            $found = false;

            foreach ($registros as $current) {
                if (!$found) {
                    if (
                        $current->identificacao
                        !== $registro->identificacao
                    ) {
                        continue;
                    }

                    $found = true;
                }

                if (
                    !$current->required
                    || $current->useParentAsContext
                ) {
                    continue;
                }

                $requiredRegistros[] = $current;

                if (
                    $current !== $registro
                    && array_any(
                        $current->getExtensions(),
                        static fn (Registro $extension): bool =>
                        $extension->required,
                    )
                ) {
                    break;
                }
            }

            foreach ($requiredRegistros as $requiredRegistro) {
                $found = array_any(
                    $lineBlock,
                    static fn (Line $line): bool =>
                        $line->registro->identificacao
                        === $requiredRegistro->identificacao,
                );

                if ($found) {
                    continue;
                }

                $this->validation->add(
                    new LayoutError(
                        type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                        line: $line,
                        registro: $requiredRegistro,
                    )
                );
            }
        }
    }

    private function getBlock(
        int $start,
        Registro $registro,
    ): array {
        $block = [];

        for ($index = $start; $index >= 0; $index--) {
            $line = $this->lines[$index];

            if (
                $index !== $start
                && $line->registro->identificacao
                === $registro->identificacao
            ) {
                break;
            }

            $block[$index] = $line;
        }

        return $block;
    }

    /**
     * @param Registro[] $registros
     * @param Line[] $lines
     */
    private function validateRequiredParents(
        array $registros,
        array $lines,
    ): void {
        foreach ($lines as $index => $line) {
            $extension = $line->registro;

            if (!$extension->useParentAsContext) {
                continue;
            }

            $parent = array_find(
                $registros,
                static fn (Registro $registro): bool =>
                    $registro->identificacao
                    === $extension->parent,
            );

            if (
                $parent === null
                || !$parent->required
                || !$this->isBlockAnchor($extension, $parent)
            ) {
                continue;
            }

            if ($this->hasExtendedParent($index, $extension)) {
                continue;
            }

            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                    line: $line,
                    registro: $parent,
                )
            );
        }
    }

    private function hasExtendedParent(
        int $index,
        Registro $extension,
    ): bool {
        for (
            $current = $index + 1;
            isset($this->lines[$current]);
            $current++
        ) {
            $registro = $this->lines[$current]->registro;

            if (
                $registro->identificacao
                === $extension->parent
            ) {
                return true;
            }

            if (
                $registro->useParentAsContext
                && $registro->parent === $extension->parent
            ) {
                return false;
            }
        }

        return false;
    }

    private function isBlockAnchor(
        Registro $extension,
        Registro $parent,
    ): bool {
        foreach ($parent->getExtensions() as $candidate) {
            if (!$candidate->required) {
                continue;
            }

            return $candidate->identificacao
                === $extension->identificacao;
        }

        return false;
    }

    private function validateRequiredExtensions(
        array $lineBlock,
        Registro $registro,
        Line $line
    ): void {
        foreach ($registro->getExtensions() as $extension) {
            if (!$extension->required) {
                continue;
            }

            $found = array_any(
                $lineBlock,
                static fn (Line $line): bool =>
                    $line->registro->identificacao
                    === $extension->identificacao,
            );

            if ($found) {
                continue;
            }

            $this->validation->add(
                new LayoutError(
                    type: LayoutErrorType::MISSING_REQUIRED_REGISTRO,
                    line: $line,
                    registro: $extension,
                )
            );
        }
    }
}
