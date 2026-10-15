<?php

namespace Proceda\Service;

use Proceda\Builder\BuilderInterface;
use Proceda\Builder\RootBuilderInterface;
use Proceda\Builder\SchemaAwareBuilderInterface;
use Proceda\Schema;

final class BuilderRegistry
{
    /** @var array<string, BuilderInterface> */
    private array $builders = [];

    /** @var array<string, BuilderInterface[]> */
    private array $collections = [];

    public function getRootBuilder(): RootBuilderInterface
    {
        $root = array_shift($this->builders);

        if ($root instanceof RootBuilderInterface) {
            return $root;
        }

        throw new \LogicException('Root builder not found.');
    }

    public function get(Schema $schema): BuilderInterface
    {
        if (isset($this->builders[$schema->path])) {
            return $this->builders[$schema->path];
        }

        return $this->builders[$schema->path]
            = $this->createBuilder($schema);
    }

    public function find(Schema $schema): ?BuilderInterface
    {
        return $this->builders[$schema->path] ?? null;
    }

    public function remove(Schema $schema): void
    {
        unset($this->builders[$schema->path]);
    }

    public function prependCollection(
        Schema $schema,
        array $builders,
    ): void {
        $this->collections[$schema->path] ??= [];

        array_unshift(
            $this->collections[$schema->path],
            ...$builders,
        );
    }

    public function findCollection(Schema $schema): array
    {
        return $this->collections[$schema->path] ?? [];
    }

    public function removeCollection(Schema $schema): void
    {
        unset($this->collections[$schema->path]);
    }

    private function createBuilder(Schema $schema): BuilderInterface
    {
        $builderClass = sprintf(
            'Proceda\\Builder\\%sBuilder',
            substr($schema->class, strlen('Proceda\\')),
        );

        $builder = new $builderClass();

        if ($builder instanceof SchemaAwareBuilderInterface) {
            $builder->configureFromSchema($schema);
        }

        return $builder;
    }
}
