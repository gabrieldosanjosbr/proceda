<?php

namespace Proceda\Tests\Infrastructure;

use PHPUnit\Framework\TestCase;
use Proceda\Infrastructure\YamlSource;

final class YamlSourceTest extends TestCase
{
    public function testFromPathWithNonexistentPathThrowsException(): void
    {
        $path = sys_get_temp_dir()
            . '/proceda-nonexistent-'
            . bin2hex(random_bytes(8));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            sprintf('YAML source "%s" does not exist.', $path),
        );

        YamlSource::fromPath($path);
    }
}