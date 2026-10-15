<?php

namespace Proceda\Tests;

use PHPUnit\Framework\TestCase;
use Proceda\NotaFiscal;
use Proceda\NotaFiscal\Item;
use Proceda\Schema\Compiler;

final class SchemaCompilerTest extends TestCase
{
    public function testCompileSchemaMetadata(): void
    {
        $compiler = new Compiler();
        $compiler->compile([
            [
                'path' => 'nota-fiscal',
                'class' => NotaFiscal::class,
            ],
            [
                'path' => 'nota-fiscal/item',
                'class' => Item::class,
                'registro' => '314',
                'occurs' => 5000,
                'overflow' => true,

                'quantidade' => [
                    'length' => 7,
                    'offset' => 4,
                    'required' => true,
                    'decimals' => 2,
                ],
            ],
        ]);

        $definition = $compiler->getDefinition(
            'nota-fiscal/item',
        );

        self::assertSame(
            5000,
            $definition['context']['occurs'],
        );

        self::assertTrue(
            $definition['context']['overflow'],
        );
    }
}
