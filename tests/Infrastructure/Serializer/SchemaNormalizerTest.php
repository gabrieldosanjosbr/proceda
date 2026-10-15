<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Documento\Notfis;
use Proceda\Infrastructure\Serializer\SchemaNormalizer;
use Proceda\Proceda;
use Proceda\Schema;
use Proceda\Schema\LayoutVersion;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class SchemaNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeUnknownSchemaThrowsException(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $manager = $layout->getSchemaManager();

        $normalizer = new SchemaNormalizer($manager);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Schema "schema/inexistente" not found.',
        );

        $normalizer->denormalize(
            'schema/inexistente',
            Schema::class,
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeWithoutManagerThrowsException(): void
    {
        $normalizer = new SchemaNormalizer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Manager is required to denormalize Schema.',
        );

        $normalizer->denormalize(
            'documento/notaFiscal',
            Schema::class,
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeSchema(): void
    {
        $schema = new Schema(
            class: Notfis::class,
            path: '/path/to/schema',
        );

        $normalizer = new SchemaNormalizer();

        self::assertTrue(
            $normalizer->supportsNormalization($schema),
        );

        self::assertFalse(
            $normalizer->supportsNormalization(new \stdClass()),
        );

        self::assertSame(
            $schema->path,
            $normalizer->normalize($schema),
        );
    }
}