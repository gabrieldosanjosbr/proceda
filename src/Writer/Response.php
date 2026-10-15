<?php

namespace Proceda\Writer;

use Proceda\Error\Validation;

final readonly class Response
{
    public function __construct(
        public bool    $successful,
        public ?string $content = null,
        public string  $encoding = 'base64',
        public Validation $validation = new Validation(),
        public ?string $exception = null,
        public int     $version = Request::VERSION,
    ) {}

    public function successful(): bool
    {
        return $this->successful;
    }
}
