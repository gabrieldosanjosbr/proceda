<?php

namespace Proceda\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proceda\Charset;

final class CharsetTest extends TestCase
{
    #[DataProvider('invalidTabSizes')]
    public function testInvalidTabReplacementSize(int $size): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Tab replacement size must be greater than zero.',
        );

        new Charset(tabReplacementSize: $size);
    }

    public static function invalidTabSizes(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
        ];
    }

    public function testReplaceTabs(): void
    {
        $charset = new Charset(tabReplacementSize: 4);

        self::assertSame(
            'ABC    DEF',
            $charset->toIso("ABC\tDEF"),
        );
    }

    public function testConvertUtf8ToIso(): void
    {
        $charset = new Charset();

        self::assertSame(
            mb_convert_encoding('São Paulo', 'ISO-8859-1', 'UTF-8'),
            $charset->toIso('São Paulo'),
        );
    }

    public function testConvertIsoToUtf8(): void
    {
        $charset = new Charset();

        $iso = mb_convert_encoding(
            'São Paulo',
            'ISO-8859-1',
            'UTF-8',
        );

        self::assertSame(
            'São Paulo',
            $charset->toUtf8($iso),
        );
    }
}