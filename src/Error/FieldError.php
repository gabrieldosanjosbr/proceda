<?php

namespace Proceda\Error;

use Proceda\Line;
use Proceda\Schema\Field;

final readonly class FieldError implements Error
{
    public function __construct(
        public FieldErrorType $type,
        public Field          $field,
        public ?Line          $line = null,
        public ?string        $value = null,
        public array          $parameters = [],
    ) {}

    public function withLine(Line $line): self
    {
        return new self(
            type: $this->type,
            field: $this->field,
            line: $line,
            value: $this->value,
            parameters: $this->parameters,
        );
    }

    public function message(): string
    {
        return match ($this->type) {
            FieldErrorType::MISSING_REQUIRED_VALUE =>
            sprintf(
                'Campo "%s" obrigatório não preenchido.',
                $this->field->property,
            ),

            FieldErrorType::TYPE_NOT_ALLOWED =>
            sprintf(
                'Valor "%s" não permitido para o campo "%s". (coleçao: "%s")',
                $this->value,
                $this->field->property,
                $this->field->type,
            ),

            FieldErrorType::VALUE_EXCEEDS_MAXIMUM_LENGTH =>
            sprintf(
                'Valor "%s" do campo "%s" excede o tamanho máximo permitido de %d caracteres.',
                $this->value,
                $this->field->property,
                $this->field->length,
            ),

            FieldErrorType::INVALID_TIME =>
            sprintf(
                'Horário "%s" inválido para o campo "%s". Informe um valor entre 0000 e 2359, com hora e minutos válidos.',
                $this->value,
                $this->field->property,
            ),
        };
    }
}
