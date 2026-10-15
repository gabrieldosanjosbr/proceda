<?php

namespace Proceda\Tests\Writer;

use PHPUnit\Framework\TestCase;
use Proceda\Error\Validation;
use Proceda\Line;
use Proceda\Schema\Registro;
use Proceda\Writer\Result;
use Random\RandomException;

final class ResultTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testGetLines(): void
    {
        $line = new Line(
            content: '500TESTE',
            registro: new Registro(
                identificacao: '500',
                length: 280,
            ),
        );

        $result = new Result(
            lines: [$line],
            validation: new Validation(),
        );

        self::assertSame([$line], $result->getLines());
    }

    /**
     * @throws RandomException
     */
    public function testToString(): void
    {
        $registro = new Registro(
            identificacao: '500',
            length: 280,
        );

        $result = new Result(
            lines: [
                new Line(content: '500PRIMEIRA', registro: $registro),
                new Line(content: '500SEGUNDA', registro: $registro),
            ],
            validation: new Validation(),
        );

        self::assertSame(
            "500PRIMEIRA\r\n500SEGUNDA\r\n",
            $result->toString(),
        );
    }

    public function testToStringWithNoLines(): void
    {
        $result = new Result(
            lines: [],
            validation: new Validation(),
        );

        self::assertSame("\r\n", $result->toString());
    }

    /**
     * @throws RandomException
     */
    public function testToFile(): void
    {
        $result = new Result(
            lines: [
                new Line(
                    content: '500TESTE',
                    registro: new Registro(
                        identificacao: '500',
                        length: 280,
                    ),
                ),
            ],
            validation: new Validation(),
        );

        $filename = tempnam(
            sys_get_temp_dir(),
            'proceda-result-',
        );

        self::assertNotFalse($filename);

        try {
            $result->toFile($filename);

            self::assertSame(
                "500TESTE\r\n",
                file_get_contents($filename),
            );
        } finally {
            @unlink($filename);
        }
    }
}