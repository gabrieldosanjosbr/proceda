<?php

namespace Proceda\Tests;

use PHPUnit\Framework\TestCase;
use Proceda\Line;
use Proceda\Schema\Registro;
use Random\RandomException;

final class LineTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testIsEmpty(): void
    {
        $registro = new Registro(
            identificacao: '500',
            length: 280,
        );

        self::assertTrue(
            (new Line(
                content: '   ',
                registro: $registro,
            ))->isEmpty(),
        );

        self::assertTrue(
            (new Line(
                content: '',
                registro: $registro,
            ))->isEmpty(),
        );

        self::assertFalse(
            (new Line(
                content: '500TESTE',
                registro: $registro,
            ))->isEmpty(),
        );
    }

    /**
     * @throws RandomException
     */
    public function testWithContent(): void
    {
        $registro = new Registro(
            identificacao: '500',
            length: 280,
        );

        $original = new Line(
            content: '500ORIGINAL',
            registro: $registro,
            number: 42,
        );

        $updated = $original->withContent('500ALTERADO');

        self::assertNotSame($original, $updated);

        self::assertSame('500ORIGINAL', $original->content);
        self::assertSame('500ALTERADO', $updated->content);

        self::assertSame($original->id, $updated->id);
        self::assertSame($original->registro, $updated->registro);
        self::assertSame($original->number, $updated->number);
    }

    /**
     * @throws RandomException
     */
    public function testWithNumber(): void
    {
        $registro = new Registro(
            identificacao: '500',
            length: 280,
        );

        $original = new Line(
            content: '500TESTE',
            registro: $registro,
            number: 1,
        );

        $updated = $original->withNumber(42);

        self::assertNotSame($original, $updated);

        self::assertSame(1, $original->number);
        self::assertSame(42, $updated->number);

        self::assertSame($original->id, $updated->id);
        self::assertSame($original->content, $updated->content);
        self::assertSame($original->registro, $updated->registro);
    }

    /**
     * @throws RandomException
     */
    public function testWithMethodsPreserveIdentity(): void
    {
        $original = new Line(
            content: '500ORIGINAL',
            registro: new Registro(
                identificacao: '500',
                length: 280,
            ),
        );

        $updated = $original
            ->withContent('500ALTERADO')
            ->withNumber(10);

        self::assertSame($original->id, $updated->id);
        self::assertSame($original->registro, $updated->registro);
        self::assertSame('500ALTERADO', $updated->content);
        self::assertSame(10, $updated->number);

        self::assertNull($original->number);
        self::assertSame('500ORIGINAL', $original->content);
    }
}