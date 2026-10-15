<?php

namespace Proceda\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Proceda\Schema\RegistroResolver;

final class RegistroResolverTest extends TestCase
{
    public function testExtensionWithoutParentThrowsException(): void
    {
        $resolver = [
            [
                [
                    'identificacao' => '333',
                    'length' => 280,
                    'useParentAsContext' => true,
                    'required' => false,
                ],
            ],
        ];

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Registro "333" utiliza o contexto do pai, mas não possui parent definido.',
        );

        RegistroResolver::fromDefinitions($resolver);
    }
}