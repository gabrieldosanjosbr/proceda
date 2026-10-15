<?php

namespace Proceda\Tests\Service;

use PHPUnit\Framework\TestCase;
use Proceda\Service\BuilderRegistry;

final class BuilderRegistryTest extends TestCase
{
    public function testGetRootBuilderWithoutRootThrowsException(): void
    {
        $registry = new BuilderRegistry();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Root builder not found.',
        );

        $registry->getRootBuilder();
    }
}