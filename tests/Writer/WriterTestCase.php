<?php

namespace Proceda\Tests\Writer;

use PHPUnit\Framework\TestCase;
use Proceda\Infrastructure\Serializer\Factory as SerializerFactory;
use Proceda\Intercambio;
use Proceda\Layout;
use Proceda\Writer\Client;
use Proceda\Writer\Response;
use Proceda\Writer\Transport\UnixSocketTransport;
use Random\RandomException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

abstract class WriterTestCase extends TestCase
{
    /**
     * @throws ExceptionInterface
     * @throws \ReflectionException|RandomException
     */
    protected function assertRoundTrip(
        Intercambio $intercambio,
        Layout $layout,
    ): void {
        $response = $this->write(
            intercambio: $intercambio,
            layout: $layout,
        );

        $read = $layout->readFromString(
            $response->content,
        );

        self::assertFalse(
            $read->validation->hasErrors(),
            'Reader errors not empty',
        );

        $serializer = SerializerFactory::create(
            $layout->getSchemaManager()
        );

        self::assertJsonStringEqualsJsonString(
            $serializer->serialize(
                $intercambio,
                'json',
            ),
            $serializer->serialize(
                $read->intercambio,
                'json',
            ),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    protected function write(
        Intercambio $intercambio,
        Layout $layout,
    ): Response {
        $client = Client::create(
            new UnixSocketTransport(
                getenv('PROCEDA_WRITER_SOCKET')
                    ?: '/run/proceda/writer.sock',
            ),
            $layout->getSchemaManager()
        );

        $response = $client->write(
            document: $intercambio,
            layout: $layout,
        );

        self::assertTrue(
            $response->successful(),
            $response->exception ?? 'Writer failed',
        );

        self::assertNotNull(
            $response->content,
        );

        return $response;
    }
}