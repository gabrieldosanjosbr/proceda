<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Charset;
use Proceda\Error\Error;
use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Error\Validation;
use Proceda\Infrastructure\Serializer\Factory;
use Proceda\Line;
use Proceda\Schema\Field;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class ValidationNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeEmptyValidation(): void
    {
        $serializer = Factory::create();

        $json = $serializer->serialize(
            new Validation(),
            'json',
        );

        self::assertSame('[]', $json);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeEmptyValidation(): void
    {
        $serializer = Factory::create();

        $validation = $serializer->deserialize(
            '[]',
            Validation::class,
            'json',
        );

        self::assertInstanceOf(Validation::class, $validation);
        self::assertCount(0, $validation);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLayoutError(): void
    {
        $serializer = Factory::create();

        $validation = new Validation([
            new LayoutError(
                type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED,
            ),
        ]);

        $data = $serializer->normalize($validation);

        self::assertCount(1, $data);
        self::assertSame('layout', $data[0]['_type']);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeFieldError(): void
    {
        $serializer = Factory::create();

        $field = new Field(
            charset: new Charset(),
            layoutVersion: LayoutVersion::V50,
            registro: new Registro(
                identificacao: '999',
                length: 200,
            ),
            property: 'hora',
            path: 'testando/error',
        );

        $validation = new Validation([
            new FieldError(
                type: FieldErrorType::INVALID_TIME,
                field: $field,
                value: '5978',
            ),
        ]);

        $data = $serializer->normalize($validation);

        self::assertCount(1, $data);
        self::assertSame('field', $data[0]['_type']);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeUnsupportedErrorThrowsException(): void
    {
        $error = new class implements Error {
            public function message(): string
            {
                return 'Unsupported error';
            }

            public function withLine(Line $line): Error
            {
                return $this;
            }
        };

        $serializer = Factory::create();

        $validation = new Validation(
            errors: [$error],
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Unsupported validation error: Proceda\Error\Error@anonymous',
        );

        $serializer->normalize($validation);
    }
}