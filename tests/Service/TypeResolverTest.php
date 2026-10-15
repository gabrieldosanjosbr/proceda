<?php

namespace Proceda\Tests\Service;

use PHPUnit\Framework\TestCase;
use Proceda\Service\TypeResolver;

final class TypeResolverTest extends TestCase
{
    public function testResolveUnknownTypeThrowsException(): void
    {
        $resolver = TypeResolver::fromDefinitions([
            [
                'status' => [
                    [
                        'name' => 'Pendente',
                        'value' => '01',
                        'label' => 'Pendente',
                    ],
                ],
            ],
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Type "status/Inexistente" not found.',
        );

        $resolver->resolve('status/Inexistente');
    }

    public function testResolveInvalidPathThrowsException(): void
    {
        $resolver = TypeResolver::fromDefinitions([]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Invalid type path "status".',
        );

        $resolver->resolve('status');
    }
}