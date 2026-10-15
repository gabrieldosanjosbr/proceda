<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Charset;
use Proceda\Infrastructure\Serializer\FieldNormalizer;
use Proceda\Proceda;
use Proceda\Schema\Field;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class FieldNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeField(): void
    {
        $field = new Field(
            charset: new Charset(),
            layoutVersion: LayoutVersion::V50,
            registro: new Registro(
                identificacao: '999',
                length: 200,
            ),
            property: 'hora',
            path: 'testando/error',
        );

        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $normalizer = new FieldNormalizer(
            $layout->getSchemaManager()
        );

        self::assertSame(
            [
                'path' => 'testando/error',
                'property' => 'hora',
                'label' => $field->label,
            ],
            $normalizer->normalize($field),
        );

        self::assertTrue(
            $normalizer->supportsNormalization($field),
        );

        self::assertFalse(
            $normalizer->supportsNormalization(new \stdClass()),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testFieldRoundTrip(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $manager = $layout->getSchemaManager();

        $schema = $manager->findByPath('nota-fiscal/frete');

        self::assertNotNull($schema);

        $field = $schema->fields['condicao'];

        $normalizer = new FieldNormalizer($manager);

        $normalized = $normalizer->normalize($field);

        $restored = $normalizer->denormalize(
            $normalized,
            Field::class,
        );

        self::assertSame($field, $restored);
    }
}