<?php

namespace Proceda\Tests\Error;

use PHPUnit\Framework\TestCase;
use Proceda\Charset;
use Proceda\Error\FieldError;
use Proceda\Error\FieldErrorType;
use Proceda\Line;
use Proceda\Schema\Field;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Random\RandomException;

final class FieldErrorTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testWithLinePreservesErrorData(): void
    {
        $field = new Field(
            charset: new Charset(),
            layoutVersion: LayoutVersion::V50,
            registro: new Registro(
                identificacao: '999',
                length: 200
            ),
            property: 'hora',
            path: 'testando/error'
        );

        $line = new Line(
            content: '999',
            registro: new Registro(
                identificacao: '999',
                length: 200,
            ),
        );

        $error = new FieldError(
            type: FieldErrorType::INVALID_TIME,
            field: $field,
            value: '5978',
            parameters: ['example' => 'preserved'],
        );

        $updated = $error->withLine($line);

        self::assertNotSame($error, $updated);
        self::assertNull($error->line);
        self::assertSame($line, $updated->line);
        self::assertSame($error->type, $updated->type);
        self::assertSame($field, $updated->field);
        self::assertSame('5978', $updated->value);
        self::assertSame($error->parameters, $updated->parameters);
    }
}