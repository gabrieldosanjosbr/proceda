<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Layout;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Infrastructure\Serializer\LayoutNormalizer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class LayoutNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeLayout(): void
    {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $normalizer = new LayoutNormalizer();

        self::assertSame(
            [
                'type' => $layout->type->value,
                'version' => $layout->version->value,
                'profile' => null,
            ],
            $normalizer->normalize($layout),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeInvalidObjectThrowsException(): void
    {
        $normalizer = new LayoutNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected instance of ' . Layout::class,
        );

        $normalizer->normalize(new \stdClass());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testDenormalizeLayout(): void
    {
        $normalizer = new LayoutNormalizer();

        $layout = $normalizer->denormalize(
            [
                'type' => LayoutType::NOTFIS->value,
                'version' => LayoutVersion::V50->value,
                'profile' => null,
            ],
            Layout::class,
        );

        self::assertSame(LayoutType::NOTFIS, $layout->type);
        self::assertSame(LayoutVersion::V50, $layout->version);
        self::assertNull($layout->profile);
    }

    public function testDenormalizeInvalidPayloadThrowsException(): void
    {
        $normalizer = new LayoutNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid Layout payload.',
        );

        $normalizer->denormalize(
            'invalid-payload',
            Layout::class,
        );
    }

    public function testSupportsLayoutType(): void
    {
        $normalizer = new LayoutNormalizer();

        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        self::assertTrue(
            $normalizer->supportsNormalization($layout),
        );

        self::assertFalse(
            $normalizer->supportsNormalization(new \stdClass()),
        );

        self::assertTrue(
            $normalizer->supportsDenormalization(
                [],
                Layout::class,
            ));

        self::assertFalse(
            $normalizer->supportsDenormalization(
                [],
                \stdClass::class,
            ));
    }

    /**
     * @throws ExceptionInterface
     */
    public function testLayoutRoundTrip(): void
    {
        $normalizer = new LayoutNormalizer();

        $original = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $restored = $normalizer->denormalize(
            $normalizer->normalize($original),
            Layout::class,
        );

        self::assertSame($original->type, $restored->type);
        self::assertSame($original->version, $restored->version);
        self::assertSame($original->profile, $restored->profile);
    }
}