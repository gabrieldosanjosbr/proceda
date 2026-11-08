<?php

namespace Proceda\Tests;

use PHPUnit\Framework\TestCase;
use Proceda\Line;
use Proceda\Proceda;
use Proceda\Reader;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Proceda\Service\BuilderRegistry;
use Random\RandomException;

class ReaderTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function testReadWithoutContextThrowsException(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $registro = new Registro(
            identificacao: '999',
            length: 280,
            build: true,
        );

        $reader = new Reader(
            schemaManager: $layout->getSchemaManager(),
            builderRegistry: new BuilderRegistry(),
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Schema not found for registro 999',
        );

        $reader->read(
            new Line(
                content: str_pad('999', 280),
                registro: $registro,
            ),
        );
    }
}