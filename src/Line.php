<?php

namespace Proceda;

use Proceda\Schema\Registro;
use Random\RandomException;

final readonly class Line
{
    public string $id;

    /**
     * @throws RandomException
     */
    public function __construct(
        public string   $content,
        public Registro $registro,
        public ?int     $number = null,
        ?string         $id = null,
    ) {
        $this->id = $id ?? bin2hex(random_bytes(16));
    }

    public function isEmpty(): bool
    {
        return trim($this->content) === '';
    }

    /**
     * @throws RandomException
     */
    public function withContent(string $content): self
    {
        return new self(
            content: $content,
            registro: $this->registro,
            number: $this->number,
            id: $this->id
        );
    }

    /**
     * @throws RandomException
     */
    public function withNumber(int $number): self
    {
        return new self(
            content: $this->content,
            registro: $this->registro,
            number: $number,
            id: $this->id
        );
    }
}
