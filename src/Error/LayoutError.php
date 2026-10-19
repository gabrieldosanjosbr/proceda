<?php

namespace Proceda\Error;

use Proceda\Line;
use Proceda\Schema;

final readonly class LayoutError implements Error
{
    public function __construct(
        public LayoutErrorType  $type,
        public ?Schema          $schema = null,
        public ?Line            $line = null,
        public ?Schema\Registro $registro = null,
        public array            $parameters = [],
    ) {}

    public function withLine(Line $line): self
    {
        return new self(
            type: $this->type,
            schema: $this->schema,
            line: $line,
            parameters: $this->parameters,
        );
    }

    public function message(): string
    {
        return match ($this->type) {
            LayoutErrorType::MISSING_REQUIRED_REGISTRO =>
            sprintf(
                'Registro obrigatório "%s" não encontrado.',
                $this->registro->identificacao,
            ),

            LayoutErrorType::INLINE_LIMIT_EXCEEDED =>
            sprintf(
                'A coleção "%s" excedeu o limite máximo de %d itens na mesma linha.',
                $this->schema->path,
                $this->schema->inline,
            ),

            LayoutErrorType::OCCURS_LIMIT_EXCEEDED =>
            sprintf(
                'A coleção "%s" excedeu o limite máximo de %d ocorrências.',
                $this->schema->path,
                $this->schema->registro->occurs,
            ),
        };
    }
}
