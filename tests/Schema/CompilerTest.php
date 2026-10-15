<?php

namespace Proceda\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Proceda\Intercambio;
use Proceda\Schema\Compiler;

final class CompilerTest extends TestCase
{
    public function testGetDefinitionNotFoundThrowsException(): void
    {
        $compiler = new Compiler();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Definition "inexistente" not found.',
        );

        $compiler->getDefinition('inexistente');
    }

    public function testInvalidAllowedRangeThrowsException(): void
    {
        $compiler = new Compiler();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Invalid value for field allowed.',
        );

        $compiler->compile([
            [
                'class' => Intercambio::class,
                'registro' => '000',
                'campoTeste' => [
                    'length' => 2,
                    'allowed' => 'valor-invalido',
                ],
            ],
        ]);
    }

    public function testCompileAllowedRange(): void
    {
        $compiler = new Compiler();

        $definition = $compiler->compile([
            [
                'class' => Intercambio::class,
                'registro' => '000',
                'campoTeste' => [
                    'length' => 2,
                    'allowed' => '1..5',
                ],
            ],
        ]);

        self::assertSame(
            [1, 2, 3, 4, 5],
            $definition['fields']['campoTeste']['allowed'],
        );
    }

    public function testGetExistingDefinition(): void
    {
        $compiler = new Compiler();

        $compiler->compile([
            [
                'class' => Intercambio::class,
                'registro' => '000',
                'campoTeste' => [
                    'length' => 2,
                ],
            ],
        ]);

        $definition = $compiler->getDefinition('intercambio');

        self::assertSame(
            Intercambio::class,
            $definition['context']['class'],
        );

        self::assertArrayHasKey(
            'campoTeste',
            $definition['fields'],
        );
    }
}