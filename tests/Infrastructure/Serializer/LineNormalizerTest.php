<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Infrastructure\Serializer\LineNormalizer;
use Proceda\Line;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Proceda\Schema\Registro;
use Random\RandomException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class LineNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     * @throws RandomException
     */
    public function testNormalizeLine(): void
    {
        $registro = new Registro(
            identificacao: '500',
            length: 280,
        );

        $line = new Line(
            content: "500TESTE\r\nDADOS",
            registro: $registro,
            number: 42,
        );

        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $normalizer = new LineNormalizer(
            $layout->getRegistroResolver()
        );

        $data = $normalizer->normalize($line);

        self::assertSame($line->id, $data['id']);
        self::assertSame(
            base64_encode($line->content),
            $data['content'],
        );
        self::assertSame('500', $data['registro']);
        self::assertSame(42, $data['number']);

        self::assertTrue(
            $normalizer->supportsNormalization($line),
        );

        self::assertFalse(
            $normalizer->supportsNormalization(new \stdClass()),
        );
    }

    /**
     * @throws RandomException
     */
    public function testDenormalizeInvalidBase64ThrowsException(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $normalizer = new LineNormalizer(
            $layout->getRegistroResolver()
        );

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid Line content.');

        $normalizer->denormalize(
            [
                'id' => 'test-id',
                'content' => '%%%INVALID_BASE64%%%',
                'registro' => '500',
                'number' => 42,
            ],
            Line::class,
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws RandomException
     */
    public function testLineRoundTrip(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $resolver = $layout->getRegistroResolver();
        $normalizer = new LineNormalizer($resolver);

        $original = new Line(
            content: "500TESTE\x00\xFF\r\n",
            registro: $resolver->get('500'),
            number: 42,
        );

        $normalized = $normalizer->normalize($original);

        $restored = $normalizer->denormalize(
            $normalized,
            Line::class,
        );

        self::assertSame($original->id, $restored->id);
        self::assertSame($original->content, $restored->content);
        self::assertSame($original->registro, $restored->registro);
        self::assertSame($original->number, $restored->number);
    }
}