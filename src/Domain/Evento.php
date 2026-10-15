<?php

namespace Proceda;

readonly class Evento
{
    public function __construct(
        protected Type    $tipo,
        protected ?string $data = null,
        protected ?string $hora = null,
    ) {}

    public function getTipo(): Type
    {
        return $this->tipo;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getHora(): ?string
    {
        return $this->hora;
    }
}
