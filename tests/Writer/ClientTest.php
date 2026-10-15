<?php

namespace Proceda\Tests\Writer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Proceda\Error\Validation;
use Proceda\Infrastructure\Serializer\Factory as SerializerFactory;
use Proceda\Intercambio;
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;
use Proceda\Writer\Client;
use Proceda\Writer\Response;
use Proceda\Writer\Transport\WriterTransportInterface;

final class ClientTest extends TestCase
{
    #[DataProvider('writerErrors')]
    public function testWriteThrowsExceptionWhenWriterFails(
        ?string $exception,
        string $expectedMessage,
    ): void {
        $layout = Proceda::notfis(
            layoutVersion: LayoutVersion::V50,
        );

        $response = new Response(
            successful: false,
            content: null,
            encoding: 'base64',
            validation: new Validation(),
            exception: $exception,
            version: 1,
        );

        $serializer = SerializerFactory::create(
            $layout->getSchemaManager(),
        );

        $transport = $this->createMock(
            WriterTransportInterface::class,
        );

        $transport->expects(self::once())
            ->method('send')
            ->willReturn(
                $serializer->serialize($response, 'json'),
            );

        $client = Client::create(
            transport: $transport,
            schemaManager: $layout->getSchemaManager(),
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($expectedMessage);

        $client->write(
            new Intercambio(),
            $layout,
        );
    }

    public static function writerErrors(): array
    {
        return [
            'explicit error' => [
                'Writer execution failed.',
                'Writer execution failed.',
            ],
            'default error' => [
                null,
                'Writer failed.',
            ],
        ];
    }
}