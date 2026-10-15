<?php

namespace Proceda;

use Proceda\Schema\Field;
use Proceda\Schema\Registro;

final readonly class Schema
{
    public function __construct(
        public string    $class,
        public string    $path,
        public ?string   $label = null,
        public ?string   $property = null,
        public bool      $collection = false,
        public ?Registro $registro = null,
        public bool      $required = false,
        public int       $occurs = 1,
        public int       $inline = 1,
        public bool      $overflow = false,
        public int       $span = 0,
        public ?Type     $type = null,
        /** @var Field[] */
        public array     $fields = [],

        /** @var Schema[] */
        public array     $children = [],
    ) {}

    public function with(
        array $fields,
        array $children = [],
    ): self {
        return new self(
            class: $this->class,
            path: $this->path,
            label: $this->label,
            property: $this->property,
            collection: $this->collection,
            registro: $this->registro,
            required: $this->required,
            occurs: $this->occurs,
            inline: $this->inline,
            overflow: $this->overflow,
            span: $this->span,
            type: $this->type,
            fields: $fields,
            children: $children,
        );
    }

    public function isCollection(): bool
    {
        return $this->collection;
    }

    public function isInline(): bool
    {
        return $this->inline > 1;
    }
}
