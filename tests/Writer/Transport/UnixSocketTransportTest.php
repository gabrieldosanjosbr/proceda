<?php

namespace Proceda\Tests\Writer\Transport;

use PHPUnit\Framework\TestCase;
use Proceda\Writer\Transport\UnixSocketTransport;

final class UnixSocketTransportTest extends TestCase
{
    public function testConnectionFailure(): void
    {
        $socket = sys_get_temp_dir()
            . '/proceda-missing-'
            . bin2hex(random_bytes(8))
            . '.sock';

        $transport = new UnixSocketTransport($socket);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to connect to Writer:',
        );

        // Evita que o aviso de conexão do PHP interfira no PHPUnit.
        @$transport->send('TESTE');
    }
}