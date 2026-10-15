<?php

namespace Proceda;

readonly class TextoLivre
{
    public function __construct(
        protected ?string $texto = null,
    ) {}

    public function getTexto(): ?string
    {
        return $this->texto;
    }
}
