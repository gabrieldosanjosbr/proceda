<?php

namespace Proceda\Tests\Error;

use PHPUnit\Framework\TestCase;
use Proceda\Error\LayoutError;
use Proceda\Error\LayoutErrorType;
use Proceda\Line;
use Proceda\Schema\Registro;
use Random\RandomException;

class LayoutErrorTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testWithLineOnOccursLimitExceeded(): void
    {
        $error = new LayoutError(
            type: LayoutErrorType::OCCURS_LIMIT_EXCEEDED
        );

        $line = new Line(
            content:'999',
            registro: new Registro(
                identificacao: '999',
                length: 200
            )
        );

        $updated = $error->withLine($line);

        self::assertSame($line, $updated->line);
    }
}
