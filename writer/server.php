<?php

declare(strict_types=1);

use Proceda\Infrastructure\Serializer\Factory as SerializerFactory;
use Proceda\Proceda;
use Proceda\Schema\Manager;
use Proceda\Writer\Request;
use Proceda\Writer\Response;
use Proceda\Writer\Writer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

require getenv('PROCEDA_AUTOLOAD_FILE')
    ?: __DIR__ . '/vendor/autoload.php';

$writer = getenv('PROCEDA_WRITER_FILE');

if ($writer === false || $writer === '') {
    $writer = '/opt/proceda/writer.prcd';
}

if (!is_file($writer)) {
    throw new RuntimeException(
        sprintf('PROCEDA Writer file "%s" not found.', $writer),
    );
}

require_once $writer;

$socketPath = getenv('PROCEDA_WRITER_SOCKET')
    ?: '/run/proceda/writer.sock';

if (file_exists($socketPath)) {
    unlink($socketPath);
}

$server = stream_socket_server(
    'unix://' . $socketPath,
    $errno,
    $error,
);

if ($server === false) {
    throw new RuntimeException(
        sprintf(
            'Unable to create Writer socket: %s',
            $error,
        ),
    );
}

$socketGid = getenv('PROCEDA_WRITER_SOCKET_GID');

if ($socketGid !== false && $socketGid !== '') {
    if (!chgrp($socketPath, (int) $socketGid)) {
        throw new RuntimeException(
            sprintf(
                'Unable to set Writer socket group: %s',
                $socketPath,
            ),
        );
    }
}

if (!chmod($socketPath, 0660)) {
    throw new RuntimeException(
        sprintf(
            'Unable to set Writer socket permissions: %s',
            $socketPath,
        ),
    );
}

$serializer = SerializerFactory::create();

while ($connection = stream_socket_accept($server, -1)) {
    $schemaManager = null;

    try {
        $header = readBytes(
            $connection,
            4,
        );

        $length = unpack(
            'Nlength',
            $header,
        )['length'];

        $payload = readBytes(
            $connection,
            $length,
        );

        $request = $serializer->deserialize(
            $payload,
            Request::class,
            'json',
        );

        if (!$request instanceof Request) {
            throw new UnexpectedValueException(
                'Invalid Writer request.',
            );
        }

        if ($request->version !== Request::VERSION) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unsupported Writer protocol version "%d".',
                    $request->version,
                ),
            );
        }

        $layout = Proceda::create(
            layoutType: $request->layout->type,
            layoutVersion: $request->layout->version,
            profile: $request->layout->profile,
        );

        $schemaManager = $layout->getSchemaManager();

        $writer = Writer::create($layout);

        $result = $writer->write(
            $request->document,
        );

        $response = new Response(
            successful: true,
            content: $result->toString(),
            validation: $result->validation,
        );
    } catch (Throwable $e) {
        $response = new Response(
            successful: false,
            exception: $e->getMessage(),
        );

        fwrite(
            STDERR,
            sprintf(
                "%s: %s\n%s\n",
                $e::class,
                $e->getMessage(),
                $e->getTraceAsString(),
            ),
        );
    }

    try {
        sendResponse(
            $connection,
            $response,
            $schemaManager
        );
    } catch (Throwable $e) {
        fwrite(
            STDERR,
            sprintf(
                "Unable to send Writer response: %s: %s\n",
                $e::class,
                $e->getMessage(),
            ),
        );
    } finally {
        fclose($connection);
    }
}


/**
 * @throws ExceptionInterface
 */
function sendResponse(
             $connection,
    Response $response,
    ?Manager $schemaManager = null,
): void {
    $serializer = SerializerFactory::create(
        $schemaManager,
    );

    $payload = $serializer->serialize(
        $response,
        'json',
    );

    $frame = pack(
        'N',
        strlen($payload),
    ) . $payload;

    writeBytes(
        $connection,
        $frame,
    );
}

/**
 * @param resource $stream
 */
function readBytes(
    mixed $stream,
    int $length,
): string {
    $buffer = '';

    while (strlen($buffer) < $length) {
        $chunk = fread(
            $stream,
            $length - strlen($buffer),
        );

        if ($chunk === false || $chunk === '') {
            throw new RuntimeException(
                'Unexpected end of Writer socket.',
            );
        }

        $buffer .= $chunk;
    }

    return $buffer;
}

/**
 * @param resource $stream
 */
function writeBytes(
    mixed $stream,
    string $payload,
): void {
    $offset = 0;
    $length = strlen($payload);

    while ($offset < $length) {
        $written = fwrite(
            $stream,
            substr($payload, $offset),
        );

        if ($written === false || $written === 0) {
            throw new RuntimeException(
                'Failed to write Writer response.',
            );
        }

        $offset += $written;
    }
}