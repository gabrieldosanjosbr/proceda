<?php

namespace Proceda\Writer\Transport;

final readonly class UnixSocketTransport implements WriterTransportInterface
{
    public function __construct(
        private string $socket,
    ) {}

    public function send(string $payload): string
    {
        $stream = stream_socket_client(
            'unix://' . $this->socket,
            $errno,
            $error,
            5,
        );

        if ($stream === false) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to connect to Writer: %s',
                    $error,
                ),
            );
        }

        try {
            $this->write(
                $stream,
                pack('N', strlen($payload)) . $payload,
            );

            $header = $this->read(
                $stream,
                4,
            );

            $length = unpack(
                'Nlength',
                $header,
            )['length'];

            return $this->read(
                $stream,
                $length,
            );
        } finally {
            fclose($stream);
        }
    }

    private function write(
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
                throw new \RuntimeException(
                    'Failed to write to Writer socket.',
                );
            }

            $offset += $written;
        }
    }

    private function read(
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
                throw new \RuntimeException(
                    'Unexpected end of Writer socket.',
                );
            }

            $buffer .= $chunk;
        }

        return $buffer;
    }
}
