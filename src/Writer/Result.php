<?php

namespace Proceda\Writer;

use Proceda\Line;
use Proceda\Error\Validation;

final readonly class Result
{
    public function __construct(
        /** @var list<Line> */
        public array $lines,
        public Validation $validation,
    ) {}

    public function toFile(string $filename): void
    {
        file_put_contents(
            $filename,
            $this->toString(),
        );
    }

    public function toString(): string
    {
        return implode(
            "\r\n",
            array_map(
                static fn(Line $line): string => $line->content,
                $this->lines,
            ),
        ) . "\r\n";
    }

    /** @return list<Line> */
    public function getLines(): array
    {
        return $this->lines;
    }
}
