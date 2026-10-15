<?php

namespace Proceda\Tests\Schema;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proceda\Charset;
use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Error\Validation;
use Proceda\Line;
use Proceda\Schema\Field;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Proceda\Service\TypeResolver;
use Proceda\Type;
use Random\RandomException;

final class FieldTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    #[DataProvider('optionalTypeValues')]
    public function testOptionalTypeResolution(
        string $type,
        string $value,
        ?string $expectedName,
        bool $expectError,
    ): void {
        $registro = new Registro(
            identificacao: '313',
            length: 280,
        );

        $field = new Field(
            charset: new Charset(),
            layoutVersion: LayoutVersion::V31,
            registro: $registro,
            property: 'value',
            path: 'nota-fiscal',
            required: false,
            type: $type,
            length: 1,
            offset: 1,
        );

        $object = new class {
            public ?Type $value = null;
        };

        $validation = new Validation();

        $result = $field->getValue(
            line: new Line(
                content: $value,
                registro: $registro,
            ),
            propertyReflection: new \ReflectionProperty(
                $object,
                'value',
            ),
            typeResolver: TypeResolver::fromDefinitions([
                [
                    'NotaFiscal/DocumentoFiscal' => [
                        [
                            'name' => 'Entrada',
                            'value' => '0',
                            'label' => 'Entrada',
                        ],
                        [
                            'name' => 'Saida',
                            'value' => '1',
                            'label' => 'Saída',
                        ],
                    ],
                    'NotaFiscal/MeioTransporte' => [
                        [
                            'name' => 'Rodoviario',
                            'value' => '1',
                            'label' => 'Rodoviário',
                        ],
                    ],
                ],
            ]),
            validation: $validation,
        );

        if ($expectedName !== null) {
            self::assertInstanceOf(Type::class, $result);
            self::assertSame($expectedName, $result->name);
        } else {
            self::assertNull($result);
        }

        self::assertSame(
            $expectError,
            $validation->hasErrors(),
        );

        if (!$expectError) {
            self::assertCount(0, $validation);
            return;
        }

        self::assertCount(1, $validation);

        $error = $validation->errors[0];

        self::assertInstanceOf(
            FieldError::class,
            $error,
        );

        self::assertSame(
            FieldErrorType::TYPE_NOT_ALLOWED,
            $error->type,
        );

        self::assertSame(
            $value,
            (string) $error->value,
        );
    }

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function testGetValueWithoutNamedTypeThrowsException(): void
    {
        $object = new class {
            public $value;
        };

        $registro = new Registro(
            identificacao: '123',
            length: 200,
        );

        $field = new Field(
            charset: new Charset(),
            layoutVersion: LayoutVersion::V50,
            registro: $registro,
            property: 'value',
            path: 'test/value',
            length: 5,
            offset: 1,
        );

        $line = new Line(
            content: 'TESTE',
            registro: $registro,
        );

        $reflection = new \ReflectionProperty(
            $object,
            'value',
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Property %s::$value must have a named type.',
                $object::class,
            ),
        );

        $field->getValue(
            line: $line,
            propertyReflection: $reflection,
            typeResolver: TypeResolver::fromDefinitions([]),
            validation: new Validation(),
        );
    }

    public static function optionalTypeValues(): array
    {
        return [
            'zero cadastrado' => [
                'NotaFiscal/DocumentoFiscal',
                '0',
                'Entrada',
                false,
            ],
            'zero não cadastrado e opcional' => [
                'NotaFiscal/MeioTransporte',
                '0',
                null,
                false,
            ],
            'valor cadastrado' => [
                'NotaFiscal/MeioTransporte',
                '1',
                'Rodoviario',
                false,
            ],
            'valor inválido' => [
                'NotaFiscal/MeioTransporte',
                '9',
                null,
                true,
            ],
        ];
    }
}