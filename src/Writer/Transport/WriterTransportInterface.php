<?php

namespace Proceda\Writer\Transport;

interface WriterTransportInterface
{
    public function send(string $payload): string;
}
