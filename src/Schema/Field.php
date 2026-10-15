<?php

namespace Proceda\Schema;

use Proceda\Charset;
use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Error\Validation;
use Proceda\Line;
use Proceda\Service\TypeResolver;

final readonly class Field
{
    public function __construct(
        public Charset       $charset,
        public LayoutVersion $layoutVersion,
        public Registro      $registro,
        public string        $property,
        public string        $path,
        public ?string       $label = null,
        public bool          $required = false,
        public ?int          $decimals = null,
        public ?string       $type = null,
        public array|null    $allowed = null,
        public array         $excluded = [],
        public int           $length = 0,
        public int           $offset = 0,
        public ?Field        $fallback = null,
    ) {}

    public function getValue(
        Line $line,
        \ReflectionProperty $propertyReflection,
        TypeResolver $typeResolver,
        Validation $validation,
        int $offsetAdjustment = 0,
    ): mixed {
        $value = $this->readValue(
            $line,
            $offsetAdjustment,
        );

        if ($this->type !== null && $value !== '') {
            $typeCollection = $typeResolver->getTypeCollection(
                $this->type,
                $this->allowed,
                $this->excluded,
            );

            if ($typeCollection->name === 'time') {
                if (!preg_match('/^(?:[01]\d|2[0-3])[0-5]\d$/', $value)) {
                    $validation->add(
                        new FieldError(
                            type: FieldErrorType::INVALID_TIME,
                            field: $this,
                            line: $line,
                            value: $value,
                        )
                    );
                }

                return $value;
            }

            $value = is_numeric($value)
                ? (int) $value
                : $value;

            $type = $typeCollection->get($value);

            if ($type !== null) {
                return $type;
            }

            if (
                !$this->required
                && (string) $value === '0'
            ) {
                return null;
            }

            $validation->add(
                new FieldError(
                    type: FieldErrorType::TYPE_NOT_ALLOWED,
                    field: $this,
                    line: $line,
                    value: $value,
                )
            );

            return null;
        }

        $reflectionType = $propertyReflection->getType();

        if (!$reflectionType instanceof \ReflectionNamedType) {
            throw new \LogicException(sprintf(
                'Property %s::$%s must have a named type.',
                $propertyReflection->getDeclaringClass()->getName(),
                $propertyReflection->getName(),
            ));
        }

        $isEmpty = $value === ''
            || trim($value, '0') === '';

        if (
            $this->required
            && $isEmpty
            && $this->registro->required
        ) {
            $validation->add(
                new FieldError(
                    type: FieldErrorType::MISSING_REQUIRED_VALUE,
                    field: $this,
                    line: $line,
                ),
            );
        }

        if ($reflectionType->allowsNull() && $isEmpty) {
            return null;
        }

        $type = $reflectionType->getName();

        if ($type === 'bool') {
            return match ($value) {
                'S' => true,
                'N' => false,
                default => null,
            };
        }

        if ($type === 'int') {
            return (int) $value;
        }

        return $value;
    }

    public function readValue(
        Line $line,
        int $offsetAdjustment = 0,
    ): string|int|float {
        $value = trim(substr(
            $line->content,
            $this->offset - 1 + $offsetAdjustment,
            $this->length,
        ));

        $value = $this->charset->toUtf8($value);

        if ($this->decimals > 0 && $value !== '') {
            return (int) $value / 10 ** $this->decimals;
        }

        return $value;
    }

    public function matchesRegistro(
        Registro $expected,
        Registro $actual,
    ): bool {
        if ($expected->identificacao === $actual->identificacao) {
            return true;
        }

        return $expected->useParentAsContext
            && $actual->useParentAsContext
            && $expected->parent === $actual->parent;
    }
}
