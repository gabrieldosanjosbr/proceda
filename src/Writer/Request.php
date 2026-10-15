<?php

namespace Proceda\Writer;

use Proceda\Intercambio;
use Proceda\Layout;

final readonly class Request
{
    public const int VERSION = 1;

    public function __construct(
        public Layout $layout,
        public Intercambio $document,
        public int $version = self::VERSION,
    ) {}
}
