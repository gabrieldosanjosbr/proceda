<?php

namespace Proceda\Tests\Infrastructure\Serializer;

use PHPUnit\Framework\TestCase;
use Proceda\Error\Validation;
use Proceda\Infrastructure\Serializer\Factory;
use Proceda\Writer\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class ResponseNormalizerTest extends TestCase
{
    public function testNormalizeResponse(): void
    {
        $response = new Response(
            successful: true,
            content: "EDI\r\nCONTEÚDO",
            encoding: 'base64',
            validation: new Validation(),
            exception: null,
            version: 1,
        );

        $serializer = Factory::create();

        $data = $serializer->normalize($response);

        self::assertSame(true, $data['successful']);
        self::assertSame(
            base64_encode("EDI\r\nCONTEÚDO"),
            $data['content'],
        );
        self::assertSame('base64', $data['encoding']);
        self::assertSame([], $data['validation']);
        self::assertNull($data['exception']);
        self::assertSame(1, $data['version']);

        self::assertTrue(
            $serializer->supportsNormalization($response),
        );
    }

    public function testNormalizeResponseWithoutContent(): void
    {
        $response = new Response(
            successful: false,
            content: null,
            encoding: 'base64',
            validation: new Validation(),
            exception: 'Writer failed',
            version: 1,
        );

        $data = Factory::create()->normalize($response);

        self::assertNull($data['content']);
        self::assertSame('Writer failed', $data['exception']);
    }

    public function testDenormalizeInvalidBase64ThrowsException(): void
    {
        $serializer = Factory::create();

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Invalid Response content.',
        );

        $serializer->denormalize(
            [
                'successful' => true,
                'content' => '%%%INVALID_BASE64%%%',
                'encoding' => 'base64',
                'validation' => [],
                'exception' => null,
                'version' => 1,
            ],
            Response::class,
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testResponseRoundTrip(): void
    {
        $serializer = Factory::create();

        $original = new Response(
            successful: true,
            content: "500TESTE\r\n506DADOS\r\n",
            encoding: 'base64',
            validation: new Validation(),
            exception: null,
            version: 1,
        );

        $json = $serializer->serialize($original, 'json');

        $restored = $serializer->deserialize(
            $json,
            Response::class,
            'json',
        );

        self::assertInstanceOf(Response::class, $restored);

        self::assertSame(
            $original->content,
            $restored->content,
        );

        self::assertSame(
            $original->successful,
            $restored->successful,
        );

        self::assertCount(0, $restored->validation);
    }
}