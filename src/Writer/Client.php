<?php

namespace Proceda\Writer;

use Proceda\Infrastructure\Serializer\Factory as SerializerFactory;
use Proceda\Intercambio;
use Proceda\Layout;
use Proceda\Schema\Manager;
use Proceda\Writer\Transport\WriterTransportInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class Client
{
    public function __construct(
        private WriterTransportInterface $transport,
        private SerializerInterface $serializer,
    ) {}

    public static function create(
        WriterTransportInterface $transport,
        Manager $schemaManager
    ): self {
        return new self(
            transport: $transport,
            serializer: SerializerFactory::create(
                $schemaManager
            ),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function write(
        Intercambio $document,
        Layout $layout,
    ): Response {
        $request = new Request(
            layout: $layout,
            document: $document,
        );

        $payload = $this->serializer->serialize(
            $request,
            'json',
        );

        $serializer = SerializerFactory::create(
            $layout->getSchemaManager(),
        );

        $transportPayload = $this->transport->send(
            $payload,
        );

        /** @var Response $response */
        $response = $serializer->deserialize(
            $transportPayload,
            Response::class,
            'json',
        );

        if (!$response->successful()) {
            throw new \RuntimeException(
                $response->exception ?? 'Writer failed.',
            );
        }

        return $response;
    }
}